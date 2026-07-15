<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $role = match($notifiable->role) {
            'prestataire' => 'hôtelier partenaire',
            'admin'       => 'administrateur',
            default       => 'voyageur',
        };

        return (new MailMessage)
            ->subject('🎉 Bienvenue sur Hôtelio !')
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Votre compte **{$role}** a bien été créé sur la plateforme Hôtelio.")
            ->line("Vous pouvez dès maintenant :")
            ->line(match($notifiable->role) {
                'prestataire' => '• Ajouter vos hôtels et gérer vos réservations',
                'admin'       => '• Administrer la plateforme et gérer tous les utilisateurs',
                default       => '• Rechercher et réserver des hôtels partout en Côte d\'Ivoire',
            })
            ->action('Accéder à mon espace', route('dashboard'))
            ->line("Bienvenue dans la famille Hôtelio 🇨🇮 — la plateforme N°1 de réservation hôtelière en Côte d'Ivoire.");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'welcome',
            'icon'    => 'fa-hand-wave',
            'color'   => '#F77F00',
            'title'   => 'Bienvenue sur Hôtelio !',
            'message' => "Votre compte " . ucfirst($notifiable->role) . " est prêt. Explorez la plateforme !",
            'url'     => route('dashboard'),
            'amount'  => null,
        ];
    }
}
