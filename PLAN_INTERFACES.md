# 🎨 PLAN DES INTERFACES UTILISATEUR

## 📱 Pages Publiques (Sans authentification)

### 1. Page d'Accueil (Déjà existe partiellement)
**Route**: `/` | **Template**: `resources/views/pages/home.blade.php`

Structure:
```
┌─ NAVBAR (Logo + Menu + Login/Register) ─────────────────────┐
├ HERO SECTION (Recherche principale)                          │
│  └─ Titre "Trouvez votre hôtel en Côte d'Ivoire"           │
│  └─ Barre de recherche (Ville, Dates, Personnes)           │
├ SECTION PROMOTIONS                                           │
│  └─ Slider des meilleures offres                            │
├ SECTION DESTINATIONS POPULAIRES                              │
│  └─ 4-6 hôtels en vedette avec photos et tarifs             │
├ SECTION AVIS CLIENTS                                         │
│  └─ Avis positifs avec photos                               │
├ SECTION FAQ                                                  │
│  └─ Questions fréquentes                                    │
└─ FOOTER (Liens, Contact, Social) ──────────────────────────┘
```

### 2. Page Résultats de Recherche
**Route**: `/search` | **Template**: `resources/views/pages/search-results.blade.php`

Structure:
```
┌─ NAVBAR ───────────────────────────────────────────────────┐
├ FILTERS SIDEBAR (Gauche, 30%)                              │
│  ├─ Prix (min-max slider)                                  │
│  ├─ Étoiles (1-5 stars)                                    │
│  ├─ Type de chambre (buttons)                              │
│  ├─ Services (checkboxes)                                  │
│  └─ Distance (km from center)                              │
├ MAIN CONTENT (70%)                                         │
│  ├─ "X hôtels trouvés pour Abidjan, 20-22 Mai"            │
│  ├─ SORT DROPDOWN (Prix ↑/↓, Avis, Popularité)            │
│  ├─ HOTEL CARDS (Répétés)                                 │
│  │  ├─ Photo carousel                                     │
│  │  ├─ Nom hôtel + ⭐ Rating                               │
│  │  ├─ Description courte                                 │
│  │  ├─ Prix par nuit                                      │
│  │  ├─ Bouton "Détails"                                   │
│  │  └─ Bouton "Réserver"                                  │
│  ├─ PAGINATION                                            │
│  │  └─ Page 1 [2] [3] [4] [Suivant]                       │
└ FOOTER ────────────────────────────────────────────────────┘
```

### 3. Page Détails Hôtel
**Route**: `/hotels/{id}` | **Template**: `resources/views/pages/hotel-details.blade.php`

Structure:
```
┌─ NAVBAR ───────────────────────────────────────────────────┐
├─ MAIN IMAGE + GALLERY                                      │
│  ├─ Grande image principale                               │
│  ├─ Thumbnails scrollables (5-10 photos)                  │
│  ├─ Bouton "Voir plus" → Gallerie complète               │
├─ HOTEL INFO (Right side)                                  │
│  ├─ Nom + ⭐ 4.8/5 (234 avis)                             │
│  ├─ Adresse + Bouton Carte                                │
│  ├─ Bouton "❤️ Ajouter favoris"                           │
├─ DESCRIPTION & SERVICES                                   │
│  ├─ Description complète                                  │
│  ├─ Services: WiFi, Parking, Pool, etc.                  │
├─ CHAMBRES DISPONIBLES                                     │
│  ├─ Table:                                                │
│  │  ├─ Type | Capacité | Prix/Nuit | Dispo | Action     │
│  │  ├─ Single | 1 | 50.000 XOF | ✅ | Réserver          │
│  │  ├─ Double | 2 | 80.000 XOF | ✅ | Réserver          │
│  │  └─ Suite | 4 | 150.000 XOF | ❌ | -                 │
├─ LOCALISATION                                             │
│  ├─ Google Map intégré                                    │
│  ├─ Adresse complète                                      │
│  ├─ Coordonnées GPS                                       │
├─ AVIS CLIENTS                                             │
│  ├─ Rating moyen + Breakdown (5⭐: 60%, 4⭐: 30%, etc)   │
│  ├─ Avis listés (avec photos)                             │
│  └─ Pagination                                            │
├─ FORMULAIRE RÉSERVATION (STICKY)                          │
│  ├─ Calendrier (dates)                                    │
│  ├─ Nombre de personnes                                  │
│  ├─ Prix total calculé                                   │
│  └─ Bouton "Réserver maintenant"                          │
└─ FOOTER ───────────────────────────────────────────────────┘
```

