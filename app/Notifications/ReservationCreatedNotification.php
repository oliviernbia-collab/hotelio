<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCreatedNotification extends Notification
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
            ->subject("📋 Nouvelle réservation — {$res->code}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Une nouvelle réservation a été enregistrée sur votre hôtel **{$res->hotel->name}**.")
            ->line("**Référence :** {$res->code}")
            ->line("**Chambre :** {$res->room}")
            ->line("**Arrivée :** " . $res->checkin->format('d/m/Y') . "  **Départ :** " . $res->checkout->format('d/m/Y'))
            ->line("**Montant :** " . number_format($res->total, 0, ',', ' ') . " FCFA")
            ->action('Voir la réservation', route('reservations.index'))
            ->line("Hôtelio — La plateforme N°1 en Côte d'Ivoire 🇨🇮");
    }

    public function toDatabase(object $notifiable): array
    {
        $res = $this->reservation;

        return [
            'type'    => 'reservation_created',
            'icon'    => 'fa-calendar-plus',
            'color'   => '#009A44',
            'title'   => 'Nouvelle réservation',
            'message' => "Réservation {$res->code} pour {$res->hotel->short_name} — " . $res->checkin->format('d/m/Y'),
            'url'     => route('reservations.index'),
            'amount'  => $res->total,
        ];
    }
}
