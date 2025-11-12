<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $messageSubject,
        public string $messageContent
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectLine = $this->messageSubject
            ? 'Message de contact : '.$this->messageSubject
            : 'Nouveau message de contact';

        return new Envelope(
            replyTo: [$this->email],
            subject: $subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.message',
            with: [
                'senderName' => $this->name,
                'senderEmail' => $this->email,
                'messageSubject' => $this->messageSubject ?? 'Aucun sujet',
                'messageContent' => $this->messageContent,
            ],
        );
    }
}
