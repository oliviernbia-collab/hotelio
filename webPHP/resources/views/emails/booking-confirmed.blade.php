@component('mail::message')
# Réservation Confirmée

Chère {{ $booking->full_name }},

Merci pour votre réservation chez {{ $hotel->name }}! Nous sommes heureux de vous accueillir.

## Détails de votre réservation

- **Référence**: {{ $booking->reference }}
- **Hôtel**: {{ $hotel->name }}
- **Check-in**: {{ $booking->check_in->format('d/m/Y') }}
- **Check-out**: {{ $booking->check_out->format('d/m/Y') }}
- **Nombre de nuits**: {{ $booking->check_out->diffInDays($booking->check_in) }}
- **Prix total**: {{ number_format($booking->total_price, 0, ',', ' ') }} FCFA

## Prochaines étapes

1. Vérifiez votre email de confirmation
2. Gardez votre référence de réservation à portée de main
3. Arrivez au moins 30 minutes avant votre check-in

@component('mail::button', ['url' => $url])
Voir votre réservation
@endcomponent

Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,
**L'équipe HôteLio**
@endcomponent
