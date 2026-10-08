<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
use App\Http\Controllers\JekoWebhookController;

// ─── Public ─────────────────────────────────────────────────────────────────
Route::post('/webhooks/jeko', [JekoWebhookController::class, 'handle']);
Route::post('/auth/login',           [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/auth/register',        [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');

// Hôtels publics (lecture seule)
Route::get('/hotels',          [HotelController::class, 'index']);
Route::get('/hotels/{hotel}',  [HotelController::class, 'show']);

// Boutique publique
Route::get('/boutique/products',       [BoutiqueController::class, 'index']);
Route::get('/boutique/products/{id}',  [BoutiqueController::class, 'show']);

// ─── Authentifié ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Profil
    Route::get('/profile',           [ProfileController::class, 'show']);
    Route::put('/profile',           [ProfileController::class, 'update']);
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword']);

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
    Route::post('/reservations/{reservation}/payment',   [ReservationController::class, 'processPayment']);

    // Clients
    Route::get('/clients',           [ClientController::class, 'index']);
    Route::post('/clients',          [ClientController::class, 'store']);
    Route::get('/clients/{client}',  [ClientController::class, 'show']);
    Route::put('/clients/{client}',  [ClientController::class, 'update']);

    // Staff
    Route::get('/staff',            [StaffController::class, 'index']);
    Route::post('/staff',           [StaffController::class, 'store']);
    Route::put('/staff/{staff}',    [StaffController::class, 'update']);
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy']);
    Route::get('/staff/schedules',  [StaffController::class, 'schedules']);
    Route::post('/staff/schedules', [StaffController::class, 'storeSchedule']);

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
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);

    // Piscines
    Route::get('/pools',          [PoolController::class, 'index']);
    Route::post('/pools',         [PoolController::class, 'store']);
    Route::put('/pools/{pool}',   [PoolController::class, 'update']);

    // Tickets support
    Route::get('/tickets',             [TicketController::class, 'index']);
    Route::post('/tickets',            [TicketController::class, 'store']);
    Route::put('/tickets/{ticket}',    [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);

    // Facturations
    Route::get('/facturations',                  [FacturationController::class, 'index']);
    Route::post('/facturations',                 [FacturationController::class, 'store']);
    Route::get('/facturations/{facturation}',    [FacturationController::class, 'show']);
    Route::delete('/facturations/{facturation}', [FacturationController::class, 'destroy']);

    // Notifications
    Route::get('/notifications',                [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all',     [NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{id}/read',    [NotificationController::class, 'markRead']);
    Route::delete('/notifications/{id}',        [NotificationController::class, 'destroy']);

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

    // ─── Admin uniquement ───────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users',             [UserController::class, 'index']);
        Route::post('/users',            [UserController::class, 'store']);
        Route::put('/users/{user}',      [UserController::class, 'update']);
        Route::delete('/users/{user}',   [UserController::class, 'destroy']);

        Route::get('/logs',    [ActivityLogController::class, 'index']);
        Route::delete('/logs', [ActivityLogController::class, 'clear']);

        Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve']);
        Route::patch('/reviews/{review}/reject',  [ReviewController::class, 'reject']);
    });
});
