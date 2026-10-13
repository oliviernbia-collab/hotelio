@component('mail::message')
# Bienvenue sur HôteLio! 

{{ $user->name }},

Merci de vous être inscrit sur notre plateforme! Nous sommes ravi de vous accueillir.

## Qu'est-ce que vous pouvez faire maintenant?

- 🔍 Rechercher et découvrir des hôtels extraordinaires en Côte d'Ivoire
- 💳 Effectuer des réservations en ligne facilement
- 📝 Laisser des avis après votre séjour
- 📊 Suivre vos réservations en temps réel

## Accès à votre compte

@component('mail::button', ['url' => $loginUrl])
Se connecter
@endcomponent

Si vous avez besoin d'aide, notre équipe de support est disponible 24/7.

Bonnes vacances!

Cordialement,
**L'équipe HôteLio**
@endcomponent
