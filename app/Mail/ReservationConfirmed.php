<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmation de réservation - {$this->reservation->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-confirmed',
            with: [
                'reservation' => $this->reservation,
                'hotel' => $this->reservation->hotel,
                'url' => route('reservations.index'),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('reservations.invoice', [
            'reservation' => $this->reservation,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'facture_'.$this->reservation->code.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
