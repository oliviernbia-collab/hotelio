# 🎯 GUIDE DE DÉMARRAGE - PHASE 1

Ce guide vous aide à démarrer l'implémentation de la Phase 1 de manière systématique.

---

## ✅ PRÉ-REQUIS

Avant de commencer, assurez-vous d'avoir:

```bash
# PHP 8.2+
php -v

# Composer
composer -v

# Node.js & npm
node -v
npm -v

# Laravel installed
php artisan --version
```

---

## 🔧 CONFIGURATION INITIALE

### 1. Fichier `.env`

Créez ou mettez à jour `.env`:

```env
APP_NAME="Réservation Hôtels CI"
APP_ENV=local
APP_KEY=base64:your_generated_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservation_hotel_ci
DB_USERNAME=root
DB_PASSWORD=

# Email (pour développement)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@hotel.ci
MAIL_FROM_NAME="Réservation Hôtels"

# Redis (optionnel)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Services de paiement (à configurer plus tard)
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=

# Tokens API (à ajouter plus tard)
GOOGLE_MAPS_API_KEY=
TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_PHONE_NUMBER=
```

### 2. Générer la clé de l'application

```bash
php artisan key:generate
```

### 3. Publier les assets

```bash
php artisan storage:link
```

---

## 📥 INSTALLATION DES DÉPENDANCES

### PHP/Composer

```bash
# Installer les dépendances PHP
composer install

# Mettre à jour les autoloads
composer dump-autoload
```

### Node.js/npm

```bash
# Installer les dépendances Node
npm install

# Compiler les assets
npm run dev

# Ou build pour production
npm run build
```

---

## 🗄️ BASE DE DONNÉES

### Créer la base de données

```bash
# MySQL
mysql -u root -p
> CREATE DATABASE reservation_hotel_ci CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> EXIT;

# Ou via phpMyAdmin (créer la base via l'interface)
```

### Migrer la base

```bash
# Exécuter toutes les migrations
php artisan migrate

# Ou réinitialiser (attention: supprime les données!)
php artisan migrate:fresh

# Avec seeders
php artisan migrate:fresh --seed
```

### Vérifier les migrations

```bash
# Voir l'état
php artisan migrate:status

# Voir les migrations en attente
php artisan migrate --pretend
```

---

## 🚀 LANCER L'APPLICATION

### Option 1: Serveur Artisan (Développement)

```bash
# Terminal 1: Serveur PHP
php artisan serve

# Terminal 2: Watcher Vite (pour CSS/JS)
npm run dev

# Terminal 3: Queue worker (pour jobs)
php artisan queue:listen

# Terminal 4: Logs en temps réel
php artisan pail
```

L'app sera accessible à: `http://localhost:8000`

### Option 2: Tout en Une Commande

```bash
# Utilise le script du composer.json
composer run dev
```

---

## 🧪 TESTS

### Lancer tous les tests

```bash
php artisan test
```

### Lancer des tests spécifiques

```bash
# Un seul fichier de test
php artisan test tests/Feature/Http/Controllers/AuthControllerTest.php

# Un seul test
php artisan test --filter=user_can_login

# Avec verbosité
php artisan test -v

# Avec coverage (nécessite PHP Xdebug)
php artisan test --coverage
```

### Créer un nouveau test

```bash
# Test Feature (teste les routes et contrôleurs)
php artisan make:test Feature/ReservationTest

# Test Unit (teste les modèles et services)
php artisan make:test Unit/Models/UserTest --unit

# Test avec mock
php artisan make:test Feature/PaymentTest
```

---

## 📦 GESTION DES PAQUETS

### Ajouter une nouvelle dépendance

```bash
# PHP
composer require vendor/package

# JavaScript
npm install package-name
```

### Mettre à jour les dépendances

```bash
# PHP (avec cautèle)
composer update

# JavaScript
npm update

# Audit de sécurité
composer audit
npm audit
```

---

