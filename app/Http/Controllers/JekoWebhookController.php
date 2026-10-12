<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Subscription;
use App\Services\JekoClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class JekoWebhookController extends Controller
{
    /**
     * Reçoit les notifications Jèko (en-tête Jeko-Event). Seul TRANSACTION_COMPLETED nous
     * intéresse : confirmation de paiement (réservation ou abonnement) et statut de virement
     * (reversement automatique aux propriétaires). Les autres événements sont accusés en 200
     * sans traitement.
     */
    public function handle(Request $request): Response
    {
        $rawBody = $request->getContent();
        $signature = $request->header('Jeko-Signature');

        if (! JekoClient::verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Jèko webhook : signature invalide.');
            return response('invalid signature', 401);
        }

        if ($request->header('Jeko-Event') !== 'TRANSACTION_COMPLETED') {
            return response('ok', 200);
        }

        $payload = json_decode($rawBody, true) ?? [];
        $transactionType = $payload['transactionType'] ?? null;
        $status = $payload['status'] ?? null;
        $reference = $payload['transactionDetails']['reference'] ?? null;

        if ($transactionType === 'payment') {
            $this->handlePayment($reference, $status);
        } elseif ($transactionType === 'transfer') {
            $this->handleTransfer($payload);
        }

        return response('ok', 200);
    }

    /** Un encaissement (réservation ou abonnement) a été confirmé ou a échoué. */
    private function handlePayment(?string $reference, ?string $status): void
    {
        if (! $reference) {
            return;
        }

        $reservation = Reservation::where('jeko_reference', $reference)->first();
        if ($reservation) {
            if ($status === 'success') {
                $dashboard = app(DashboardController::class);
                $dashboard->markReservationPaid($reservation);
                $dashboard->triggerOwnerPayout($reservation->fresh());
            } elseif ($status === 'error' && ! $reservation->paid) {
                $reservation->update(['status' => 'paiement_échoué']);
            }
            return;
        }

        $subscription = Subscription::where('jeko_reference', $reference)->first();
        if ($subscription && $status === 'success' && $subscription->status !== 'active') {
            app(DashboardController::class)->activateSubscriptionFromPayment($subscription);
        }
    }

    /** Suivi asynchrone d'un virement de reversement vers un propriétaire. */
    private function handleTransfer(array $payload): void
    {
        $transferId = $payload['id'] ?? null;
        if (! $transferId) {
            return;
        }

        $reservation = Reservation::where('payout_transfer_id', $transferId)->first();
        if (! $reservation) {
            return;
        }

        $reservation->update(['payout_status' => $payload['status'] ?? $reservation->payout_status]);
    }
}
