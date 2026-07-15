# 🏗️ ARCHITECTURE DE LA PLATEFORME

## Diagramme Global du Système

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENTS UTILISATEURS                     │
├─────────────────────────────────────────────────────────────┤
│  Propriétaires d'Hôtel   │   Clients (Voyageurs)   │  Admin │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│              INTERFACES UTILISATEUR (Frontend)              │
├─────────────────────────────────────────────────────────────┤
│  Blade Templates + TailwindCSS + Vite + Alpine JS          │
│  - Accueil / Recherche                                      │
│  - Détails Hôtel                                            │
│  - Réservation                                              │
│  - Paiement                                                 │
│  - Dashboard (3 rôles)                                      │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│           COUCHE DE ROUTAGE ET MIDDLEWARE                   │
├─────────────────────────────────────────────────────────────┤
│  - Authentication Middleware                                │
│  - Role-Based Access Control                                │
│  - Rate Limiting                                            │
│  - CORS Handler                                             │
│  - Logging                                                  │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│              CONTRÔLEURS (Business Logic)                   │
├─────────────────────────────────────────────────────────────┤
│  ├─ AuthController          ├─ HotelController              │
│  ├─ DashboardController     ├─ ReservationController        │
│  ├─ PaymentController       ├─ ReviewController             │
│  ├─ NotificationController  ├─ SearchController             │
│  └─ ReportController        └─ ClientController             │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│              SERVICES (Logique Métier)                      │
├─────────────────────────────────────────────────────────────┤
│  ├─ ReservationService      ├─ PaymentService               │
│  ├─ SearchService           ├─ NotificationService          │
│  ├─ PricingService          ├─ AvailabilityService          │
│  ├─ ReportService           └─ ReviewService                │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│              MODÈLES ELOQUENT (ORM)                         │
├─────────────────────────────────────────────────────────────┤
│  ├─ User                    ├─ Hotel                        │
│  ├─ Room                    ├─ RoomAvailability             │
│  ├─ Reservation             ├─ Payment                      │
│  ├─ Review                  ├─ Favorite                     │
│  ├─ Product                 ├─ Order                        │
│  └─ Notification            └─ Staff                        │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│         COUCHE DE DONNÉES (Database Layer)                  │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────────┐         ┌──────────────────────────┐ │
│  │  Base de Données │◄────────►  Cache Redis/Memcached   │ │
│  │   (MySQL)        │         │   (Sessions, Queries)    │ │
│  └──────────────────┘         └──────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
         ↙         ↓         ↘
      Tables    Queries    Indexes
```

---

## Architecture MVC Détaillée

```
┌──────────────────────────────────────────────────────────────┐
│                     COUCHE PRÉSENTATION                      │
│  (Vue: Blade Templates + TailwindCSS)                        │
│  ├─ layouts/app.blade.php (Master layout)                    │
│  ├─ pages/home.blade.php                                     │
│  ├─ pages/search-results.blade.php                           │
│  ├─ pages/hotel-details.blade.php                            │
│  ├─ pages/reservation.blade.php                              │
│  ├─ pages/payment.blade.php                                  │
│  └─ dashboard/                                               │
│     ├─ prestataire/ (Owner)                                  │
│     ├─ client/ (Guest)                                       │
│     └─ admin/                                                │
└──────────────────────────────────────────────────────────────┘
                          ↕
┌──────────────────────────────────────────────────────────────┐
│                 COUCHE MÉTIER (Controllers)                  │
│  Request → Controller → Service → Model → Response          │
│                                                              │
│  Flux:                                                       │
│  1. Récupère request (Form Request)                          │
│  2. Valide les données (Validation rules)                    │
│  3. Appelle Service approprié                                │
│  4. Retourne Vue ou JSON                                     │
└──────────────────────────────────────────────────────────────┘
                          ↕
┌──────────────────────────────────────────────────────────────┐
│          COUCHE LOGIQUE MÉTIER (Services)                    │
│  Contient la logique complexe du domaine                     │
│                                                              │
│  ReservationService:                                         │
│  ├─ validateAvailability()                                   │
│  ├─ calculatePrice()                                         │
│  ├─ createReservation()                                      │
│  ├─ cancelReservation()                                      │
│  └─ sendConfirmationEmail()                                  │
└──────────────────────────────────────────────────────────────┘
                          ↕
