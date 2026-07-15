<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommandeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;

    public function __construct($commande)
    {
        $this->commande = $commande;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de commande'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.commande',
            with: [
                'commande' => $this->commande
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}