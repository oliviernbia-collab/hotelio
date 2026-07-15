@component('mail::message')
# Votre avis a été publié!

{{ $review->user->name }},

Merci pour votre avis! Votre commentaire vient de être publié sur {{ $hotel->name }}.

## Votre avis

**Note**: ⭐ {{ $review->rating }}/5

> {{ $review->comment }}

Votre avis aide les futurs voyageurs à faire le meilleur choix. Merci de votre contribution!

@component('mail::button', ['url' => route('hotels.show', $hotel)])
Voir l'hôtel
@endcomponent

Cordialement,
**L'équipe HôteLio**
@endcomponent
