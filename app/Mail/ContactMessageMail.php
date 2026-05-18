<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $siteName,
        public string $pageTitle,
        public string $name,
        public string $email,
        public string $message,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Message - ' . $this->siteName,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->email, $this->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'siteName' => $this->siteName,
                'pageTitle' => $this->pageTitle,
                'name' => $this->name,
                'email' => $this->email,
                'message' => $this->message,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
