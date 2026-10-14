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

// ─── Retour paiement abonnement (app mobile) ───────────────────────────────
// Page statique sans session : l'activation réelle se fait uniquement via le webhook
// Jèko partagé (JekoWebhookController) — cf. Api\SubscriptionController::pay().
Route::get('/mobile/subscription/callback', function (\Illuminate\Http\Request $request) {
    $ok = $request->query('result') !== 'error';
    $title = $ok ? '✅ Paiement confirmé' : '❌ Paiement annulé ou échoué';
    $body  = $ok
        ? "Vous pouvez fermer cette fenêtre et revenir à l'application."
        : "Vous pouvez fermer cette fenêtre et réessayer dans l'application.";

    return response(
        '<!doctype html><html><head><meta charset="utf-8"><title>Hôtelio</title></head>'
        . '<body style="font-family:sans-serif;text-align:center;padding:40px">'
        . "<h2>{$title}</h2><p>{$body}</p></body></html>"
    );
})->name('subscription.payment.callback.mobile');

// ─── Page d'accueil publique ────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'premiere'])->name('premiere');
Route::post('/premiere', [HomeController::class, 'premierePost'])->name('premiere.post');
Route::get('/hotels-en-ligne', [HomeController::class, 'hotelSearch'])->name('hotels.public');
Route::get('/hotels-en-ligne/{hotel}', [HomeController::class, 'hotelDetails'])->name('hotels.details');
Route::get('/tarifs', [HomeController::class, 'pricing'])->name('pricing');

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
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/payout-info', [DashboardController::class, 'updatePayoutInfo'])->name('profile.payout-info');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fiche hôtel (visible par tous les rôles connectés — booking)
    Route::get('/hotels/{hotel}', [DashboardController::class, 'hotel'])->name('hotels.show');

    // Paiement & facture (accessibles à tous les rôles connectés)
    Route::get('/reservations/{reservation}/paiement', [DashboardController::class, 'payment'])->name('reservations.payment');
    Route::post('/reservations/{reservation}/paiement', [DashboardController::class, 'processPayment'])->name('reservations.payment.process')->middleware('throttle:20,1');
    Route::get('/reservations/{reservation}/paiement/retour', [DashboardController::class, 'paymentCallback'])->name('reservations.payment.callback');
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
        Route::get('/admin/reviews', [ReviewController::class, 'adminIndex'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [ReviewController::class, 'adminApprove'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [ReviewController::class, 'adminReject'])->name('reviews.reject');
        Route::get('/admin/subscriptions', [DashboardController::class, 'subscriptions'])->name('subscriptions.index');
        Route::post('/admin/subscriptions/{subscription}/payment', [DashboardController::class, 'recordSubscriptionPayment'])->name('subscriptions.payment');
        Route::get('/admin/payouts', [DashboardController::class, 'payouts'])->name('payouts.index');
        Route::post('/admin/payouts/{user}', [DashboardController::class, 'recordPayout'])->name('payouts.record');
        Route::post('/admin/payouts/retry/{reservation}', [DashboardController::class, 'retryPayout'])->name('payouts.retry');
        Route::post('/admin/payouts/retry-commande/{ligneCommande}', [CommandeController::class, 'retryLignePayout'])->name('payouts.retry-commande');
    });

    // ── Prestataire ──────────────────────────────────────────────────────────
    Route::middleware('role:prestataire')->group(function () {
        Route::get('/prestataire', [DashboardController::class, 'prestataire'])->name('prestataire.index');
        Route::post('/abonnement/choisir', [DashboardController::class, 'chooseSubscriptionPlan'])->name('subscription.choose');
        Route::post('/abonnement/payer', [DashboardController::class, 'paySubscription'])->name('subscription.pay');
        Route::get('/abonnement/paiement/retour', [DashboardController::class, 'subscriptionPaymentCallback'])->name('subscription.payment.callback');
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
        Route::put('/rooms/{room}', [DashboardController::class, 'updateRoom'])->name('rooms.update');
        Route::patch('/rooms/{room}/status', [DashboardController::class, 'updateRoomStatus'])->name('rooms.status');
        Route::post('/rooms/{room}/image', [DashboardController::class, 'updateRoomImage'])->name('rooms.image');
        Route::delete('/rooms/{room}', [DashboardController::class, 'destroyRoom'])->name('rooms.destroy');
        Route::get('/clients', [DashboardController::class, 'clients'])->name('clients.index');
        Route::post('/clients', [DashboardController::class, 'storeClient'])->name('clients.store');
        Route::put('/clients/{client}', [DashboardController::class, 'updateClient'])->name('clients.update');
        Route::post('/clients/{client}/points', [DashboardController::class, 'addClientPoints'])->name('clients.points');
        Route::get('/boutique-admin', [DashboardController::class, 'boutique'])->name('boutique.index');
        Route::post('/boutique-admin', [DashboardController::class, 'storeProduct'])->name('products.store');
        Route::get('/menu', [DashboardController::class, 'menu'])->name('menu.index');
        Route::post('/menu', [DashboardController::class, 'storeMenuItem'])->name('menu.store');
        Route::put('/menu/{menuItem}', [DashboardController::class, 'updateMenuItem'])->name('menu.update');
        Route::delete('/menu/{menuItem}', [DashboardController::class, 'destroyMenuItem'])->name('menu.destroy');
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
        Route::put('/expenses/{expense}', [DashboardController::class, 'updateExpense'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [DashboardController::class, 'destroyExpense'])->name('expenses.destroy');
        Route::get('/pools', [DashboardController::class, 'pools'])->name('pools.index');
        Route::post('/pools', [DashboardController::class, 'storePool'])->name('pools.store');
        Route::put('/pools/{pool}', [DashboardController::class, 'updatePool'])->name('pools.update');
        Route::delete('/pools/{pool}', [DashboardController::class, 'destroyPool'])->name('pools.destroy');
        Route::get('/services', [DashboardController::class, 'services'])->name('services.index');
        Route::post('/services', [DashboardController::class, 'storeService'])->name('services.store');
        Route::put('/services/{service}', [DashboardController::class, 'updateService'])->name('services.update');
        Route::patch('/services/{service}/toggle', [DashboardController::class, 'toggleService'])->name('services.toggle');
        Route::delete('/services/{service}', [DashboardController::class, 'destroyService'])->name('services.destroy');
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
        Route::post('/mes-reservations/{reservation}/annuler', [DashboardController::class, 'cancelReservation'])->name('client.reservations.cancel');
        Route::get('/mes-commandes', [CommandeController::class, 'mesCommandes'])->name('client.commandes');
        Route::get('/mes-favoris', [FavoriteController::class, 'index'])->name('client.favorites');
    });

    // Favoris (toggle AJAX — accessible à tous les rôles connectés)
    Route::post('/hotels/{hotel}/favorite', [FavoriteController::class, 'toggle'])->name('hotels.favorite');
});

