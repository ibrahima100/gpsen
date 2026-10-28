<?php

namespace App\Mail;

use App\Models\Warning;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarningBeforeAccountClosureMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Warning $warning) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Avertissement important concernant votre compte GPsen',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.warnings.before-closure',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
