<?php

namespace App\Mail;

use App\Models\Booking\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu cita está confirmada — Patricia Cuesta Psicología',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.appointment-confirmed',
        );
    }
}
