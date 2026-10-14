<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TwoFAController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\PoolController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\FacturationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\BoutiqueController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\PointageController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PayoutController;
use App\Http\Controllers\JekoWebhookController;

// ─── Public ─────────────────────────────────────────────────────────────────
Route::post('/webhooks/jeko', [JekoWebhookController::class, 'handle']);
// Appelé par le backend mobile, protégé par X-Internal-Key (pas par une session utilisateur) — voir JekoWebhookController::syncPayment().
Route::post('/internal/jeko/sync-payment', [JekoWebhookController::class, 'syncPayment'])->middleware('throttle:30,1');
Route::post('/auth/login',           [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/auth/register',        [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
// Suite du login quand celui-ci répond `requires_2fa` (pas encore de jeton à ce stade).
Route::post('/auth/2fa/verify', [TwoFAController::class, 'verifyLogin'])->middleware('throttle:5,1');

// Hôtels publics (lecture seule)
Route::get('/hotels',          [HotelController::class, 'index']);
Route::get('/hotels/{hotel}',  [HotelController::class, 'show']);

// Boutique publique
Route::get('/boutique/products',       [BoutiqueController::class, 'index']);
// Déclarée avant {id} pour ne pas être capturée par le paramètre (voir aussi /staff/me).
Route::get('/boutique/products/mine',  [BoutiqueController::class, 'mine'])->middleware('auth:sanctum');
Route::get('/boutique/products/{id}',  [BoutiqueController::class, 'show']);

// Abonnements — paliers publics (consultables avant inscription, cf. page tarifs)
Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans']);

// ─── Authentifié ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout',     [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/auth/me',          [AuthController::class, 'me']);

    // 2FA (activation/désactivation depuis le profil — déjà connecté)
    Route::get('/auth/2fa/setup',    [TwoFAController::class, 'setup']);
    Route::post('/auth/2fa/enable',  [TwoFAController::class, 'enable']);
    Route::post('/auth/2fa/disable', [TwoFAController::class, 'disable']);

    // Profil
    Route::get('/profile',           [ProfileController::class, 'show']);
    Route::put('/profile',           [ProfileController::class, 'update']);
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword']);
    Route::post('/profile/avatar',   [ProfileController::class, 'uploadAvatar']);
    Route::put('/profile/payout',    [ProfileController::class, 'updatePayout']);
    Route::delete('/profile',        [ProfileController::class, 'destroy']);

    // Dashboard (adapté au rôle)
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Hôtels (CRUD pour prestataire/admin)
    Route::post('/hotels',           [HotelController::class, 'store']);
    Route::put('/hotels/{hotel}',    [HotelController::class, 'update']);
    Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy']);

    // Chambres
    Route::get('/rooms',                    [RoomController::class, 'index']);
    Route::post('/rooms',                   [RoomController::class, 'store']);
    Route::patch('/rooms/{room}/status',    [RoomController::class, 'updateStatus']);
    Route::post('/rooms/{room}/cleaned',    [RoomController::class, 'markCleaned']);
    Route::delete('/rooms/{room}',          [RoomController::class, 'destroy']);

    // Réservations
    Route::get('/reservations',                          [ReservationController::class, 'index']);
    Route::post('/reservations',                         [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}',            [ReservationController::class, 'show']);
    Route::patch('/reservations/{reservation}/status',   [ReservationController::class, 'updateStatus']);
    Route::post('/reservations/{reservation}/checkin',   [ReservationController::class, 'checkIn']);
    Route::post('/reservations/{reservation}/checkout',  [ReservationController::class, 'checkOut']);
    Route::post('/reservations/{reservation}/payment',   [ReservationController::class, 'processPayment'])->middleware('throttle:20,1');
    Route::post('/reservations/{reservation}/cancel',    [ReservationController::class, 'cancel']);

    // Clients
    Route::get('/clients',               [ClientController::class, 'index']);
    Route::post('/clients',              [ClientController::class, 'store']);
    Route::get('/clients/{client}',      [ClientController::class, 'show']);
    Route::put('/clients/{client}',      [ClientController::class, 'update']);
    Route::delete('/clients/{client}',   [ClientController::class, 'destroy']);
    Route::post('/clients/{client}/points', [ClientController::class, 'addPoints']);

    // Staff
    Route::get('/staff',            [StaffController::class, 'index']);
    Route::post('/staff',           [StaffController::class, 'store']);
    Route::get('/staff/schedules',  [StaffController::class, 'schedules']);
    Route::post('/staff/schedules', [StaffController::class, 'storeSchedule']);
    Route::get('/staff/me',         [StaffController::class, 'me']); // avant {staff} pour ne pas être capturé par le paramètre
    Route::get('/staff/{staff}',    [StaffController::class, 'show']);
    Route::put('/staff/{staff}',    [StaffController::class, 'update']);
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy']);

    // Menu
    Route::get('/menu',              [MenuController::class, 'index']);
    Route::post('/menu',             [MenuController::class, 'store']);
    Route::put('/menu/{item}',       [MenuController::class, 'update']);
    Route::delete('/menu/{item}',    [MenuController::class, 'destroy']);

    // Commandes restaurant
    Route::get('/orders',            [OrderController::class, 'index']);
    Route::post('/orders',           [OrderController::class, 'store']);
    Route::put('/orders/{order}',    [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    // Charges
    Route::get('/expenses',              [ExpenseController::class, 'index']);
    Route::post('/expenses',             [ExpenseController::class, 'store']);
    Route::put('/expenses/{expense}',    [ExpenseController::class, 'update']);
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);

    // Piscines
    Route::get('/pools',          [PoolController::class, 'index']);
    Route::post('/pools',         [PoolController::class, 'store']);
    Route::put('/pools/{pool}',   [PoolController::class, 'update']);
    Route::delete('/pools/{pool}',[PoolController::class, 'destroy']);

    // Services
    Route::get('/services',                  [ServiceController::class, 'index']);
    Route::post('/services',                 [ServiceController::class, 'store']);
    Route::put('/services/{service}',        [ServiceController::class, 'update']);
    Route::patch('/services/{service}/toggle', [ServiceController::class, 'toggle']);
    Route::delete('/services/{service}',     [ServiceController::class, 'destroy']);

    // Pointage
    Route::get('/pointage/me',         [PointageController::class, 'me']);
    Route::post('/pointage/check-in',  [PointageController::class, 'checkIn']);
    Route::post('/pointage/check-out', [PointageController::class, 'checkOut']);
    Route::get('/pointage/report',     [PointageController::class, 'report']);

    // Tickets support
    Route::get('/tickets',             [TicketController::class, 'index']);
    Route::post('/tickets',            [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}',    [TicketController::class, 'show']);
    Route::put('/tickets/{ticket}',    [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);

    // Abonnements (self-service prestataire)
    Route::get('/subscriptions/mine',  [SubscriptionController::class, 'mine']);
    Route::post('/subscriptions/choose', [SubscriptionController::class, 'choose']);
    Route::post('/subscriptions/pay',  [SubscriptionController::class, 'pay']);

    // Facturations — réservé à prestataire/admin, filtrage par hôtel appliqué dans
    // Api\FacturationController::authorizeFacturation()/index() (miroir du contrôleur web).
    Route::middleware('role:prestataire')->group(function () {
        Route::get('/facturations',                  [FacturationController::class, 'index']);
        Route::post('/facturations',                 [FacturationController::class, 'store']);
        Route::get('/facturations/{facturation}',    [FacturationController::class, 'show']);
        Route::put('/facturations/{facturation}',    [FacturationController::class, 'update']);
        Route::patch('/facturations/{facturation}/pay', [FacturationController::class, 'pay']);
        Route::delete('/facturations/{facturation}', [FacturationController::class, 'destroy']);
    });

    // Notifications
    Route::get('/notifications',                [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all',     [NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{id}/read',    [NotificationController::class, 'markRead']);
    Route::delete('/notifications/{id}',        [NotificationController::class, 'destroy']);
    Route::delete('/notifications',             [NotificationController::class, 'destroyAll']);

    // Avis (reviews)
    Route::post('/hotels/{hotel}/reviews',  [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}',      [ReviewController::class, 'destroy']);

    // Favoris
    Route::get('/favorites',                  [FavoriteController::class, 'index']);
    Route::post('/hotels/{hotel}/favorite',   [FavoriteController::class, 'toggle']);

    // Boutique (panier + commandes client)
    Route::get('/boutique/cart',           [BoutiqueController::class, 'cart']);
    Route::post('/boutique/cart/{id}',     [BoutiqueController::class, 'addToCart']);
    Route::delete('/boutique/cart/{id}',   [BoutiqueController::class, 'removeFromCart']);
    Route::post('/boutique/order',         [BoutiqueController::class, 'placeOrder']);
    Route::get('/boutique/my-orders',      [BoutiqueController::class, 'myOrders']);
    Route::get('/boutique/orders/{id}/items',    [BoutiqueController::class, 'orderItems']);
    Route::patch('/boutique/orders/{id}/cancel', [BoutiqueController::class, 'cancelOrder']);

    // Boutique — gestion des produits (prestataire propriétaire ou admin, vérifié dans le contrôleur)
    // GET /boutique/products/mine est déclarée plus haut, dans le groupe public, avant {id}.
    Route::post('/boutique/products',        [BoutiqueController::class, 'storeProduct']);
    Route::put('/boutique/products/{id}',    [BoutiqueController::class, 'updateProduct']);
    Route::delete('/boutique/products/{id}', [BoutiqueController::class, 'destroyProduct']);

    // ─── Admin uniquement ───────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users',             [UserController::class, 'index']);
        Route::post('/users',            [UserController::class, 'store']);
        Route::put('/users/{user}',      [UserController::class, 'update']);
        Route::delete('/users/{user}',   [UserController::class, 'destroy']);

        Route::get('/logs',    [ActivityLogController::class, 'index']);
        Route::delete('/logs', [ActivityLogController::class, 'clear']);

        Route::get('/reviews',                    [ReviewController::class, 'adminIndex']);
        Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve']);
        Route::patch('/reviews/{review}/reject',  [ReviewController::class, 'reject']);

        Route::get('/reports',  [ReportController::class, 'index']);
        Route::get('/settings', [SettingController::class, 'index']);

        Route::get('/subscriptions',                      [SubscriptionController::class, 'adminIndex']);
        Route::post('/subscriptions/{subscription}/payment', [SubscriptionController::class, 'adminRecordPayment']);

        Route::get('/payouts',               [PayoutController::class, 'index']);
        Route::post('/payouts/{user}',       [PayoutController::class, 'recordPayout']);
        Route::post('/payouts/retry/{reservation}', [PayoutController::class, 'retryPayout']);
    });
});
