# 🚀 PHASE 1 - FONDAMENTAUX AMÉLIORÉS (Semaine 1-2)

## Vue d'ensemble de la Phase 1

La Phase 1 se concentre sur la consolidation et l'amélioration de la base existante. C'est fondamental pour une plateforme robuste et sécurisée.

### Objectifs
- ✅ Renforcer la sécurité
- ✅ Implémenter les validations
- ✅ Gérer les images
- ✅ Améliorer la base de données
- ✅ Créer des tests

---

## 1️⃣ TÂCHE 1: SYSTÈME DE VALIDATIONS AVANCÉES

### 1.1 Créer les FormRequests

```bash
# Générer les FormRequest
php artisan make:request StoreHotelRequest
php artisan make:request UpdateHotelRequest
php artisan make:request StoreReservationRequest
php artisan make:request UpdateReservationRequest
php artisan make:request StoreRoomRequest
php artisan make:request StoreReviewRequest
php artisan make:request StorePaymentRequest
```

### 1.2 Implémenter les FormRequests

Créer `app/Http/Requests/StoreHotelRequest.php`:
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'prestataire';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:hotels,name',
            'description' => 'required|string|min:20|max:2000',
            'phone' => 'required|regex:/^[0-9\-\+\s\(\)]{10,}$/',
            'email' => 'required|email|unique:hotels,email',
            'address' => 'required|string|min:5|max:255',
            'city' => 'required|string|in:Abidjan,Yamoussoukro,Bouaké,Daloa,Korhogo',
            'country' => 'required|string|in:Côte d\'Ivoire',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i|after:check_in_time',
            'services' => 'nullable|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom de l\'hôtel est requis',
            'name.unique' => 'Ce nom d\'hôtel existe déjà',
            'email.email' => 'Veuillez entrer une adresse email valide',
            'phone.regex' => 'Le numéro de téléphone n\'est pas valide',
            'city.in' => 'Veuillez sélectionner une ville valide',
        ];
    }
}
```

### 1.3 Utiliser dans les Contrôleurs

Modifier `DashboardController::storeHotel()`:
```php
public function storeHotel(StoreHotelRequest $request)
{
    // Données validées automatiquement
    $validated = $request->validated();
    
    // Créer l'hôtel
    $hotel = $this->user()->hotels()->create($validated);
    
    // Uploader l'image
    if ($request->hasFile('image')) {
        $hotel->saveImage($request->file('image'));
    }
    
    return redirect()->route('hotels.show', $hotel)
                    ->with('success', 'Hôtel créé avec succès');
}
```

---

## 2️⃣ TÂCHE 2: GESTION DES IMAGES

### 2.1 Créer le Trait ImageUpload

Créer `app/Traits/HasImages.php`:
```php
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait HasImages
{
    /**
     * Uploader et sauvegarder une image
     */
    public function uploadImage($image, $path = 'images')
    {
        if (!$image) {
            return null;
        }

        // Générer un nom unique
        $filename = Str::slug($this->name) . '-' . time() . '.' . $image->extension();
        
        // Créer le dossier s'il n'existe pas
        $folder = $path . '/' . class_basename($this);
        
        // Uploader l'image
        $path = Storage::disk('public')->putFileAs(
            $folder,
            $image,
            $filename
        );
        
        // Compresser l'image
        $this->compressImage(storage_path('app/public/' . $path));
        
        return $path;
    }

    /**
     * Compresser l'image
     */
    private function compressImage($imagePath)
    {
        $image = Image::read($imagePath);
        
        // Redimensionner si trop grand
        if ($image->width() > 1920) {
            $image->scaleDown(width: 1920);
        }
        
        // Compresser
        $image->toJpeg(quality: 80)->save($imagePath);
    }

    /**
     * Obtenir l'URL de l'image
     */
    public function getImageUrl()
    {
        return $this->image_url ? asset('storage/' . $this->image_url) : null;
    }

    /**
     * Supprimer l'image
     */
    public function deleteImage()
    {
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            Storage::disk('public')->delete($this->image_url);
            $this->update(['image_url' => null]);
        }
    }
}
```

### 2.2 Ajouter aux Modèles

Modifier `app/Models/Hotel.php`:
```php
<?php

namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasImages;
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'check_in_time',
        'check_out_time',
        'services',
        'image_url',
        'status',
    ];

    protected $casts = [
        'services' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
```

---

## 3️⃣ TÂCHE 3: MIGRATION ET MODÈLES AMÉLIORÉS

### 3.1 Créer la Migration pour Images

```bash
php artisan make:migration add_image_columns_to_hotels
php artisan make:migration add_image_columns_to_rooms
```

`database/migrations/[timestamp]_add_image_columns_to_hotels.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('services');
            $table->softDeletes(); // Pour soft delete
            $table->index('user_id');
            $table->index('status');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['image_url']);
            $table->dropSoftDeletes();
            $table->dropIndex(['user_id', 'status', 'city']);
        });
    }
};
```

### 3.2 Améliorer le Modèle User

Modifier `app/Models/User.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Hotel::class, 'favorites');
    }

    // Scopes
    public function scopePrestataires($query)
    {
        return $query->where('role', 'prestataire');
    }

    public function scopeClients($query)
    {
        return $query->where('role', 'client');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPrestataire()
    {
        return $this->role === 'prestataire';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function hasHotels()
    {
        return $this->hotels()->exists();
    }
}
```

---

## 4️⃣ TÂCHE 4: MIDDLEWARE D'AUTORISATION

### 4.1 Créer le Middleware CheckRole

```bash
php artisan make:middleware CheckRole
```

`app/Http/Middleware/CheckRole.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}
```

### 4.2 Enregistrer dans Kernel

Modifier `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... existing middleware ...
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

