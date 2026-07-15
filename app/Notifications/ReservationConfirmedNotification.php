<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $res = $this->reservation;

        return (new MailMessage)
            ->subject("✅ Paiement confirmé — {$res->code}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre paiement a été confirmé pour votre séjour à **{$res->hotel->name}**.")
            ->line("**Référence :** {$res->code}")
            ->line("**Chambre :** {$res->room}")
            ->line("**Arrivée :** " . $res->checkin->format('d/m/Y') . " à partir de 14h00")
            ->line("**Départ :** " . $res->checkout->format('d/m/Y') . " avant 12h00")
            ->line("**Total payé :** " . number_format($res->total, 0, ',', ' ') . " FCFA")
            ->line("**Mode de paiement :** {$res->payment_method}")
            ->action('Télécharger ma facture', route('reservations.invoice', $res))
            ->line("Bon séjour ! L'équipe Hôtelio 🇨🇮");
    }

    public function toDatabase(object $notifiable): array
    {
        $res = $this->reservation;

        return [
            'type'    => 'reservation_confirmed',
            'icon'    => 'fa-circle-check',
            'color'   => '#009A44',
            'title'   => 'Paiement confirmé',
            'message' => "Votre réservation {$res->code} à {$res->hotel->name} est confirmée.",
            'url'     => route('reservations.success', $res),
            'amount'  => $res->total,
        ];
    }
}
