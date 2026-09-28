<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Accusé de réception envoyé au demandeur, dans la langue du formulaire */
class AppointmentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
        $this->locale($appointment->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.mail.appointment_received.subject', ['reference' => $this->appointment->reference], $this->appointment->locale));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.site.appointment-received');
    }
}
