<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\WebsiteFormMail;
use App\Support\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Website forms (contact, volunteer, job application, donation), sent by email.
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
        $fields = [];
        foreach (self::LABELS as $key => $label) {
            if (isset($data[$key]) && $data[$key] !== '') {
                $fields[$label] = (string) $data[$key];
            }
        }

        $recipient = collect([$site->get('form_email'), $site->get('email'), config('mail.from.address')])
            ->first(fn ($email) => filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL));

        try {
            if (!$recipient) {
                throw new \RuntimeException('No email address is configured for website forms.');
            }
            Mail::to(trim($recipient))->send(new WebsiteFormMail(
                subjectLine: $definition['subject'] . ' - ' . $site->get('site_title'),
                fields: $fields,
                replyToEmail: $data['email'],
                replyToName: $data['name'],
                attachment: $request->file('cv'),
            ));
        } catch (\Throwable $e) {
            report($e);

            return $this->respond($request, $form, false, (string) $site->get('form_error'));
        }

        return $this->respond($request, $form, true, $this->successMessage($form, $definition, $request));
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
