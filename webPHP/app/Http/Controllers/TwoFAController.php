<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFAController extends Controller
{
    public function showSetup()
    {
        $user      = Auth::user();
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

        return view('auth.2fa.setup', compact('qrCodeUrl'));
    }

    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = Auth::user();

        if (! app(Google2FA::class)->verifyKey($user->two_fa_secret, $request->otp)) {
            return back()->withErrors(['otp' => 'Code incorrect. Vérifiez votre application.']);
        }

        $user->update(['two_fa_enabled' => true]);
        $request->session()->put('2fa_verified', true);

        return redirect()->route('profile.index')
            ->with('success', 'Authentification à deux facteurs activée avec succès.');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (! Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        Auth::user()->update(['two_fa_enabled' => false, 'two_fa_secret' => null]);
        $request->session()->forget('2fa_verified');

        return back()->with('success', 'Authentification à deux facteurs désactivée.');
    }

    public function showVerify()
    {
        if (! session('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.2fa.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|string']);

        $userId = session('2fa_user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::findOrFail($userId);

        if (! app(Google2FA::class)->verifyKey($user->two_fa_secret, $request->otp)) {
            return back()->withErrors(['otp' => 'Code invalide. Vérifiez votre application Google Authenticator.']);
        }

        Auth::login($user);
        $request->session()->forget('2fa_user_id');
        $request->session()->put('2fa_verified', true);
        $request->session()->regenerate();

        return redirect()->intended(match ($user->role) {
            'admin'       => route('admin.index'),
            'prestataire' => route('prestataire.index'),
            'employe'     => route('prestataire.index'),
            default       => route('client.index'),
        });
    }
}
