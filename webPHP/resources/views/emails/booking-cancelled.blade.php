@component('mail::message')
# Réservation Annulée

Chère {{ $booking->full_name }},

Votre réservation a été annulée avec succès.

## Détails de l'annulation

- **Référence**: {{ $booking->reference }}
- **Hôtel**: {{ $hotel->name }}
- **Date d'annulation**: {{ now()->format('d/m/Y à H:i') }}

Si vous aviez effectué un paiement, celui-ci sera remboursé dans les 5-7 jours ouvrables.

Si vous avez des questions sur votre remboursement, veuillez contacter notre support.

Merci d'avoir utilisé HôteLio!

Cordialement,
**L'équipe HôteLio**
@endcomponent
