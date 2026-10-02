<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin → Submissions: job applications, contact messages, donations and volunteer
 * applications sent from the website forms.
 */
class SubmissionController extends Controller
{
    public function index(Request $request, string $type)
    {
        $query = $this->filteredQuery($request, $type);

        if ($request->input('export') === 'csv') {
            return $this->exportCsv($type, (clone $query)->latest()->get());
        }

        $perPage = in_array((int) $request->input('per_page', 25), [25, 50, 100], true) ? (int) $request->input('per_page', 25) : 25;
        $submissions = (clone $query)->latest()->paginate($perPage);

        $statusCounts = FormSubmission::ofType($type)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.submissions.index', [
            'type' => $type,
            'submissions' => $submissions,
            'perPage' => $perPage,
            'statuses' => FormSubmission::statusesFor($type),
            'statusCounts' => $statusCounts,
            'totalAmount' => $type === 'donate' ? (float) (clone $query)->sum('amount') : null,
        ]);
    }

    public function show(string $type, FormSubmission $submission)
    {
        $this->ensureType($type, $submission);

        return view('admin.submissions.show', [
            'type' => $type,
            'submission' => $submission,
            'statuses' => FormSubmission::statusesFor($type),
        ]);
    }

    public function update(Request $request, string $type, FormSubmission $submission)
    {
        $this->ensureType($type, $submission);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(FormSubmission::statusesFor($type)))],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);
        $submission->update($validated);

        return redirect()
            ->route('admin.submissions.show', [$type, $submission])
            ->with('success', FormSubmission::typeLabel($type, false) . ' updated.');
    }

    public function destroy(string $type, FormSubmission $submission)
    {
        $this->ensureType($type, $submission);

        if ($submission->attachment_path) {
            Storage::disk('local')->delete($submission->attachment_path);
        }
        $submission->delete();

        return redirect()
            ->route('admin.submissions.index', $type)
            ->with('success', FormSubmission::typeLabel($type, false) . ' deleted.');
    }

    public function attachment(string $type, FormSubmission $submission)
    {
        $this->ensureType($type, $submission);

        if (!$submission->attachment_path || !Storage::disk('local')->exists($submission->attachment_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($submission->attachment_path, $submission->attachment_name ?: 'attachment.pdf');
    }

    private function filteredQuery(Request $request, string $type)
    {
        $query = FormSubmission::ofType($type);

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhere('subject', 'like', $search)
                    ->orWhere('message', 'like', $search);
            });
        }

        if ($request->filled('status') && array_key_exists($request->input('status'), FormSubmission::statusesFor($type))) {
            $query->where('status', $request->input('status'));
        }

        foreach (['from' => '>=', 'to' => '<='] as $field => $operator) {
            if ($request->filled($field)) {
                try {
                    $date = Carbon::parse($request->input($field));
                    $query->where('created_at', $operator, $field === 'from' ? $date->startOfDay() : $date->endOfDay());
                } catch (\Throwable) {
                    // Ignore an invalid date filter.
                }
            }
        }

        return $query;
    }

    private function exportCsv(string $type, $rows): StreamedResponse
    {
        $subjectLabel = FormSubmission::TYPES[$type][2];
        $filename = str_replace(' ', '-', strtolower(FormSubmission::typeLabel($type))) . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows, $type, $subjectLabel) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Khmer text correctly
            $header = ['Date', 'Status', 'Name', 'Email', 'Phone', $subjectLabel, 'Message', 'Admin note'];
            if ($type === 'donate') {
                array_splice($header, 5, 0, ['Amount (USD)']);
            }
            fputcsv($out, $header);
            foreach ($rows as $row) {
                $line = [
                    $row->created_at?->format('Y-m-d H:i'), $row->statusLabel(), $row->name, $row->email,
                    $row->phone, $row->subject, $row->message, $row->admin_note,
                ];
                if ($type === 'donate') {
                    array_splice($line, 5, 0, [(string) $row->amount]);
                }
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function ensureType(string $type, FormSubmission $submission): void
    {
        abort_unless($submission->type === $type, 404);
    }
}
