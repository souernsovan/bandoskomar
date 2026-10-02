<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /** Pie chart slices: label => [min, max] in USD (inclusive). */
    private const AMOUNT_RANGES = [
        'Up to $10' => [0, 10],
        '$11 – $25' => [10.01, 25],
        '$26 – $50' => [25.01, 50],
        '$51 – $100' => [50.01, 100],
        'Over $100' => [100.01, PHP_FLOAT_MAX],
    ];

    /**
     * Show the admin dashboard with donation charts.
     * Filters: ?period=week|month|year|all, &month=YYYY-MM (month view), &year=YYYY (year view).
     */
    public function index(Request $request)
    {
        $period = in_array($request->input('period'), ['week', 'month', 'year', 'all'], true) ? $request->input('period') : 'month';
        [$start, $end, $buckets, $keyFormat, $rangeLabel] = $this->buckets($period, $request);

        // Cancelled pledges are left out of the totals and charts.
        $donations = FormSubmission::ofType('donate')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get(['id', 'name', 'email', 'amount', 'status', 'created_at']);

        $amounts = array_fill_keys(array_keys($buckets), 0.0);
        $counts = array_fill_keys(array_keys($buckets), 0);
        foreach ($donations as $donation) {
            $key = $donation->created_at->format($keyFormat);
            if (array_key_exists($key, $amounts)) {
                $amounts[$key] += (float) $donation->amount;
                $counts[$key]++;
            }
        }

        $pie = [];
        foreach (self::AMOUNT_RANGES as $label => [$min, $max]) {
            $inRange = $donations->filter(fn ($d) => (float) $d->amount >= $min && (float) $d->amount <= $max);
            $pie[$label] = ['amount' => round($inRange->sum('amount'), 2), 'count' => $inRange->count()];
        }

        $total = (float) $donations->sum('amount');

        return view('admin.dashboard', [
            'period' => $period,
            'rangeLabel' => $rangeLabel,
            'month' => $period === 'month' ? $start->format('Y-m') : now()->format('Y-m'),
            'year' => $period === 'year' ? (int) $start->format('Y') : (int) now()->format('Y'),
            'years' => $this->availableYears(),
            'chart' => [
                'labels' => array_values($buckets),
                'amounts' => array_map(fn ($v) => round($v, 2), array_values($amounts)),
                'counts' => array_values($counts),
                'pieLabels' => array_keys($pie),
                'pieAmounts' => array_column($pie, 'amount'),
                'pieCounts' => array_column($pie, 'count'),
            ],
            'stats' => [
                'total' => $total,
                'count' => $donations->count(),
                'average' => $donations->count() ? $total / $donations->count() : 0,
                'received' => (float) $donations->where('status', 'received')->sum('amount'),
                'allTime' => (float) FormSubmission::ofType('donate')->where('status', '!=', 'cancelled')->sum('amount'),
            ],
            'recentDonations' => $donations->sortByDesc('created_at')->take(8),
            'newCounts' => FormSubmission::where('status', 'new')
                ->selectRaw('type, COUNT(*) as total')->groupBy('type')->pluck('total', 'type'),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: array<string, string>, 3: string, 4: string}
     *               start, end, bucket key => chart label, date format of the keys, readable range
     */
    private function buckets(string $period, Request $request): array
    {
        if ($period === 'week') {
            $start = now()->subDays(6)->startOfDay();
            $end = now()->endOfDay();
            $buckets = [];
            for ($day = $start->copy(); $day <= $end; $day->addDay()) {
                $buckets[$day->format('Y-m-d')] = $day->format('D d M');
            }

            return [$start, $end, $buckets, 'Y-m-d', 'Last 7 days'];
        }

        if ($period === 'year') {
            $year = (int) $request->input('year', now()->year);
            $year = $year >= 2000 && $year <= 2100 ? $year : (int) now()->year;
            $start = Carbon::create($year)->startOfYear();
            $end = $start->copy()->endOfYear();
            $buckets = [];
            for ($m = $start->copy(); $m <= $end; $m->addMonth()) {
                $buckets[$m->format('Y-m')] = $m->format('M');
            }

            return [$start, $end, $buckets, 'Y-m', (string) $year];
        }

        if ($period === 'all') {
            $first = (int) (FormSubmission::ofType('donate')->min('created_at')
                ? Carbon::parse(FormSubmission::ofType('donate')->min('created_at'))->year
                : now()->year);
            $start = Carbon::create($first)->startOfYear();
            $end = now()->endOfYear();
            $buckets = [];
            for ($y = $first; $y <= (int) now()->year; $y++) {
                $buckets[(string) $y] = (string) $y;
            }

            return [$start, $end, $buckets, 'Y', 'All years'];
        }

        try {
            $start = Carbon::createFromFormat('Y-m', (string) $request->input('month', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable) {
            $start = now()->startOfMonth();
        }
        $end = $start->copy()->endOfMonth();
        $buckets = [];
        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $buckets[$day->format('Y-m-d')] = $day->format('j');
        }

        return [$start, $end, $buckets, 'Y-m-d', $start->format('F Y')];
    }

    private function availableYears(): Collection
    {
        $first = FormSubmission::ofType('donate')->min('created_at');
        $from = $first ? Carbon::parse($first)->year : now()->year;

        return collect(range((int) now()->year, $from));
    }
}
