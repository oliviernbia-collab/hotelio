<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TwoFAController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FacturationController;
use App\Http\Controllers\PageController;

// ─── Changement de langue ────────────────────────────────────────────────────
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en', 'es'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('locale.switch');

// ─── Page d'accueil publique ────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'premiere'])->name('premiere');
Route::post('/premiere', [HomeController::class, 'premierePost'])->name('premiere.post');
Route::get('/hotels-en-ligne', [HomeController::class, 'hotelSearch'])->name('hotels.public');

// ─── Pages d'information (footer) ──────────────────────────────────────────
Route::get('/page/{slug}', [PageController::class, 'show'])
    ->whereIn('slug', ['centre-aide', 'securite', 'conditions-utilisation', 'confidentialite', 'ressources-hotes', 'forum', 'assistance-hotes'])
    ->name('pages.show');

// ─── Boutique publique ───────────────────────────────────────────────────────
Route::get('/boutique', [BoutiqueController::class, 'boutique'])->name('boutique.clientboutique');
Route::post('/panier/add/{id}', [BoutiqueController::class, 'add'])->name('panier.add');
Route::post('/panier/decrease/{id}', [BoutiqueController::class, 'decrease'])->name('panier.decrease');
Route::get('/produits/{id}', [BoutiqueController::class, 'show'])->name('show');

// ─── 2FA ─────────────────────────────────────────────────────────────────────
Route::prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/verify',  [TwoFAController::class, 'showVerify'])->name('verify');
    Route::post('/verify', [TwoFAController::class, 'verify'])->name('verify.post')->middleware('throttle:5,1');
});
Route::middleware('auth')->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/setup',   [TwoFAController::class, 'showSetup'])->name('setup');
    Route::post('/enable', [TwoFAController::class, 'enable'])->name('enable');
    Route::post('/disable',[TwoFAController::class, 'disable'])->name('disable');
});

