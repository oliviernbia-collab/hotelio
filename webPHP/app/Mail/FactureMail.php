<?php

namespace App\Mail;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $facture;

    // ✅ IMPORTANT
    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }

    // Sujet du mail
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre facture '.$this->facture->numero,
        );
    }

    // Vue email
    public function content(): Content
    {
        return new Content(
            view: 'emails.facture',
            with: [
                'facture' => $this->facture
            ]
        );
    }

    // Pièce jointe PDF
    public function attachments(): array
    {
        $pdf = Pdf::loadView('facturations.pdf', [
            'facture' => $this->facture
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'facture_'.$this->facture->numero.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}