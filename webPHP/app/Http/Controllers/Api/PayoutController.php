<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\LigneCommande;
use App\Models\Payout;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

/** Mirror de DashboardController (sections reversements) — réservé aux admins. */
class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $prestataires = app(DashboardController::class)->payoutsData($request->get('type'));

        $failedPayouts = Reservation::with(['hotel', 'client'])
            ->where('payout_status', 'error')
            ->latest()
            ->get();

        $failedLignes = LigneCommande::with(['commande', 'product.user'])
            ->where('payout_status', 'error')
            ->latest()
            ->get();

        return response()->json([
            'prestataires'   => $prestataires,
            'failed_payouts' => $failedPayouts,
            'failed_lignes'  => $failedLignes,
        ]);
    }

    /** Versement manuel — réservé aux cas où le virement automatique Jèko a échoué. */
    public function recordPayout(Request $request, User $user)
    {
        if (! $user->hasFailedAutomaticPayout()) {
            return response()->json(['message' => 'Le versement manuel est réservé aux virements automatiques en échec.'], 422);
        }

        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'notes'  => 'nullable|string|max:255',
        ]);

        if ($data['amount'] > $user->payoutBalance()) {
            return response()->json(['message' => 'Le montant dépasse le solde dû (' . number_format($user->payoutBalance(), 0, ',', ' ') . ' FCFA).'], 422);
        }

        $payout = Payout::create([
            'user_id'     => $user->id,
            'amount'      => $data['amount'],
            'notes'       => $data['notes'] ?? null,
            'paid_at'     => now(),
            'recorded_by' => $request->user()->id,
        ]);

        return response()->json($payout, 201);
    }

    /** Relance un virement automatique Jèko qui a échoué. */
    public function retryPayout(Reservation $reservation)
    {
        $reservation->update(['payout_transfer_id' => null, 'payout_status' => null, 'payout_failure_reason' => null]);
        app(DashboardController::class)->triggerOwnerPayout($reservation->fresh());

        $reservation->refresh();
        if ($reservation->payout_status === 'error') {
            return response()->json(['message' => 'Le virement a de nouveau échoué : ' . ($reservation->payout_failure_reason ?? 'raison inconnue')], 422);
        }

        return response()->json($reservation);
    }
}
