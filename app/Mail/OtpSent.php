<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpSent extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $otpCode;
    public $expiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
        $this->otpCode = $admin->otp_code;
        $this->expiresAt = $admin->otp_expires_at;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Code de vérification - ' . $this->otpCode,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-sent',
            with: [
                'admin' => $this->admin,
                'otpCode' => $this->otpCode,
                'expiresAt' => $this->expiresAt,
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