### 4. Page Réservation
**Route**: `/reservations/create` | **Template**: `resources/views/pages/reservation.blade.php`

Structure:
```
┌─ NAVBAR ───────────────────────────────────────────────────┐
├─ PROGRESS BAR: Détails → Paiement → Confirmation           │
├─ LEFT (60%): FORMULAIRE                                    │
│  ├─ CHAMBRE SÉLECTIONNÉE                                   │
│  │  ├─ Photo + Nom + Type                                 │
│  │  ├─ Dates: 20 Mai - 22 Mai (2 nuits)                   │
│  │  ├─ Personnes: 2 adultes + 1 enfant                    │
│  ├─ FORMULAIRE DÉTAILS CLIENT                             │
│  │  ├─ Nom complet *                                      │
│  │  ├─ Email *                                            │
│  │  ├─ Téléphone *                                        │
│  │  ├─ Nationalité *                                      │
│  │  ├─ Commentaires spéciaux (optionnel)                 │
│  │  └─ Checkbox: Accepte T&C                             │
├─ RIGHT (40%): RÉSUMÉ                                       │
│  ├─ HÔTEL                                                  │
│  │  └─ [Photo] Nom Hôtel ⭐ 4.8                           │
│  ├─ CHAMBRE                                                │
│  │  └─ Type | Capacité | Photos                          │
│  ├─ DATES                                                  │
│  │  ├─ Arrivée: 20 Mai 2024                               │
│  │  ├─ Départ: 22 Mai 2024                                │
│  │  └─ 2 nuits                                            │
│  ├─ PRIX DÉTAIL                                            │
│  │  ├─ 2 nuits × 50.000 XOF = 100.000 XOF                │
│  │  ├─ Taxe (10%) = 10.000 XOF                            │
│  │  ├─ Frais service (2%) = 2.000 XOF                     │
│  │  └─ TOTAL = 112.000 XOF                                │
│  ├─ CODE PROMO (optionnel)                                │
│  │  └─ Input + Bouton "Appliquer"                         │
│  └─ BOUTON "Continuer vers paiement"                      │
└─ FOOTER ───────────────────────────────────────────────────┘
```

### 5. Page Paiement
**Route**: `/payments/create/{reservation_id}` | **Template**: `resources/views/pages/payment.blade.php`

