<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
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
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null,
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
        if (!$this->attachmentPath || !is_file($this->attachmentPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->attachmentPath)
                ->as($this->attachmentName ?: basename($this->attachmentPath))
                ->withMime('application/pdf'),
        ];
    }
}
