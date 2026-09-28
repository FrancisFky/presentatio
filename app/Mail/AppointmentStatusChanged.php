<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Réponse de l'ambassade au demandeur : confirmé, à reprogrammer ou refusé */
class AppointmentStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment, public ?string $note = null)
    {
        $this->locale($appointment->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.mail.appointment_status.subject', ['reference' => $this->appointment->reference], $this->appointment->locale));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.site.appointment-status');
    }
}
