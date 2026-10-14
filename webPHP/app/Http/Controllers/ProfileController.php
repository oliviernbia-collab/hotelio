<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\CloudinaryUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** Photo de profil — ouverte à tous les rôles (prestataire, client, employé, admin). */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $user = Auth::user();

        CloudinaryUploader::destroy($user->avatar);
        $user->update(['avatar' => CloudinaryUploader::upload($request->file('avatar'), 'avatars')]);

        return back()->with('success', 'Photo de profil mise à jour.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        // Sync client record if exists
        Client::where('user_id', $user->id)->update([
            'first_name' => explode(' ', $data['name'])[0],
            'last_name'  => explode(' ', $data['name'])[1] ?? '',
            'email'      => $data['email'],
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    /**
     * Désactivation de compte en self-service : passe le statut à `inactif` (bloque la
     * connexion, cf. AuthController::login) sans supprimer les données liées (réservations,
     * factures, hôtel...). La suppression définitive des données reste une démarche
     * manuelle (confidentialite@hotelio.ci, cf. politique de confidentialité).
     */
    public function destroy(Request $request)
    {
        $request->validate(['password' => 'required']);

        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.'], 'destroyAccount');
        }

        $user->forceFill(['status' => 'inactif'])->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'success',
            'Votre compte a été désactivé. Contactez-nous à confidentialite@hotelio.ci pour le réactiver ou demander la suppression définitive de vos données.'
        );
    }
}