### 4.3 Utiliser dans les Routes

Les routes dans `routes/web.php` utilisent déjà:
```php
Route::middleware('role:prestataire')->group(function () {
    // Routes pour propriétaires
});

Route::middleware('role:client')->group(function () {
    // Routes pour clients
});

Route::middleware('role:admin')->group(function () {
    // Routes pour admin
});
```

---

## 5️⃣ TÂCHE 5: SOFT DELETES ET AUDIT

### 5.1 Créer le Trait Loggable

Créer `app/Traits/Loggable.php`:
```php
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait Loggable
{
    public static function bootLoggable()
    {
        static::created(function ($model) {
            Log::info("Model created: {$model->getMorphClass()} #{$model->id}", [
                'user_id' => auth()->id(),
                'data' => $model->getAttributes(),
            ]);
        });

        static::updated(function ($model) {
            Log::info("Model updated: {$model->getMorphClass()} #{$model->id}", [
                'user_id' => auth()->id(),
                'changes' => $model->getChanges(),
            ]);
        });

        static::deleted(function ($model) {
            Log::info("Model deleted: {$model->getMorphClass()} #{$model->id}", [
                'user_id' => auth()->id(),
            ]);
        });
    }
}
```

### 5.2 Ajouter à tous les Modèles

Modifier chaque modèle (Hotel, Room, Reservation, etc.):
```php
use App\Traits\Loggable;

class Hotel extends Model
{
    use HasFactory, HasImages, Loggable, SoftDeletes;
}
```

---

## 6️⃣ TÂCHE 6: SEEDERS DE TEST

### 6.1 Créer les Seeders

```bash
php artisan make:seeder UserSeeder
php artisan make:seeder HotelSeeder
php artisan make:seeder RoomSeeder
php artisan make:seeder DatabaseSeeder
```

### 6.2 Implémenter UserSeeder

Créer `database/seeders/UserSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@hotel.ci',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+225 01 23 45 67 89',
        ]);

        // Prestataire (Hôtelier)
        User::create([
            'name' => 'Jean Proprietaire',
            'email' => 'proprietaire@hotel.ci',
            'password' => Hash::make('password'),
            'role' => 'prestataire',
            'status' => 'active',
            'phone' => '+225 01 23 45 67 90',
            'city' => 'Abidjan',
        ]);

        // Clients
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Client {$i}",
                'email' => "client{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'client',
                'status' => 'active',
                'phone' => "+225 0" . rand(1, 9) . " " . rand(10, 99) . " " . rand(10, 99) . " " . rand(10, 99),
            ]);
        }
    }
}
```

### 6.3 Exécuter les Seeders

```bash
# Migrer la base de données
php artisan migrate --fresh

# Charger les seeders
php artisan db:seed
```

---

## 7️⃣ TÂCHE 7: TESTS UNITAIRES

### 7.1 Créer les Tests

```bash
php artisan make:test Http/Controllers/AuthControllerTest
php artisan make:test Http/Controllers/HotelControllerTest
php artisan make:test Models/UserTest
php artisan make:test Models/HotelTest
```

### 7.2 Tester l'Authentification

Créer `tests/Feature/Http/Controllers/AuthControllerTest.php`:
```php
<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'client',
        ]);

        $response->assertStatus(302)
                ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'client',
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302)->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertStatus(302)->assertRedirect('/');
        $this->assertGuest();
    }
}
```

### 7.3 Lancer les Tests

```bash
php artisan test
```

---

## 📋 CHECKLIST PHASE 1

```
SÉCURITÉ ET VALIDATIONS
- [ ] Créer tous les FormRequest
- [ ] Implémenter middleware CheckRole
- [ ] Valider tous les inputs
- [ ] Hasher les mots de passe
- [ ] Ajouter CSRF tokens

GESTION DES IMAGES
- [ ] Créer trait HasImages
- [ ] Implémenter upload d'images
- [ ] Ajouter compression
- [ ] Créer dossiers de stockage
- [ ] Configurer Storage

MODÈLES ET MIGRATIONS
- [ ] Ajouter softDeletes
- [ ] Ajouter timestamps
- [ ] Améliorer relations
- [ ] Ajouter indexes
- [ ] Créer migrations manquantes

AUDITS ET LOGS
- [ ] Créer trait Loggable
- [ ] Ajouter à tous les modèles
- [ ] Configurer fichiers logs
- [ ] Implémenter audit trail

SEEDERS ET DONNÉES TEST
- [ ] Créer UserSeeder
- [ ] Créer HotelSeeder
- [ ] Créer RoomSeeder
- [ ] Générer données de test

TESTS
- [ ] Tests authentification
- [ ] Tests autorisation
- [ ] Tests validation
- [ ] Tests modèles
- [ ] Coverage > 80%

DOCUMENTATION
- [ ] Documenter migrations
- [ ] Documenter FormRequest
- [ ] Documenter Services
- [ ] Documenter API interne
```

---

## 🎯 Commandes Essentielles

```bash
# Générer ressources
php artisan make:model Hotel -m -c -r

# Lancer serveur de développement
php artisan serve

# Lancer tests
php artisan test

# Vérifier code
./vendor/bin/pint

# Migrations
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh
php artisan migrate:fresh --seed

# Seeders
php artisan db:seed
php artisan db:seed --class=UserSeeder

# Logs
php artisan tail
```

---

**Prêt à commencer la Phase 1 ?** ✨

Les fichiers de configuration et bases sont en place. Voulez-vous que je commence à implémenter ces tâches une par une ?
