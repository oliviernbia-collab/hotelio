<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $stars = str_repeat('★', $this->review->rating) . str_repeat('☆', 5 - $this->review->rating);

        return (new MailMessage)
            ->subject("⭐ Nouvel avis sur {$this->review->hotel->name}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Un client vient de laisser un avis sur votre hôtel **{$this->review->hotel->name}**.")
            ->line("**Note :** {$stars} ({$this->review->rating}/5)")
            ->when($this->review->title, fn($m) => $m->line("**Titre :** {$this->review->title}"))
            ->line("**Commentaire :** {$this->review->comment}")
            ->action("Voir l'avis", route('hotels.show', $this->review->hotel))
            ->line("Continuez à offrir d'excellents séjours ! 🏨");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'new_review',
            'icon'    => 'fa-star',
            'color'   => '#F77F00',
            'title'   => 'Nouvel avis client',
            'message' => "{$this->review->rating}/5 étoiles sur {$this->review->hotel->name} — \"{$this->review->comment}\"",
            'url'     => route('hotels.show', $this->review->hotel),
            'amount'  => null,
        ];
    }
}