Structure:
```
┌─ NAVBAR ───────────────────────────────────────────────────┐
├─ PROGRESS BAR: Détails → Paiement ✓ → Confirmation         │
├─ LEFT (60%): MÉTHODES DE PAIEMENT                          │
│  ├─ TAB: Cartes Bancaires                                  │
│  │  ├─ Radio: Stripe (Visa, MasterCard)                  │
│  │  ├─ Formulaire:                                        │
│  │  │  ├─ Numéro carte (16 digits)                       │
│  │  │  ├─ Nom titulaire                                  │
│  │  │  ├─ Date expiration                                │
│  │  │  ├─ CVV                                            │
│  │  │  └─ Bouton "Payer 112.000 XOF"                     │
│  ├─ TAB: Orange Money (Côte d'Ivoire)                     │
│  │  ├─ Radio: Orange Money                               │
│  │  ├─ Formulaire:                                        │
│  │  │  ├─ Numéro Orange (01 XX XX XX XX)                │
│  │  │  ├─ Bouton "Envoyer USSD" / "Payer"               │
│  │  │  └─ Note: "Vous recevrez un SMS USSD"             │
│  ├─ TAB: MTN Money                                        │
│  │  ├─ Radio: MTN Money                                  │
│  │  ├─ Formulaire:                                        │
│  │  │  ├─ Numéro MTN (01 XX XX XX XX)                   │
│  │  │  ├─ Bouton "Envoyer USSD" / "Payer"               │
│  │  │  └─ Note: "Vous recevrez un SMS USSD"             │
│  └─ TAB: Virement Bancaire                                │
│     ├─ Radio: Virement                                    │
│     ├─ Affichage Détails bancaires                        │
│     ├─ Numéro compte, IBAN, BIC                           │
│     └─ Réf: RES-12345-2024-05-20                          │
├─ RIGHT (40%): RÉSUMÉ & SÉCURITÉ                           │
│  ├─ RÉSUMÉ COMMANDE                                        │
│  │  ├─ Hôtel + Chambre + Dates                            │
│  │  └─ Total: 112.000 XOF                                 │
│  ├─ SÉCURITÉ                                               │
│  │  ├─ 🔒 Connexion sécurisée SSL                        │
│  │  ├─ 🛡️ Données chiffrées                              │
│  │  ├─ ✓ Paiement garanti                                │
│  │  └─ Logo Stripe/Providers                             │
│  └─ PROBLÈME DE PAIEMENT?                                │
│     └─ Lien contact support                               │
└─ FOOTER ───────────────────────────────────────────────────┘
```

### 6. Page Confirmation
**Route**: `/reservations/{id}/confirmation` | **Template**: `resources/views/pages/confirmation.blade.php`

Structure:
```
┌─ NAVBAR ───────────────────────────────────────────────────┐
├─ SUCCESS MESSAGE (Vert, large)                            │
│  └─ ✅ "Réservation confirmée!"                           │
├─ RÉSUMÉ PRINCIPAL                                         │
│  ├─ Numéro réservation: #RES-123456                       │
│  ├─ Confirmation envoyée à: client@example.com           │
├─ DÉTAILS RÉSERVATION                                      │
│  ├─ HÔTEL                                                  │
│  │  ├─ [Photo] Nom Hôtel ⭐                               │
│  │  ├─ Adresse                                            │
│  │  ├─ Téléphone                                          │
│  │  └─ Email                                              │
│  ├─ CHAMBRE                                                │
│  │  └─ Type | Capacité | Services                         │
│  ├─ DATES                                                  │
│  │  ├─ Arrivée: 20 Mai 2024 (14:00)                       │
│  │  ├─ Départ: 22 Mai 2024 (11:00)                        │
│  │  └─ 2 nuits                                            │
│  ├─ CLIENT                                                 │
│  │  ├─ Nom: Jean Dupont                                   │
│  │  └─ Personnes: 2 adultes + 1 enfant                    │
│  ├─ PRIX                                                   │
│  │  ├─ 2 nuits × 50.000 = 100.000 XOF                     │
│  │  ├─ Taxes = 12.000 XOF                                 │
│  │  └─ TOTAL: 112.000 XOF ✓ Payé                          │
├─ ACTIONS                                                   │
│  ├─ Bouton: "📥 Télécharger facture PDF"                  │
│  ├─ Bouton: "📧 Renvoyer confirmation"                    │
│  ├─ Bouton: "📅 Ajouter au calendrier"                    │
│  └─ Bouton: "⬅️ Retour à l'accueil"                       │
├─ PROCHAINES ÉTAPES                                        │
│  ├─ 📩 Email confirmation reçu                            │
│  ├─ 📱 SMS de rappel (24h avant)                          │
│  ├─ 🔑 Clé/Instructions arrivée                           │
│  └─ ⭐ Avis après séjour                                  │
└─ FOOTER ───────────────────────────────────────────────────┘
```