// ─── Produits (édition) ──────────────────────────────────────────────────────
// (édition/suppression protégées — vérification de propriété faite dans le contrôleur)
Route::get('/products', [DashboardController::class, 'boutique'])->name('products.index');
Route::middleware('auth')->group(function () {
    Route::get('/products/{product}/edit', [DashboardController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [DashboardController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [DashboardController::class, 'destroy'])->name('products.destroy');
});

// ─── Factures ────────────────────────────────────────────────────────────────
Route::get('/facturations/{facture}/pdf', [FactureController::class, 'pdf'])->name('facturations.pdf')->middleware('auth');

// ─── Commandes ───────────────────────────────────────────────────────────────
// Le paiement réel (redirection Jèko + confirmation côté serveur) a besoin d'un compte pour
// savoir à qui confirmer/rattacher la commande : plus de "guest checkout" possible ici.
Route::get('/commande/create', [CommandeController::class, 'create'])->name('commande.create')->middleware('auth');
Route::post('/commande/store', [CommandeController::class, 'store'])->name('commande.store')->middleware(['auth', 'throttle:20,1']);
Route::post('/commande/acheter', [CommandeController::class, 'buyNow'])->name('commande.buy-now')->middleware(['auth', 'throttle:20,1']);
Route::get('/commande/success/{id}', [CommandeController::class, 'success'])->name('commande.success')->middleware('auth');
Route::get('/commande/{id}/facture', [CommandeController::class, 'facture'])->name('commande.facture')->middleware('auth');
Route::get('/admin/commandes', [CommandeController::class, 'commandes'])->name('admin.commandes')->middleware(['auth', 'role:admin']);
Route::patch('/commande/{commande}/annuler', [CommandeController::class, 'cancel'])->name('commande.cancel')->middleware('auth');
Route::get('/commande/{commande}/paiement/retour', [CommandeController::class, 'paymentCallback'])->name('commande.payment.callback')->middleware('auth');

// ─── Facturations ────────────────────────────────────────────────────────────
// Était accessible sans authentification (faille) : toutes les factures (noms de
// clients, montants) étaient consultables/supprimables par n'importe qui.
// Restreint ensuite à prestataire/admin : un simple client ne doit pas pouvoir
// lire/modifier/supprimer les factures de la plateforme.
// NB: ce module n'a pas de colonne "propriétaire" (créé avant le rattachement
// hotel_id) — toute facture reste donc visible par tout prestataire, comme les
// autres ressources globales (ex. liste des commandes admin). À restreindre par
// hôtel si ce module est un jour remis à plat.
Route::middleware(['auth', 'role:prestataire'])->prefix('facturations')->name('facturations.')->group(function () {
    Route::get('/', [FacturationController::class, 'index'])->name('index');
    Route::post('/', [FacturationController::class, 'store'])->name('store');
    Route::get('/{facturation}', [FacturationController::class, 'show'])->name('show');
    Route::put('/{facturation}', [FacturationController::class, 'update'])->name('update');
    Route::patch('/{facturation}/pay', [FacturationController::class, 'pay'])->name('pay');
    Route::delete('/{facturation}', [FacturationController::class, 'destroy'])->name('destroy');
});
