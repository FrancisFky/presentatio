<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Alerte à l'ambassade : une demande de rendez-vous ou un message vient d'arriver */
class NewSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment|Message $submission)
    {
        $this->locale('fr');
    }

    public function envelope(): Envelope
    {
        $subject = $this->submission instanceof Appointment
            ? "Nouvelle demande de rendez-vous {$this->submission->reference} — {$this->submission->name}"
            : 'Nouveau message du site — ' . ($this->submission->subject ?: $this->submission->name);

        return new Envelope(subject: $subject, replyTo: [new Address($this->submission->email, $this->submission->name)]);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.site.new-submission');
    }
}
