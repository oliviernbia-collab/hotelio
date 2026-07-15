<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancelledNotification extends Notification
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
            ->subject("❌ Réservation annulée — {$res->code}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre réservation **{$res->code}** pour l'hôtel **{$res->hotel->name}** a été annulée.")
            ->line("Si vous n'êtes pas à l'origine de cette annulation, contactez-nous immédiatement.")
            ->action('Faire une nouvelle réservation', route('hotels.public'))
            ->line("L'équipe Hôtelio reste à votre disposition 🇨🇮");
    }

    public function toDatabase(object $notifiable): array
    {
        $res = $this->reservation;

        return [
            'type'    => 'reservation_cancelled',
            'icon'    => 'fa-calendar-xmark',
            'color'   => '#e63946',
            'title'   => 'Réservation annulée',
            'message' => "La réservation {$res->code} pour {$res->hotel->short_name} a été annulée.",
            'url'     => route('hotels.public'),
            'amount'  => null,
        ];
    }
}