┌──────────────────────────────────────────────────────────────┐
│          COUCHE DE DONNÉES (Models & Database)               │
│  Represents métiers et relations                             │
│                                                              │
│  Modèles avec relations:                                     │
│  ├─ User hasMany Hotels                                      │
│  ├─ Hotel hasMany Rooms                                      │
│  ├─ Room hasMany Reservations                                │
│  ├─ Reservation belongsTo User                               │
│  └─ Room hasMany RoomAvailabilities                          │
└──────────────────────────────────────────────────────────────┘
```

---

## Flux de Réservation (Séquence)

```
CLIENT                  SYSTÈME                  PAYMENT             EMAIL
  │                       │                        │                   │
  ├─ Recherche hôtels ──→ │                        │                   │
  │                    (Filtre & Trie)             │                   │
  ├─ Voit résultats ←──   │                        │                   │
  │                                                 │                   │
  ├─ Clique hôtel ───────→ │                        │                   │
  │                    (Charge détails)            │                   │
  ├─ Voit infos ←────────  │                        │                   │
  │                                                 │                   │
  ├─ Remplit formulaire → │                        │                   │
  │  (Dates, personnes)    │                        │                   │
  │                    (Vérifie dispo)             │                   │
  ├─ Voit prix total ←────│                        │                   │
  │                                                 │                   │
  ├─ Clique "Réserver" →  │                        │                   │
  │                    (Valide)                    │                   │
  │                    ├─ Crée réservation temp    │                   │
  │                    └─ Redirige paiement ──────→│                   │
  │                                          (Traite)                   │
  ├─ Saisit carte    ────────────────────→│        │                   │
  │                                        ├─ Valide                   │
  │                                        ├─ Débite                   │
  │                                        └─ Confirme ──→ │           │
  │                                                    ├─ Envoie ──→ EMAIL
  │                                                    │                │
  │                    (Mise à jour) ←───────────────┤                │
  │                    ├─ Confirme réservation        │        ├─ Confirmation
  │                    ├─ Génère facture              │        ├─ Facture PDF
  │                    └─ Notifie prestataire        │        └─ Instructions
  │                                                   │                │
  ├─ Reçoit confirmation ←─────────────────────────────────────────────┤
  │
  ✓ Réservation confirmée !
```

---

## Modèles de Données et Relations

```sql
USERS (Authentification)
├─ id
├─ name
├─ email
├─ password (hashed)
├─ role (admin | prestataire | client)
├─ phone
├─ status (active | inactive)
├─ created_at
└─ updated_at
   ↓
   ├── many: Hotels (si prestataire)
   ├── many: Reservations (si client)
   ├── many: Reviews (si client)
   └── many: Favorites (si client)