## 🛠️ COMMANDES ESSENTIELLES LARAVEL

### Génération de ressources

```bash
# Modèle + Migration + Contrôleur + Seeder
php artisan make:model Review -m -c -s

# Request de validation
php artisan make:request StoreReviewRequest

# Service
php artisan make:class Services/ReviewService

# Trait
php artisan make:class Traits/HasReviews

# Middleware
php artisan make:middleware CheckPermission

# Event
php artisan make:event ReservationCreated

# Listener
php artisan make:listener SendReservationEmail --event=ReservationCreated

# Mail Notification
php artisan make:notification ReservationConfirmed

# Seeder
php artisan make:seeder ReviewSeeder

# Factory
php artisan make:factory ReviewFactory --model=Review

# Job (pour queue)
php artisan make:job ProcessPayment
```

### Cache et Configuration

```bash
# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Ou tout nettoyer
php artisan optimize:clear

# Mettre en cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Base de données

```bash
# Rollback dernière migration
php artisan migrate:rollback

# Rollback tout
php artisan migrate:reset

# Rollback + Migrate
php artisan migrate:refresh

# Rollback + Migrate + Seed
php artisan migrate:fresh --seed

# Créer une migration
php artisan make:migration add_price_to_rooms_table

# Voir les migrations
php artisan migrate:status
```

### Tinker (REPL)

```bash
php artisan tinker

# Puis dans la console:
>>> $user = User::first();
>>> $user->name;
>>> User::count();
>>> User::factory(10)->create();
```

---

## 🔍 DEBUGGING

### Utiliser dd() et dump()

```php
// dd() = dump + die
dd($variable);

// dump() = affiche et continue
dump($variable);

// Multiple
dd($var1, $var2, $var3);
```

### Utiliser Tinker

```bash
php artisan tinker

# Tester des requêtes
>>> $users = User::all();
>>> $users->count();
>>> $users->first();
```

### Logs

```php
// Dans le code
Log::debug('Message', ['variable' => $value]);
Log::info('User login', ['user_id' => $user->id]);
Log::error('Error occurred', ['exception' => $e]);

// Voir les logs en direct
php artisan pail

// Ou consulter le fichier
tail -f storage/logs/laravel.log
```

### Fichier `.log` Laravel

```bash
# Consulter le dernier log
tail -f storage/logs/laravel.log

# Les 100 dernières lignes
tail -100 storage/logs/laravel.log

# Rechercher une erreur
grep "Error" storage/logs/laravel.log
```

---

## 🎨 ASSETS FRONTEND

### Compiler pendant le développement

```bash
npm run dev
```

### Compiler pour production

```bash
npm run build
```

### Ajouter un package CSS/JS

```bash
# Par exemple, ajouter Alpine.js (déjà dans le projet)
npm install alpinejs

# Dans resources/js/app.js:
import Alpine from 'alpinejs'
Alpine.start()
```

---

## 📋 WORKFLOW TYPIQUE POUR UNE FEATURE

### 1. Créer la migration

```bash
php artisan make:migration create_reviews_table
```

Éditer la migration dans `database/migrations/`:
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hotel_id')->constrained();
    $table->foreignId('user_id')->constrained();
    $table->integer('rating')->min(1)->max(5);
    $table->text('comment');
    $table->timestamps();
});
```

### 2. Créer le modèle

```bash
php artisan make:model Review -m
```

Éditer `app/Models/Review.php`:
```php
class Review extends Model
{
    protected $fillable = ['hotel_id', 'user_id', 'rating', 'comment'];
    
    public function hotel() { return $this->belongsTo(Hotel::class); }
    public function user() { return $this->belongsTo(User::class); }
}
```

### 3. Créer le contrôleur

```bash
php artisan make:controller ReviewController -r
```

Éditer `app/Http/Controllers/ReviewController.php`:
```php
class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request) { ... }
    public function index() { ... }
}
```

### 4. Créer la validation

