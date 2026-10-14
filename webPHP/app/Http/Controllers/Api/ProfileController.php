<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryUploader;
use App\Services\JekoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'role'               => $user->role,
            'status'             => $user->status,
            'permissions'        => $user->permissions ?? [],
            'two_fa'             => (bool) $user->two_fa_enabled,
            'payout_method'      => $user->payout_method,
            'payout_identifier'  => $user->payout_identifier,
            'has_payout_contact' => $user->hasPayoutContact(),
        ]);
    }

    /**
     * Enregistre (ou remplace) les coordonnées de paiement (mobile money ou RIB bancaire) et
     * crée le contact bénéficiaire correspondant chez Jèko — utilisé aussi bien par un
     * prestataire (reversements) que par un client (remboursements en cas d'annulation).
     */
    public function updatePayout(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'payout_method'  => 'required|string|in:wave,orange_money,mtn,moov,djamo,bank',
            'phone_number'   => 'required_unless:payout_method,bank|nullable|string|max:20',
            'bank_name'      => 'required_if:payout_method,bank|nullable|string|max:100',
            'bank_code'      => 'required_if:payout_method,bank|nullable|string|max:20',
            'swift_code'     => 'required_if:payout_method,bank|nullable|string|max:20',
            'agency_code'    => 'required_if:payout_method,bank|nullable|string|max:20',
            'account_number' => 'required_if:payout_method,bank|nullable|string|max:40',
            'rib_key'        => 'required_if:payout_method,bank|nullable|string|max:10',
        ]);

        $isBank = $data['payout_method'] === 'bank';
        $identifier = $isBank
            ? [
                'bankName'      => $data['bank_name'],
                'bankCode'      => $data['bank_code'],
                'swiftCode'     => $data['swift_code'],
                'agencyCode'    => $data['agency_code'],
                'accountNumber' => $data['account_number'],
                'key'           => $data['rib_key'],
            ]
            : ['number' => $data['phone_number']];

        $result = JekoClient::createContact([
            'name'          => $user->name,
            'paymentMethod' => $data['payout_method'],
            'identifier'    => $identifier,
        ]);

        if (empty($result['id'])) {
            return response()->json(['message' => $result['message'] ?? "Impossible d'enregistrer ces coordonnées de paiement pour le moment."], 422);
        }

        $oldContactId = $user->jeko_contact_id;

        $user->update([
            'payout_method'     => $data['payout_method'],
            'payout_identifier' => $identifier,
            'jeko_contact_id'   => $result['id'],
        ]);

        if ($oldContactId) {
            try { JekoClient::deleteContact($oldContactId); } catch (\Throwable) {}
        }

        return response()->json(['message' => 'Coordonnées de paiement enregistrées.', 'user' => $user->fresh()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profil mis à jour.', 'user' => $user->fresh()]);
    }

    /** Mirror de ProfileController::updateAvatar (web). */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $user = $request->user();

        CloudinaryUploader::destroy($user->avatar);
        $user->update(['avatar' => CloudinaryUploader::upload($request->file('avatar'), 'avatars')]);

        return response()->json(['message' => 'Photo de profil mise à jour.', 'user' => $user->fresh()]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mot de passe actuel incorrect.'],
            ]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Mot de passe modifié.']);
    }

    /** Désactivation de compte en self-service (voir ProfileController::destroy côté web). */
    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validate(['password' => 'required']);

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Mot de passe incorrect.'],
            ]);
        }

        $user->forceFill(['status' => 'inactif'])->save();
        $user->tokens()->delete();

        return response()->json(['message' => 'Compte désactivé.']);
    }
}
