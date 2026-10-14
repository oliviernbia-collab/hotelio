<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Services\CloudinaryUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::validate() ne crée ni session ni cookie "remember" — on ne sait donc pas
        // encore si l'utilisateur va passer par le 2FA au moment où les identifiants sont
        // vérifiés, ce qui évite de poser un remember_token persistant qu'un Auth::logout()
        // ultérieur ne retire jamais (le cookie réauthentifierait silencieusement l'utilisateur
        // sans jamais redemander l'OTP).
        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->first();

            if ($user->status !== 'actif') {
                return back()->withErrors([
                    'email' => $user->status === 'en_attente'
                        ? "Votre compte est en attente de validation par un administrateur. Vous recevrez un accès sous 24–48 h."
                        : 'Votre compte est inactif. Contactez un administrateur.',
                ])->onlyInput('email');
            }

            // Vérification 2FA obligatoire si activée : le remember-me souhaité est mémorisé
            // en session et n'est appliqué qu'après succès de l'OTP (TwoFAController::verify).
            if ($user->two_fa_enabled) {
                $request->session()->put('2fa_user_id', $user->id);
                $request->session()->put('2fa_remember', $request->boolean('remember'));
                return redirect()->route('2fa.verify');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $request->session()->put('2fa_verified', true);

            return redirect()->route(match ($user->role) {
                'admin'       => 'admin.index',
                'prestataire' => 'prestataire.index',
                'employe'     => 'prestataire.index',
                default       => 'client.index',
            });
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('auth.register', compact('plans'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:prestataire,client'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'proof_document' => [
                Rule::requiredIf($request->input('role') === 'prestataire'),
                'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120',
            ],
            'plan' => [
                Rule::requiredIf($request->input('role') === 'prestataire'),
                'nullable', 'string', 'in:starter,business,premium',
            ],
        ], [
            'proof_document.required' => "Un justificatif de propriété est requis pour un compte hôtelier.",
            'plan.required' => "Veuillez choisir un palier d'abonnement.",
        ]);

        $isPrestataire = $data['role'] === 'prestataire';

        $permissions = null;
        if ($isPrestataire && $request->hasFile('proof_document')) {
            $url = CloudinaryUploader::upload($request->file('proof_document'), 'proofs', 'auto');
            $permissions = ['proof_document' => $url, 'proof_status' => 'pending'];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'hotels' => 'Toutes les propriétés',
        ]);
        $user->forceFill([
            'role' => $data['role'],
            // Un hôtelier doit être validé par un admin avant de pouvoir se connecter.
            'status' => $isPrestataire ? 'en_attente' : 'actif',
            'permissions' => $permissions,
        ])->save();

        if ($isPrestataire) {
            $plan = \App\Models\SubscriptionPlan::where('slug', $data['plan'])->first();
            if ($plan) {
                \App\Models\Subscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => 'pending_payment',
                ]);
            }

            return redirect()->route('login')->with(
                'success',
                'Votre demande a bien été soumise, ' . $data['name'] . ' ! Un administrateur validera votre compte hôtelier sous 24–48 h.'
            );
        }

        Auth::login($user);

        // Notification de bienvenue (en base, email en option)
        try { $user->notify(new WelcomeNotification()); } catch (\Throwable) {}

        return redirect()->route('client.index')
            ->with('success', 'Bienvenue sur Hôtelio, ' . $data['name'] . ' !');
    }

    public function logout(Request $request)
    {
        // Invalide le remember_token en base pour que le cookie "se souvenir de moi" ne
        // puisse plus jamais réauthentifier silencieusement cet utilisateur après ce logout.
        optional(Auth::user())->forceFill(['remember_token' => null])->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
