<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::query()
            ->latest()
            ->paginate(10)
            ->through(fn (Donation $donation) => [
                'donor' => $donation->donor_name,
                'email' => $donation->email,
                'project' => $donation->campaign_project ?: 'General Donation',
                'amount' => '$' . number_format($donation->amount, 2),
                'date' => $donation->created_at->format('M d, Y'),
                'status' => Str::headline($donation->payment_status),
                'initials' => $this->initials($donation->donor_name),
            ]);

        return view('admin.donations', compact('donations'));
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
