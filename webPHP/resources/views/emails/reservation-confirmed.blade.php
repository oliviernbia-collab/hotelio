@component('mail::message')
# Réservation confirmée

Bonjour {{ optional($reservation->client)->first_name ?? 'cher client' }},

Votre réservation a bien été enregistrée pour l'hôtel **{{ $hotel->name }}**.

## Détails de la réservation

- Référence : **{{ $reservation->code }}**
- Chambre : **{{ $reservation->room }}**
- Check-in : **{{ $reservation->checkin->format('d/m/Y') }}**
- Check-out : **{{ $reservation->checkout->format('d/m/Y') }}**
- Nombres de nuits : **{{ $reservation->nights }}**
- Montant total : **{{ number_format($reservation->total, 0, ',', ' ') }} FCFA**
- Mode de paiement : **{{ $reservation->payment_method }}**

@component('mail::button', ['url' => $url])
Voir mes réservations
@endcomponent

La facture est jointe à cet email au format PDF.

Merci de nous avoir choisis,
**L'équipe Hôtelio**
@endcomponent
