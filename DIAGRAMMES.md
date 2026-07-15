# 📊 DIAGRAMMES DE FLUX - PLATEFORME RÉSERVATION HÔTELS

## 1. Flux Global du Système

```mermaid
graph TB
    subgraph Client["👥 CLIENT (Voyageur)"]
        A["Visite le site<br/>www.hotel-ci.com"]
    end
    
    subgraph Recherche["🔍 MODULE RECHERCHE"]
        B["Filtres:<br/>Ville, Dates<br/>Prix, Personnes"]
        C["Affiche résultats<br/>avec photos et avis"]
    end
    
    subgraph Details["🏨 DÉTAILS HÔTEL"]
        D["Photos complètes<br/>Services<br/>Localisation"]
    end
    
    subgraph Reservation["📅 RÉSERVATION"]
        E["Sélection chambres<br/>Vérif dispo<br/>Calcul prix"]
    end
    
    subgraph Payment["💳 PAIEMENT"]
        F["Stripe<br/>Orange Money<br/>MTN Money"]
    end
    
    subgraph Confirmation["✅ CONFIRMATION"]
        G["Email<br/>Facture PDF<br/>N° Réservation"]
    end
    
    subgraph Review["⭐ AVIS"]
        H["Évaluation<br/>Commentaire<br/>Photos"]
    end
    
    A --> B --> C --> D --> E --> F --> G --> H
```

## 2. Système de Rôles et Permissions

```mermaid
graph TD
    Users["👥 UTILISATEURS"]
    
    Users -->|Role| Admin["👨‍💼 ADMIN"]
    Users -->|Role| Prestataire["🏨 PRESTATAIRE<br/>(Hôtelier)"]
    Users -->|Role| Client["✈️ CLIENT<br/>(Voyageur)"]
    
    Admin -->|Permissions| AP1["Gérer users"]
    Admin -->|Permissions| AP2["Voir rapports"]
    Admin -->|Permissions| AP3["Paramètres"]
    Admin -->|Permissions| AP4["Modérer avis"]
    
    Prestataire -->|Permissions| PP1["Gérer hôtels"]
    Prestataire -->|Permissions| PP2["Gérer chambres"]
    Prestataire -->|Permissions| PP3["Voir réservations"]
    Prestataire -->|Permissions| PP4["Voir revenus"]
    
    Client -->|Permissions| CP1["Chercher hôtels"]
    Client -->|Permissions| CP2["Réserver"]
    Client -->|Permissions| CP3["Payer"]
    Client -->|Permissions| CP4["Laisser avis"]
```

## 3. Architecture MVC

```mermaid
graph LR
    subgraph Models["📦 MODELS<br/>(Eloquent ORM)"]
        M1["User<br/>Hotel<br/>Room"]
        M2["Reservation<br/>Payment<br/>Review"]
    end
    
    subgraph Views["🎨 VIEWS<br/>(Blade Templates)"]
        V1["Layouts<br/>Components<br/>Pages"]
        V2["Dashboards<br/>Forms<br/>Lists"]
    end
    
    subgraph Controllers["⚙️ CONTROLLERS<br/>(Business Logic)"]
        C1["Auth<br/>Home<br/>Hotel"]
        C2["Reservation<br/>Payment<br/>Review"]
    end
    
    subgraph Services["🔧 SERVICES<br/>(Complex Logic)"]
        S1["ReservationService<br/>PaymentService"]
        S2["SearchService<br/>ReviewService"]
    end
    
    User["🌐 USER<br/>HTTP Request"]
    
    User -->|Request| Controllers
    Controllers -->|Query| Models
    Controllers -->|Call| Services
    Services -->|Query| Models
    Models -->|Data| Views
    Views -->|Response| User
```

## 4. Flux de Paiement

```mermaid
graph TD
    A["Client termine<br/>la réservation"]
    A -->|Clique: Payer| B["Page de paiement<br/>affichée"]
    
    B -->|Choisit: Carte| C1["Stripe"]
    B -->|Choisit: Orange| C2["Orange Money API"]
    B -->|Choisit: MTN| C3["MTN Money API"]
    B -->|Choisit: Virement| C4["Détails bancaires"]
    
    C1 -->|Tokenize| D1["Paiement sécurisé"]
    C2 -->|USSD| D2["Confirmation SMS"]
    C3 -->|USSD| D2
    C4 -->|Attente| D3["Manuel (Admin)"]
    
    D1 -->|Success| E["Webhook reçu"]
    D2 -->|Success| E
    D3 -->|Confirmé| E
    
    E --> F["✅ Réservation confirmée"]
    E --> G["📧 Email envoyé"]
    E --> H["📱 SMS envoyé"]
    E --> I["💾 Facture créée"]
    
    F --> J["Client reçoit<br/>confirmation"]
```

