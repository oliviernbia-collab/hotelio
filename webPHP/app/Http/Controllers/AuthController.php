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

        if (Auth::attempt($credentials, $request->boolean('remember')))
        {
            $user = Auth::user();

            if ($user->status !== 'actif') {
                Auth::logout();
                return back()->withErrors([
                    'email' => $user->status === 'en_attente'
                        ? "Votre compte est en attente de validation par un administrateur. Vous recevrez un accès sous 24–48 h."
                        : 'Votre compte est inactif. Contactez un administrateur.',
                ])->onlyInput('email');
            }

            // Vérification 2FA obligatoire si activée
            if ($user->two_fa_enabled) {
                $request->session()->put('2fa_user_id', $user->id);
                Auth::logout();
                return redirect()->route('2fa.verify');
            }

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
            'role' => $data['role'],
            // Un hôtelier doit être validé par un admin avant de pouvoir se connecter.
            'status' => $isPrestataire ? 'en_attente' : 'actif',
            'hotels' => 'Toutes les propriétés',
            'permissions' => $permissions,
        ]);

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
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
