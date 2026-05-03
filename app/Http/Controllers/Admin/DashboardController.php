<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Donation;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $postCount = Post::count();
        $donationSum = Donation::where('payment_status', 'completed')->sum('amount');
        $categoryCount = Category::count();

        $stats = [
            'total_users' => [
                'value' => number_format($userCount),
                'change' => $this->monthlyChangeLabel(User::query()),
                'up' => $this->monthlyChangeDirection(User::query())
            ],
            'total_posts' => [
                'value' => number_format($postCount),
                'change' => $this->monthlyChangeLabel(Post::query()),
                'up' => $this->monthlyChangeDirection(Post::query())
            ],
            'total_donations' => [
                'value' => '$' . number_format($donationSum, 2),
                'change' => $this->monthlyDonationChangeLabel(),
                'up' => $this->monthlyDonationChangeDirection()
            ],
            'active_categories' => [
                'value' => number_format($categoryCount),
                'change' => $this->monthlyChangeLabel(Category::query()),
                'up' => null
            ]
        ];

        $recent_posts = Post::with(['user', 'category'])->latest()->take(4)->get()->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'author' => $post->user->name ?? 'Admin',
                'category' => $post->category->name ?? 'Uncategorized',
                'status' => ucfirst($post->status),
                'date' => $post->created_at->format('M d, Y')
            ];
        });

        $top_donors = Donation::query()
            ->select('donor_name', 'email', DB::raw('COUNT(*) as donation_count'), DB::raw('SUM(amount) as total_amount'))
            ->where('payment_status', 'completed')
            ->groupBy('donor_name', 'email')
            ->orderByDesc('total_amount')
            ->take(4)
            ->get()
            ->map(fn ($donor) => [
                'name' => $donor->donor_name,
                'donations' => $donor->donation_count . ' ' . Str::plural('Donation', $donor->donation_count),
                'amount' => '$' . number_format($donor->total_amount, 2),
                'initials' => $this->initials($donor->donor_name),
            ]);

        return view('admin.dashboard', compact('stats', 'recent_posts', 'top_donors'));
    }

    private function monthlyChangeLabel($query): string
    {
        $current = (clone $query)->whereBetween('created_at', [now()->startOfMonth(), now()])->count();
        $previous = (clone $query)->whereBetween('created_at', [
            now()->subMonthNoOverflow()->startOfMonth(),
            now()->subMonthNoOverflow()->endOfMonth(),
        ])->count();

        if ($previous === 0) {
            return $current === 0 ? 'No change' : "+{$current} this month";
        }

        $change = (($current - $previous) / $previous) * 100;

        return ($change >= 0 ? '+' : '') . number_format($change, 1) . '%';
    }

    private function monthlyChangeDirection($query): ?bool
    {
        $current = (clone $query)->whereBetween('created_at', [now()->startOfMonth(), now()])->count();
        $previous = (clone $query)->whereBetween('created_at', [
            now()->subMonthNoOverflow()->startOfMonth(),
            now()->subMonthNoOverflow()->endOfMonth(),
        ])->count();

        return $current === $previous ? null : $current > $previous;
    }

    private function monthlyDonationChangeLabel(): string
    {
        $current = Donation::where('payment_status', 'completed')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('amount');
        $previous = Donation::where('payment_status', 'completed')->whereBetween('created_at', [
            now()->subMonthNoOverflow()->startOfMonth(),
            now()->subMonthNoOverflow()->endOfMonth(),
        ])->sum('amount');

        if ((float) $previous === 0.0) {
            return (float) $current === 0.0 ? 'No change' : '+$' . number_format($current, 2) . ' this month';
        }

        $change = (($current - $previous) / $previous) * 100;

        return ($change >= 0 ? '+' : '') . number_format($change, 1) . '%';
    }

    private function monthlyDonationChangeDirection(): ?bool
    {
        $current = Donation::where('payment_status', 'completed')->whereBetween('created_at', [now()->startOfMonth(), now()])->sum('amount');
        $previous = Donation::where('payment_status', 'completed')->whereBetween('created_at', [
            now()->subMonthNoOverflow()->startOfMonth(),
            now()->subMonthNoOverflow()->endOfMonth(),
        ])->sum('amount');

        return (float) $current === (float) $previous ? null : $current > $previous;
    }

    private function initials(string $name): string
    {
        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->map(fn (string $part) => Str::substr($part, 0, 1))
            ->take(2)
            ->implode('');
    }
}