## 5. Disponibilité des Chambres

```mermaid
graph TD
    A["Room"]
    
    A -->|hasMany| B["RoomAvailability"]
    
    B -->|Date: 2024-05-20| C1["Available: True<br/>Price: 50.000 XOF"]
    B -->|Date: 2024-05-21| C2["Available: False<br/>Price: -"]
    B -->|Date: 2024-05-22| C3["Available: True<br/>Price: 60.000 XOF<br/>(Tarif dynamique)"]
    
    D["Calendrier affichage"]
    
    C1 -->|Affiche vert| D
    C2 -->|Affiche rouge| D
    C3 -->|Affiche jaune| D
```

## 6. Workflow Complet Réservation

```mermaid
sequenceDiagram
    participant Client as 👥 Client
    participant Web as 🌐 Website
    participant API as ⚙️ Backend API
    participant DB as 💾 Database
    participant Payment as 💳 Paiement
    participant Email as 📧 Email Service
    
    Client->>Web: Cherche hôtels
    Web->>API: GET /search?city=Abidjan&date=2024-05-20
    API->>DB: Query hotels & availability
    DB-->>API: Résultats
    API-->>Web: JSON résultats
    Web-->>Client: Affiche résultats
    
    Client->>Web: Clique hôtel
    Web-->>Client: Affiche détails + tarifs
    
    Client->>Web: Formulaire réservation
    Web->>API: POST /reservations
    API->>DB: Vérifier disponibilité
    DB-->>API: OK (chambre libre)
    API->>DB: Créer réservation (statut: pending)
    DB-->>API: ID réservation: #12345
    API-->>Web: Réservation créée
    Web-->>Client: Redirect paiement
    
    Client->>Payment: Saisit données bancaire
    Payment->>Payment: Traite paiement
    Payment-->>Web: Webhook: payment.success
    Web->>API: POST /payments/confirm
    API->>DB: Update réservation (confirmed)
    API->>Email: Queue email confirmation
    Email-->>Client: ✅ Réservation confirmée!
    
    Client->>Web: Reçoit email avec facture PDF
```

## 7. Structure Base de Données

```mermaid
erDiagram
    USERS ||--o{ HOTELS : owns
    USERS ||--o{ RESERVATIONS : makes
    USERS ||--o{ REVIEWS : writes
    HOTELS ||--o{ ROOMS : has
    ROOMS ||--o{ RESERVATIONS : occupiedBy
    ROOMS ||--o{ ROOM_AVAILABILITIES : schedules
    RESERVATIONS ||--o{ PAYMENTS : pays
    HOTELS ||--o{ REVIEWS : receives
    USERS ||--o{ FAVORITES : marks
    HOTELS ||--o{ FAVORITES : markedBy

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin|prestataire|client"
        datetime created_at
    }

    HOTELS {
        bigint id PK
        bigint user_id FK
        string name
        text description
        string city
        decimal latitude
        decimal longitude
        string image_url
        datetime created_at
    }

    ROOMS {
        bigint id PK
        bigint hotel_id FK
        string number
        enum room_type "single|double|suite"
        int capacity
        decimal price_per_night
        datetime created_at
    }

    RESERVATIONS {
        bigint id PK
        bigint user_id FK
        bigint room_id FK
        date check_in_date
        date check_out_date
        int number_of_guests
        decimal total_price
        enum status "pending|confirmed|cancelled"
        datetime created_at
    }

    PAYMENTS {
        bigint id PK
        bigint reservation_id FK
        decimal amount
        enum method "stripe|orange|mtn"
        enum status "success|failed|pending"
        datetime created_at
    }

    REVIEWS {
        bigint id PK
        bigint user_id FK
        bigint hotel_id FK
        int rating "1-5"
        text comment
        enum status "pending|approved"
        datetime created_at
    }

    FAVORITES {
        bigint id PK
        bigint user_id FK
        bigint hotel_id FK
    }

    ROOM_AVAILABILITIES {
        bigint id PK
        bigint room_id FK
        date date
        boolean is_available
        decimal price_override
    }
```

## 8. Sécurité - Flux d'Authentification