---

## 🔐 Pages Authentification

### 7. Page Login
**Route**: `/login` | **Template**: `resources/views/auth/login.blade.php`

### 8. Page Register
**Route**: `/register` | **Template**: `resources/views/auth/register.blade.php`

---

## 👤 Dashboard Client

### 9. Dashboard Client (Accueil)
**Route**: `/client` | **Template**: `resources/views/dashboard/client/index.blade.php`

Structure:
```
┌─ NAVBAR + SIDEBAR ──────────────────────────────────────────┐
├─ CONTENT PRINCIPALE                                        │
│  ├─ Bienvenue, Jean!                                       │
│  ├─ QUICK STATS                                            │
│  │  ├─ Réservations actives: 1                             │
│  │  ├─ Dépenses totales: 450.000 XOF                       │
│  │  └─ Avis écrits: 5                                      │
│  ├─ RÉSERVATIONS À VENIR                                   │
│  │  └─ Card (Hôtel + Dates + Actions)                      │
│  ├─ MES FAVORIS                                            │
│  │  └─ Grille hôtels (photos)                              │
│  └─ AVIS À LAISSER                                         │
│     └─ Card (Hôtel + "Laisser avis")                       │
└─────────────────────────────────────────────────────────────┘
```

### 10. Mes Réservations
**Route**: `/client/reservations` | **Template**: `resources/views/dashboard/client/reservations.blade.php`

### 11. Mes Favoris
**Route**: `/client/favorites` | **Template**: `resources/views/dashboard/client/favorites.blade.php`

### 12. Laisser un Avis
**Route**: `/reviews/create/{reservation_id}` | **Template**: `resources/views/pages/review-form.blade.php`

---

## 🏨 Dashboard Prestataire (Hôtelier)

### 13. Dashboard Prestataire (Accueil)
**Route**: `/prestataire` | **Template**: `resources/views/dashboard/prestataire/index.blade.php`

Structure:
```
┌─ NAVBAR + SIDEBAR ──────────────────────────────────────────┐
├─ STATISTICS GRID                                           │
│  ├─ Card: Revenue (Mois) = 2.500.000 XOF                   │
│  ├─ Card: Occupancy Rate = 78%                             │
│  ├─ Card: Rating Moyen = ⭐ 4.6/5                          │
│  ├─ Card: Guests Total = 145                               │
│  └─ Card: Nouvelle Réservations = 3                        │
├─ CHARTS                                                    │
│  ├─ Graphique Revenue (12 mois)                            │
│  ├─ Graphique Occupancy (30 jours)                         │
│  └─ Graphique Rating Evolution                             │
├─ RÉSERVATIONS RÉCENTES                                     │
│  └─ Table (Hotel | Guest | Dates | Total | Status)        │
├─ NOUVEAUX AVIS                                             │
│  └─ Card (Hotel | Guest | Rating | Comment)               │
└─────────────────────────────────────────────────────────────┘
```

### 14. Gestion Hôtels
**Route**: `/prestataire/hotels` | **Template**: `resources/views/dashboard/prestataire/hotels/index.blade.php`

### 15. Détails Hôtel
**Route**: `/prestataire/hotels/{id}` | **Template**: `resources/views/dashboard/prestataire/hotels/show.blade.php`

### 16. Ajouter/Éditer Hôtel
**Route**: `/prestataire/hotels/create` | `/prestataire/hotels/{id}/edit`
**Template**: `resources/views/dashboard/prestataire/hotels/form.blade.php`

### 17. Gestion Chambres
**Route**: `/prestataire/rooms` | **Template**: `resources/views/dashboard/prestataire/rooms/index.blade.php`

### 18. Ajouter/Éditer Chambre
**Route**: `/prestataire/rooms/create` | `/prestataire/rooms/{id}/edit`
**Template**: `resources/views/dashboard/prestataire/rooms/form.blade.php`