HOTELS (Propriétaires d'hôtel)
├─ id
├─ user_id (FK → prestataire)
├─ name
├─ description
├─ phone
├─ email
├─ address
├─ city
├─ country (Côte d'Ivoire)
├─ latitude
├─ longitude
├─ stars_count (1-5)
├─ image_url
├─ check_in_time
├─ check_out_time
├─ services (JSON)
├─ status (active | inactive)
├─ created_at
└─ updated_at
   ↓
   ├── many: Rooms
   ├── many: Reviews
   ├── many: Staff
   ├── many: Expenses
   └── one: Reviews → hasMany


ROOMS
├─ id
├─ hotel_id (FK → Hotels)
├─ number
├─ room_type (single | double | suite)
├─ capacity
├─ price_per_night
├─ description
├─ image_url
├─ amenities (JSON)
├─ status (available | maintenance)
├─ created_at
└─ updated_at
   ↓
   ├── many: Reservations
   ├── many: RoomAvailabilities
   └── one: Hotel


RESERVATIONS
├─ id
├─ user_id (FK → Client)
├─ room_id (FK → Room)
├─ hotel_id (FK → Hotel)
├─ check_in_date
├─ check_out_date
├─ number_of_guests
├─ total_price
├─ status (pending | confirmed | cancelled | completed)
├─ payment_id (FK → Payment)
├─ notes
├─ created_at
└─ updated_at
   ↓
   ├── belongsTo: User
   ├── belongsTo: Room
   ├── belongsTo: Hotel
   └── hasOne: Payment


PAYMENTS
├─ id
├─ reservation_id (FK)
├─ user_id (FK)
├─ amount
├─ currency (XOF)
├─ method (stripe | orange_money | mtn_money | virement)
├─ status (pending | success | failed | refunded)
├─ transaction_id
├─ reference_number
├─ created_at
└─ updated_at


REVIEWS
├─ id
├─ user_id (FK → Client)
├─ hotel_id (FK)
├─ reservation_id (FK)
├─ rating (1-5)
├─ title
├─ comment
├─ images (JSON)
├─ helpful_count
├─ status (pending | approved | rejected)
├─ created_at
└─ updated_at


FAVORITES
├─ id
├─ user_id (FK → Client)
├─ hotel_id (FK)
├─ created_at


ROOM_AVAILABILITIES
├─ id
├─ room_id (FK)
├─ date
├─ is_available (boolean)
├─ price_override (nullable)
├─ notes
└─ created_at
```

---

## Architecture des Services

```
┌─────────────────────────────────────────────────────────────┐
│                     SERVICE LAYER                           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ReservationService                                         │
│  ├─ createReservation()                                     │
│  ├─ cancelReservation()                                     │
│  ├─ checkAvailability()                                     │
│  ├─ calculateTotalPrice()                                   │
│  └─ generateConfirmation()                                  │
│                                                             │
│  SearchService                                              │
│  ├─ searchHotels()                                          │
│  ├─ applyFilters()                                          │
│  ├─ sortResults()                                           │
│  └─ getPaginatedResults()                                   │
│                                                             │
│  PaymentService                                             │
│  ├─ processPayment()                                        │
│  ├─ refundPayment()                                         │
│  ├─ verifyPayment()                                         │
│  └─ generateInvoice()                                       │
│                                                             │
│  NotificationService                                        │
│  ├─ sendEmail()                                             │
│  ├─ sendSMS()                                               │
│  ├─ sendPushNotification()                                  │
│  └─ queue()                                                 │
│                                                             │
│  ReviewService                                              │
│  ├─ createReview()                                          │
│  ├─ moderateReview()                                        │
│  ├─ calculateRating()                                       │
│  └─ getHotelRating()                                        │
│                                                             │
│  ReportService                                              │
│  ├─ generateRevenueReport()                                 │
│  ├─ generateOccupancyReport()                               │
│  ├─ generateGuestReport()                                   │
│  └─ exportToCSV()                                           │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Flux de Sécurité

```
REQUEST
  ↓
MIDDLEWARE STACK:
├─ VerifyCSRFToken
├─ EncryptCookies
├─ CheckForMaintenanceMode
├─ HandleCors
├─ TrustProxies
├─ Authentication
├─ Authorization (Role-based)
├─ RateLimit
└─ Logging
  ↓
CONTROLLER
  ├─ ValidateInput (FormRequest)
  ├─ SanitizeData
  ├─ CheckPermissions
  └─ ProcessRequest
  ↓
SERVICE LAYER
  ├─ ExecuteLogic
  ├─ LogActions
  └─ HandleErrors
  ↓
DATABASE
  ├─ Prepared Statements (Prevent SQL Injection)
  ├─ Eloquent ORM
  └─ AuditTrail
  ↓
RESPONSE
  ├─ Encrypt Sensitive Data
  ├─ Set Security Headers
  └─ Send to Client
```

---

## Stack Technologique Complète

```
┌─ FRONTEND ─────────────────────┐
│ - HTML 5                        │
│ - TailwindCSS 4                 │
│ - Alpine.js (Interactivité)     │
│ - Vite (Build tool)             │
│ - Axios (HTTP Client)           │
└─────────────────────────────────┘

┌─ BACKEND ──────────────────────┐
│ - Laravel 12                    │
│ - PHP 8.2                       │
│ - Eloquent ORM                  │
│ - Laravel Queue                 │
│ - Laravel Cache                 │
│ - Laravel Mail                  │
└─────────────────────────────────┘

┌─ PAYMENT ──────────────────────┐
│ - Stripe API                    │
│ - Orange Money API              │
│ - MTN Money API                 │
│ - Webhook Processing            │
└─────────────────────────────────┘

┌─ DATABASE ─────────────────────┐
│ - MySQL 8 / PostgreSQL 13       │
│ - Redis (Cache & Sessions)      │
│ - Backup Automated              │
└─────────────────────────────────┘

┌─ INFRASTRUCTURE ───────────────┐
│ - Ubuntu Server 22.04           │
│ - Nginx / Apache                │
│ - SSL Certificate               │
│ - Docker (Optional)             │
│ - CI/CD Pipeline                │
└─────────────────────────────────┘
```

---

## Intégrations Externes

```
┌─────────────────────────────────────────────────────────────┐
│               SERVICES EXTERNES                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Payment Providers:                                         │
│  ├─ Stripe (Cartes bancaires, Wallets)                     │
│  ├─ Orange Money (Mobile Payment - CI)                     │
│  └─ MTN Money (Mobile Payment - CI)                        │
│                                                             │
│  Communication:                                             │
│  ├─ SendGrid / Mailable (Email)                            │
│  ├─ Twilio / Africast (SMS)                                │
│  └─ Firebase (Push Notifications)                          │
│                                                             │
│  Mapping & Location:                                        │
│  ├─ Google Maps API                                        │
│  └─ OpenStreetMap                                          │
│                                                             │
│  Analytics:                                                 │
│  ├─ Google Analytics                                       │
│  ├─ Sentry (Error Tracking)                                │
│  └─ New Relic (Performance)                                │
│                                                             │
│  Storage:                                                   │
│  ├─ AWS S3 / DigitalOcean Spaces (Images)                 │
│  └─ Backup Services                                        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```