```mermaid
graph TD
    A["Utilisateur accède<br/>page protégée"]
    A -->|Sans token| B["❌ Redirection login"]
    A -->|Avec token| C{Token<br/>valide?}
    
    C -->|Expiré| D["🔄 Refresh token"]
    C -->|Invalide| B
    C -->|Valide| E{Rôle<br/>autorisé?}
    
    D -->|OK| E
    D -->|Fail| B
    
    E -->|Non| F["❌ Error 403<br/>Accès refusé"]
    E -->|Oui| G{Permissions<br/>spécifiques?}
    
    G -->|Non| F
    G -->|Oui| H["✅ Accès autorisé<br/>Affiche page"]
    
    B --> I["Page login<br/>user saisit credentials"]
    I --> J["Valider email/password"]
    J -->|Fail| K["❌ Identifiants<br/>invalides"]
    J -->|OK| L["✅ Login réussi<br/>Token créé"]
    
    K --> I
    L --> H
```

## 9. Process Avis et Modération

```mermaid
graph TD
    A["Client termine séjour"]
    A -->|Reçoit email| B["Lien: Laisser avis"]
    B --> C["Formulaire avis"]
    C -->|Rating + Comment| D["Soumis<br/>(Status: pending)"]
    
    D --> E["⏳ Modérateur examine"]
    E -->|Contains spam/insulte| F["❌ Rejeté"]
    E -->|OK| G["✅ Approuvé"]
    
    F --> H["Supprimé"]
    G --> I["Affiché sur site"]
    
    I --> J["😊 Rating visible<br/>Compte dans moyenne"]
    
    G --> K["🔔 Notification<br/>au propriétaire"]
    K --> L["Propriétaire peut<br/>répondre"]
    L --> M["Réponse affichée<br/>avec avis"]
```

## 10. Calendrier Disponibilité

```mermaid
graph LR
    A["Room #101"]
    
    A -->|Mai 20| B1["✅ Disponible<br/>50.000 XOF"]
    A -->|Mai 21| B2["❌ Réservée"]
    A -->|Mai 22| B3["✅ Disponible<br/>60.000 XOF"]
    A -->|Mai 23| B4["🔧 Maintenance"]
    A -->|Mai 24| B5["✅ Disponible<br/>50.000 XOF"]
    A -->|Mai 25| B6["✅ Disponible<br/>50.000 XOF"]
    A -->|Mai 26| B7["❌ Réservée"]
    
    B1 -->|Réservation clique| C1["Ajouter au panier"]
    B2 -->|Occupée| C2["Indisponible"]
    B3 -->|Clic| C3["Ajouter au panier"]
    B4 -->|En maintenance| C4["Non disponible"]
    
    C1 --> D["Voir prix total<br/>Procéder paiement"]
    C3 --> D
```

## 11. Notifications Multi-Canal

```mermaid
graph TD
    Event["📌 Événement<br/>(Reservation created)"]
    
    Event --> Queue["📤 Queue Notification"]
    Queue -->|Worker| N1["📧 Email"]
    Queue -->|Worker| N2["📱 SMS"]
    Queue -->|Worker| N3["🔔 Push"]
    
    N1 -->|SendGrid| C1["Email sent<br/>to client"]
    N1 -->|SendGrid| P1["Email sent<br/>to owner"]
    
    N2 -->|Twilio| C2["SMS sent<br/>to client"]
    N2 -->|Twilio| P2["SMS sent<br/>to owner"]
    
    N3 -->|Firebase| C3["Push notification<br/>App client"]
    N3 -->|Firebase| P3["Push notification<br/>App owner"]
    
    C1 --> D1["✅ Confirmation"]
    C2 --> D2["✅ Confirmation"]
    P1 --> D3["✅ New reservation"]
    P2 --> D4["✅ New reservation"]
```

## 12. Dashboard Prestataire (Propriétaire)

```mermaid
graph TB
    Dashboard["📊 DASHBOARD PRESTATAIRE"]
    
    Dashboard --> Stats["📈 STATISTIQUES"]
    Stats --> S1["Revenue Total"]
    Stats --> S2["Occupancy Rate %"]
    Stats --> S3["Avg Rating ⭐"]
    Stats --> S4["Total Guests"]
    
    Dashboard --> Reservations["📅 RÉSERVATIONS"]
    Reservations --> R1["Confirmées"]
    Reservations --> R2["En attente"]
    Reservations --> R3["Annulées"]
    
    Dashboard --> Management["⚙️ GESTION"]
    Management --> M1["Gérer hôtels"]
    Management --> M2["Gérer chambres"]
    Management --> M3["Gérer tarifs"]
    Management --> M4["Gérer équipe"]
    
    Dashboard --> Analytics["📊 ANALYTICS"]
    Analytics --> A1["Revenue par mois"]
    Analytics --> A2["Occupancy trend"]
    Analytics --> A3["Guest feedback"]
    Analytics --> A4["Reports PDF"]
```

---

**Ces diagrammes vous aident à visualiser l'architecture complète! 📊**