### 19. Gestion Réservations
**Route**: `/prestataire/reservations` | **Template**: `resources/views/dashboard/prestataire/reservations/index.blade.php`

### 20. Détails Réservation
**Route**: `/prestataire/reservations/{id}` | **Template**: `resources/views/dashboard/prestataire/reservations/show.blade.php`

### 21. Gestion Tarifs
**Route**: `/prestataire/pricing` | **Template**: `resources/views/dashboard/prestataire/pricing/index.blade.php`

### 22. Calendrier Disponibilité
**Route**: `/prestataire/calendar` | **Template**: `resources/views/dashboard/prestataire/calendar/index.blade.php`

### 23. Avis Clients
**Route**: `/prestataire/reviews` | **Template**: `resources/views/dashboard/prestataire/reviews/index.blade.php`

### 24. Rapports Financiers
**Route**: `/prestataire/reports` | **Template**: `resources/views/dashboard/prestataire/reports/index.blade.php`

---

## 👨‍💼 Dashboard Admin

### 25. Dashboard Admin
**Route**: `/admin` | **Template**: `resources/views/dashboard/admin/index.blade.php`

### 26. Gestion Utilisateurs
**Route**: `/admin/users` | **Template**: `resources/views/dashboard/admin/users/index.blade.php`

### 27. Modération Avis
**Route**: `/admin/reviews` | **Template**: `resources/views/dashboard/admin/reviews/index.blade.php`

### 28. Rapports Système
**Route**: `/admin/reports` | **Template**: `resources/views/dashboard/admin/reports/index.blade.php`

### 29. Paramètres Système
**Route**: `/admin/settings` | **Template**: `resources/views/dashboard/admin/settings/index.blade.php`

---

## 📝 Pages Utilitaires

### 30. Page Profil
**Route**: `/profile` | **Template**: `resources/views/profile/index.blade.php`

### 31. Page Paramètres
**Route**: `/settings` | **Template**: `resources/views/settings/index.blade.php`

### 32. Page À Propos
**Route**: `/about` | **Template**: `resources/views/pages/about.blade.php`

### 33. Page Contact
**Route**: `/contact` | **Template**: `resources/views/pages/contact.blade.php`

### 34. Page Conditions d'Utilisation
**Route**: `/terms` | **Template**: `resources/views/pages/terms.blade.php`

### 35. Page Politique de Confidentialité
**Route**: `/privacy` | **Template**: `resources/views/pages/privacy.blade.php`

---

## 📊 RÉSUMÉ

**Total Pages à Créer/Améliorer: 35 pages**

```
✅ Déjà existantes (3):
  - Accueil (partielle)
  - Login/Register
  - Dashboard basique

❌ À créer (32):
  - Recherche avancée (2)
  - Réservation (3)
  - Client (4)
  - Prestataire (11)
  - Admin (5)
  - Utilitaires (7)
```

**Structure de dossiers recommandée:**
```
resources/views/
├─ layouts/
│  ├─ app.blade.php (Master)
│  ├─ guest.blade.php (Sans login)
│  └─ dashboard.blade.php (Avec sidebar)
├─ pages/
│  ├─ home.blade.php
│  ├─ search-results.blade.php
│  ├─ hotel-details.blade.php
│  ├─ reservation.blade.php
│  ├─ payment.blade.php
│  ├─ confirmation.blade.php
│  └─ review-form.blade.php
├─ auth/
│  ├─ login.blade.php
│  └─ register.blade.php
├─ dashboard/
│  ├─ client/
│  ├─ prestataire/
│  └─ admin/
└─ components/
   ├─ navbar.blade.php
   ├─ sidebar.blade.php
   ├─ footer.blade.php
   ├─ hotel-card.blade.php
   └─ reservation-card.blade.php
```

---

**Êtes-vous prêt à commencer à créer ces interfaces ?** 🎨