// ─── Authentification ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post')->middleware('throttle:5,1');

    // Réinitialisation du mot de passe
    Route::get('/forgot-password',     [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',    [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',     [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ─── Zone authentifiée ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard général
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile.index');

    // ─── Notifications ───────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                              [NotificationController::class, 'index'])      ->name('index');
        Route::get('/fetch',                         [NotificationController::class, 'fetch'])      ->name('fetch');
        Route::patch('/{id}/read',                   [NotificationController::class, 'markRead'])   ->name('read');
        Route::patch('/read-all',                    [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::delete('/{id}',                       [NotificationController::class, 'destroy'])    ->name('destroy');
        Route::delete('/',                           [NotificationController::class, 'destroyAll']) ->name('destroy-all');
    });
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Fiche hôtel (visible par tous les rôles connectés — booking)
    Route::get('/hotels/{hotel}', [DashboardController::class, 'hotel'])->name('hotels.show');

    // Paiement & facture (accessibles à tous les rôles connectés)
    Route::get('/reservations/{reservation}/paiement', [DashboardController::class, 'payment'])->name('reservations.payment');
    Route::post('/reservations/{reservation}/paiement', [DashboardController::class, 'processPayment'])->name('reservations.payment.process');
    Route::get('/reservations/{reservation}/facture', [DashboardController::class, 'invoice'])->name('reservations.invoice');
    Route::get('/reservations/{reservation}/confirmation', [DashboardController::class, 'success'])->name('reservations.success');

    // Avis / Évaluations (clients)
    Route::post('/hotels/{hotel}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ── Admin ────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [DashboardController::class, 'admin'])->name('admin.index');
        Route::get('/admin/journal', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.journal');
        Route::delete('/admin/journal', [\App\Http\Controllers\ActivityLogController::class, 'clear'])->name('admin.journal.clear');
        Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings.index');
        Route::patch('/reviews/{review}/approve', [ReviewController::class, 'adminApprove'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [ReviewController::class, 'adminReject'])->name('reviews.reject');
    });

    // ── Prestataire ──────────────────────────────────────────────────────────
    Route::middleware('role:prestataire')->group(function () {
        Route::get('/prestataire', [DashboardController::class, 'prestataire'])->name('prestataire.index');
        Route::get('/hotels', [DashboardController::class, 'hotels'])->name('hotels.index');
        Route::get('/hotels/{hotel}/edit', [DashboardController::class, 'editHotel'])->name('hotels.edit');
        Route::post('/hotels', [DashboardController::class, 'storeHotel'])->name('hotels.store');
        Route::put('/hotels/{hotel}', [DashboardController::class, 'updateHotel'])->name('hotels.update');
        Route::delete('/hotels/{hotel}', [DashboardController::class, 'destroyHotel'])->name('hotels.destroy');
        Route::get('/reservations', [DashboardController::class, 'reservations'])->name('reservations.index');
        Route::post('/reservations', [DashboardController::class, 'storeReservation'])->name('reservations.store');
        Route::patch('/reservations/{reservation}/status', [DashboardController::class, 'updateReservationStatus'])->name('reservations.update-status');
        Route::post('/reservations/{reservation}/checkin', [DashboardController::class, 'checkIn'])->name('reservations.checkin');
        Route::post('/reservations/{reservation}/checkout', [DashboardController::class, 'checkOut'])->name('reservations.checkout');
        Route::post('/rooms/{room}/cleaned', [DashboardController::class, 'roomCleaned'])->name('rooms.cleaned');
        Route::get('/rooms', [DashboardController::class, 'rooms'])->name('rooms.index');
        Route::post('/rooms', [DashboardController::class, 'storeRoom'])->name('rooms.store');
        Route::patch('/rooms/{room}/status', [DashboardController::class, 'updateRoomStatus'])->name('rooms.status');
        Route::delete('/rooms/{room}', [DashboardController::class, 'destroyRoom'])->name('rooms.destroy');
        Route::get('/clients', [DashboardController::class, 'clients'])->name('clients.index');
        Route::post('/clients', [DashboardController::class, 'storeClient'])->name('clients.store');
        Route::get('/boutique-admin', [DashboardController::class, 'boutique'])->name('boutique.index');
        Route::post('/boutique-admin', [DashboardController::class, 'storeProduct'])->name('products.store');
        Route::get('/menu', [DashboardController::class, 'menu'])->name('menu.index');
        Route::post('/menu', [DashboardController::class, 'storeMenuItem'])->name('menu.store');
        Route::get('/staff', [DashboardController::class, 'staff'])->name('staff.index');
        Route::post('/staff', [DashboardController::class, 'storeStaff'])->name('staff.store');
        Route::put('/staff/{staff}', [DashboardController::class, 'updateStaff'])->name('staff.update');
        Route::delete('/staff/{staff}', [DashboardController::class, 'destroyStaff'])->name('staff.destroy');
        Route::get('/staff/schedule', [DashboardController::class, 'staffSchedule'])->name('staff.schedule');
        Route::post('/staff/schedule', [DashboardController::class, 'storeSchedule'])->name('staff.schedule.store');
        Route::get('/pointage', [\App\Http\Controllers\PointageController::class, 'index'])->name('pointage.index');
        Route::post('/pointage/check-in', [\App\Http\Controllers\PointageController::class, 'checkIn'])->name('pointage.check-in');
        Route::post('/pointage/check-out', [\App\Http\Controllers\PointageController::class, 'checkOut'])->name('pointage.check-out');
        Route::get('/pointage/export', [\App\Http\Controllers\PointageController::class, 'export'])->name('pointage.export');
        Route::get('/expenses', [DashboardController::class, 'expenses'])->name('expenses.index');
        Route::post('/expenses', [DashboardController::class, 'storeExpense'])->name('expenses.store');
        Route::get('/pools', [DashboardController::class, 'pools'])->name('pools.index');
        Route::post('/pools', [DashboardController::class, 'storePool'])->name('pools.store');
        Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets.index');
        Route::post('/tickets', [DashboardController::class, 'storeTicket'])->name('tickets.store');
        Route::put('/tickets/{ticket}', [DashboardController::class, 'updateTicket'])->name('tickets.update');
        Route::delete('/tickets/{ticket}', [DashboardController::class, 'destroyTicket'])->name('tickets.destroy');
        Route::get('/orders', [DashboardController::class, 'orders'])->name('orders.index');
        Route::post('/orders', [DashboardController::class, 'storeOrder'])->name('orders.store');
        Route::put('/orders/{order}', [DashboardController::class, 'updateOrder'])->name('orders.update');
        Route::delete('/orders/{order}', [DashboardController::class, 'destroyOrder'])->name('orders.destroy');
        Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar.index');
    });

    // ── Client ───────────────────────────────────────────────────────────────
    Route::middleware('role:client')->group(function () {
        Route::get('/client', [DashboardController::class, 'client'])->name('client.index');
        Route::get('/mes-reservations', [DashboardController::class, 'reservations'])->name('client.reservations');
        Route::post('/mes-reservations', [DashboardController::class, 'storeReservation'])->name('client.reservations.store');
        Route::get('/mes-commandes', [CommandeController::class, 'mesCommandes'])->name('client.commandes');
        Route::get('/mes-favoris', [FavoriteController::class, 'index'])->name('client.favorites');
    });

    // Favoris (toggle AJAX — accessible à tous les rôles connectés)
    Route::post('/hotels/{hotel}/favorite', [FavoriteController::class, 'toggle'])->name('hotels.favorite');
});

// ─── Produits (édition) ──────────────────────────────────────────────────────
Route::get('/products', [DashboardController::class, 'boutique'])->name('products.index');
Route::get('/products/{product}/edit', [DashboardController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [DashboardController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [DashboardController::class, 'destroy'])->name('products.destroy');

// ─── Factures ────────────────────────────────────────────────────────────────
Route::get('/facturations/{facture}/pdf', [FactureController::class, 'pdf'])->name('facturations.pdf');

// ─── Commandes ───────────────────────────────────────────────────────────────
Route::get('/commande/create', [CommandeController::class, 'create'])->name('commande.create');
Route::post('/commande/store', [CommandeController::class, 'store'])->name('commande.store');
Route::post('/commande/acheter', [CommandeController::class, 'buyNow'])->name('commande.buy-now');
Route::get('/commande/success/{id}', [CommandeController::class, 'success'])->name('commande.success');
Route::get('/commande/{id}/facture', [CommandeController::class, 'facture'])->name('commande.facture');
Route::get('/admin/commandes', [CommandeController::class, 'commandes'])->name('admin.commandes');

// ─── Facturations ────────────────────────────────────────────────────────────
Route::prefix('facturations')->name('facturations.')->group(function () {
    Route::get('/', [FacturationController::class, 'index'])->name('index');
    Route::post('/', [FacturationController::class, 'store'])->name('store');
    Route::get('/{facturation}', [FacturationController::class, 'show'])->name('show');
    Route::delete('/{facturation}', [FacturationController::class, 'destroy'])->name('destroy');
});
