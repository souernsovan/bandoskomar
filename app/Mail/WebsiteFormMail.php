<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * A website form submission (contact, volunteer, job, donation) sent to the team.
 */
class WebsiteFormMail extends Mailable
{
    use Queueable;

    /**
     * @param  array<string, string>  $fields  label => value
     */
    public function __construct(
        public string $subjectLine,
        public array $fields,
        public string $replyToEmail,
        public string $replyToName,
        public ?UploadedFile $attachment = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->replyToEmail, $this->replyToName)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.website-form',
            with: ['title' => $this->subjectLine, 'fields' => $this->fields],
        );
    }

    public function attachments(): array
    {
        if (!$this->attachment) {
            return [];
        }

        return [
            Attachment::fromPath($this->attachment->getRealPath())
                ->as($this->attachment->getClientOriginalName())
                ->withMime('application/pdf'),
        ];
    }
}