```bash
php artisan make:request StoreReviewRequest
```

Éditer `app/Http/Requests/StoreReviewRequest.php`:
```php
public function rules()
{
    return [
        'rating' => 'required|integer|between:1,5',
        'comment' => 'required|string|min:10',
    ];
}
```

### 5. Ajouter les routes

Éditer `routes/web.php`:
```php
Route::resource('reviews', ReviewController::class);
```

### 6. Migrer

```bash
php artisan migrate
```

### 7. Tester

```bash
php artisan make:test Feature/ReviewTest
php artisan test tests/Feature/ReviewTest.php
```

---

## 🔐 BONNES PRATIQUES

### Validations
- ✅ Toujours utiliser FormRequest
- ✅ Valider côté client ET serveur
- ✅ Messages d'erreur clairs

### Sécurité
- ✅ Toujours utiliser `@csrf` dans les formulaires
- ✅ Utiliser l'authentification Laravel
- ✅ Vérifier les permissions avec middleware
- ✅ Ne jamais faire confiance aux données utilisateur

### Performance
- ✅ Utiliser les relations Eager Loading (with())
- ✅ Indexer les colonnes fréquemment interrogées
- ✅ Mettre en cache les requêtes longues
- ✅ Utiliser la pagination pour les listes longues

### Code
- ✅ Suivre PSR-12 (Laravel Pint)
- ✅ Ajouter des tests
- ✅ Documenter le code complexe
- ✅ Utiliser des types stricts (PHP 8)

---

## 🔄 DÉPLOIEMENT LOCAL (XAMPP)

Votre projet est dans `C:\xampp\htdocs\autres2\`

### Démarrer XAMPP

```bash
# Démarrer Apache et MySQL dans XAMPP
# Ouvrir le Control Panel et cliquer "Start" pour Apache et MySQL
```

### Accéder à l'app

```
http://localhost/autres2/public
```

### Installer Composer localement

Si Composer n'est pas dans le PATH:

```bash
# Télécharger Composer: https://getcomposer.org/download/
# Puis dans le dossier du projet:
php composer.phar install
```

---

## 🐛 TROUBLESHOOTING

### Problem: `Class not found`

**Solution**: Régénérer l'autoloader
```bash
composer dump-autoload
```

### Problem: `SQLSTATE[HY000]: General error`

**Solution**: Réinitialiser la base
```bash
php artisan migrate:fresh
```

### Problem: `View not found`

**Solution**: Vérifier le chemin dans le contrôleur
```php
return view('pages.home'); // Correspond à resources/views/pages/home.blade.php
```

### Problem: `npm run dev` ne fonctionne pas

**Solution**: Réinstaller les dépendances
```bash
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Problem: Port 8000 déjà utilisé

**Solution**: Utiliser un autre port
```bash
php artisan serve --port=8001
```

---

## 📚 RESSOURCES UTILES

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Laravel Best Practices](https://laravel.com/docs/12.x#optimization)
- [Eloquent ORM](https://laravel.com/docs/12.x/eloquent)
- [Blade Templates](https://laravel.com/docs/12.x/blade)
- [PHP 8 Features](https://www.php.net/manual/en/language.types.declarations.php)
- [TailwindCSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)

---

## ✅ CHECKLIST AVANT DE DÉMARRER

- [ ] `.env` configuré correctement
- [ ] Base de données créée
- [ ] `composer install` exécuté
- [ ] `npm install` exécuté
- [ ] `php artisan key:generate` exécuté
- [ ] `php artisan migrate` exécuté
- [ ] `npm run dev` tourne
- [ ] `php artisan serve` tourne
- [ ] Application accessible sur `http://localhost:8000`
- [ ] Tests passent: `php artisan test`
- [ ] Pas d'erreurs dans `php artisan pail`

---

**Prêt à développer ? 🚀**

Commencez par la tâche 1 de [PHASE_1_DETAILS.md](PHASE_1_DETAILS.md)!
