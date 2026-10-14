<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Google2FA;

/**
 * Mirror de App\Http\Controllers\TwoFAController (web) — même secret TOTP (compatible
 * Google Authenticator), réponses JSON + jeton Sanctum au lieu de session.
 */
class TwoFAController extends Controller
{
    public function setup(Request $request)
    {
        $user      = $request->user();
        $google2fa = app(Google2FA::class);

        if (! $user->two_fa_secret) {
            $user->update(['two_fa_secret' => $google2fa->generateSecretKey()]);
            $user->refresh();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_fa_secret
        );

        return response()->json(['secret' => $user->two_fa_secret, 'qr_code_url' => $qrCodeUrl]);
    }

    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = $request->user();

        if (! app(Google2FA::class)->verifyKey($user->two_fa_secret, $request->otp)) {
            return response()->json(['message' => 'Code incorrect. Vérifiez votre application.'], 422);
        }

        $user->update(['two_fa_enabled' => true]);

        return response()->json(['message' => 'Authentification à deux facteurs activée avec succès.']);
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (! Hash::check($request->password, $request->user()->password)) {
            return response()->json(['message' => 'Mot de passe incorrect.'], 422);
        }

        $request->user()->update(['two_fa_enabled' => false, 'two_fa_secret' => null]);

        return response()->json(['message' => 'Authentification à deux facteurs désactivée.']);
    }

    /**
     * Suite de AuthController::login quand `requires_2fa` a été renvoyé : vérifie l'OTP et
     * délivre le jeton Sanctum (mirror backendMobile twoFaController::verifyLogin).
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'otp'     => 'required|string',
        ]);

        $user = User::find($request->user_id);
        if (! $user || ! $user->two_fa_enabled || ! $user->two_fa_secret) {
            return response()->json(['message' => 'Compte introuvable ou 2FA non activée.'], 422);
        }

        if (! app(Google2FA::class)->verifyKey($user->two_fa_secret, $request->otp)) {
            return response()->json(['message' => 'Code invalide. Vérifiez votre application Google Authenticator.'], 422);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->role,
                'status'      => $user->status,
                'permissions' => $user->permissions ?? [],
                'hotels'      => $user->hotels,
                'two_fa'      => true,
            ],
            'token' => $token,
        ]);
    }
}
