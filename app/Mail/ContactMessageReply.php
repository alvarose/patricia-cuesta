<?php

namespace App\Mail;

use App\Models\Contact\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message,
        public string $reply,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta a tu mensaje — Patricia Cuesta Psicología',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-message-reply',
        );
    }
}
