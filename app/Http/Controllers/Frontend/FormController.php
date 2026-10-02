<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\WebsiteFormMail;
use App\Models\FormSubmission;
use App\Support\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Website forms (contact, volunteer, job application, donation): saved in the database
 * (Admin → Submissions) and emailed to the team.
 */
class FormController extends Controller
{
    private const FORMS = [
        'contact' => [
            'page' => 'contact',
            'subject' => 'New contact message',
            'rules' => [
                'subject' => ['nullable', 'string', 'max:255'],
                'message' => ['required', 'string', 'max:5000'],
            ],
        ],
        'volunteer' => [
            'page' => 'volunteer',
            'subject' => 'New volunteer application',
            'rules' => [
                'phone' => ['nullable', 'string', 'max:50'],
                'availability' => ['nullable', 'string', 'max:255'],
                'interest' => ['nullable', 'string', 'max:255'],
                'message' => ['nullable', 'string', 'max:5000'],
            ],
        ],
        'job' => [
            'page' => 'jobs-announcement',
            'subject' => 'New job application',
            'rules' => [
                'phone' => ['nullable', 'string', 'max:50'],
                'position' => ['required', 'string', 'max:255'],
                'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                'message' => ['nullable', 'string', 'max:5000'],
            ],
        ],
        'donate' => [
            'page' => 'donate',
            'subject' => 'New donation pledge',
            'rules' => [
                'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
                'phone' => ['nullable', 'string', 'max:50'],
                'message' => ['nullable', 'string', 'max:5000'],
            ],
        ],
    ];

    private const LABELS = [
        'name' => 'Full name',
        'email' => 'Email',
        'phone' => 'Phone',
        'subject' => 'Subject',
        'position' => 'Position',
        'availability' => 'Availability',
        'interest' => 'Area of interest',
        'amount' => 'Amount (USD)',
        'message' => 'Message',
    ];

    public function submit(Request $request, string $form)
    {
        $definition = self::FORMS[$form] ?? abort(404);
        $site = PageContent::site();

        // Spam bots fill the hidden "website" field; pretend it worked.
        if ($request->filled('website')) {
            return $this->respond($request, $form, true, $this->successMessage($form, $definition, $request));
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ] + $definition['rules']);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $validator->errors()->first()], 422);
            }

            return back()->withErrors($validator)->withInput($request->except('cv'));
        }

        $data = $validator->validated();
        $submission = $this->store($request, $form, $data);

        $fields = [];
        foreach (self::LABELS as $key => $label) {
            if (isset($data[$key]) && $data[$key] !== '') {
                $fields[$label] = (string) $data[$key];
            }
        }

        $recipient = collect([$site->get('form_email'), $site->get('email'), config('mail.from.address')])
            ->first(fn ($email) => filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL));

        // The submission is already saved, so a mail problem does not lose it.
        try {
            if ($recipient) {
                Mail::to(trim($recipient))->send(new WebsiteFormMail(
                    subjectLine: $definition['subject'] . ' - ' . $site->get('site_title'),
                    fields: $fields,
                    replyToEmail: $data['email'],
                    replyToName: $data['name'],
                    attachmentPath: $submission->attachment_path ? Storage::disk('local')->path($submission->attachment_path) : null,
                    attachmentName: $submission->attachment_name,
                ));
                $submission->update(['email_sent' => true]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->respond($request, $form, true, $this->successMessage($form, $definition, $request));
    }

    private function store(Request $request, string $form, array $data): FormSubmission
    {
        $attachmentPath = null;
        $attachmentName = null;
        if ($file = $request->file('cv')) {
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->storeAs(
                'submissions/' . $form,
                Str::slug(pathinfo($attachmentName, PATHINFO_FILENAME)) . '-' . Str::random(8) . '.pdf',
                'local'
            ) ?: null;
        }

        $columns = ['name', 'email', 'phone', 'subject', 'position', 'interest', 'amount', 'message', 'cv'];

        return FormSubmission::create([
            'type' => $form,
            'status' => array_key_first(FormSubmission::statusesFor($form)),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? $data['position'] ?? $data['interest'] ?? null,
            'amount' => $form === 'donate' ? (float) $data['amount'] : null,
            'message' => $data['message'] ?? null,
            'details' => array_diff_key($data, array_flip($columns)) ?: null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);
    }

    private function successMessage(string $form, array $definition, Request $request): string
    {
        $message = (string) PageContent::forSlug($definition['page'])->get('success_message');

        return $form === 'donate'
            ? str_replace(':amount', (string) (float) $request->input('amount'), $message)
            : $message;
    }

    private function respond(Request $request, string $form, bool $ok, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok, 'message' => $message], $ok ? 200 : 500);
        }

        $response = back()->with('form_status', ['form' => $form, 'ok' => $ok, 'message' => $message]);

        return $ok ? $response : $response->withInput($request->except('cv'));
    }
}
