<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Ces identifiants sont incorrects.'],
            ]);
        }

        if ($user->status === 'inactif') {
            return response()->json(['message' => 'Votre compte est désactivé.'], 403);
        }

        if ($user->status === 'en_attente') {
            return response()->json(['message' => 'Votre compte est en attente de validation par un administrateur.'], 403);
        }

        // Compte protégé par 2FA : pas de jeton immédiat, le client doit d'abord vérifier
        // l'OTP via TwoFAController::verifyLogin (mirror backendMobile authController::login).
        if ($user->two_fa_enabled) {
            return response()->json(['requires_2fa' => true, 'user_id' => $user->id]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user'  => $this->userPayload($user),
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // Accepte "name" ou "prenom"+"nom" (app mobile)
        $name = $request->name
            ?? trim(($request->prenom ?? '') . ' ' . ($request->nom ?? ''))
            ?: null;

        if (! $name) {
            return response()->json([
                'message' => 'Le champ nom est obligatoire.',
                'errors'  => ['name' => ['Le champ nom est obligatoire.']],
            ], 422);
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'client',
            'status'   => 'actif',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user'  => $this->userPayload($user),
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    /** Révoque tous les tokens Sanctum de l'utilisateur (ex: téléphone perdu/volé). */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Déconnecté de tous les appareils.']);
    }

    public function me(Request $request)
    {
        return response()->json($this->userPayload($request->user()));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Un lien de réinitialisation a été envoyé.'
                : 'Impossible d\'envoyer le lien.',
        ], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    private function userPayload(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $user->role,
            'status'      => $user->status,
            'permissions' => $user->permissions ?? [],
            'hotels'      => $user->hotels,
            'two_fa'      => (bool) $user->two_fa_enabled,
        ];
    }
}
