<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivationLinkSent extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $activationToken;
    public $activationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Admin $admin, string $activationToken)
    {
        $this->admin = $admin;
        $this->activationToken = $activationToken;
        $this->activationUrl = route('admin.activate', ['token' => $activationToken]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lien d\'activation de votre compte administrateur - Ambassade du Congo au Kenya',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-activation-sent',
            with: [
                'admin' => $this->admin,
                'activationToken' => $this->activationToken,
                'activationUrl' => $this->activationUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}