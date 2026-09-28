<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(public Admin $admin, string $token, public int $expiresInMinutes)
    {
        $this->resetUrl = route('password.reset', ['token' => $token, 'email' => $admin->email]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réinitialisation de votre mot de passe - Ambassade du Congo au Kenya',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-password-reset',
        );
    }
}
