<?php

namespace App\Http\Controllers;

use App\Models\Commande;
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

    /**
     * Filet de sécurité appelé par le backend mobile (Node, voir /payment/bridge) quand
     * l'utilisateur revient d'un paiement Jèko mais que le webhook n'est pas (encore) arrivé —
     * typiquement en développement local, où notre serveur n'est pas joignable depuis
     * l'extérieur pour recevoir le webhook. On ne fait jamais confiance à un statut transmis
     * par le client : on interroge toujours Jèko directement avant d'agir.
     */
    public function syncPayment(Request $request)
    {
        $key = (string) config('services.jeko.internal_sync_key');
        if (! $key || ! hash_equals($key, (string) $request->header('X-Internal-Key'))) {
            return response('forbidden', 403);
        }

        $reference = $request->input('reference');
        if (! is_string($reference) || $reference === '') {
            return response()->json(['synced' => false], 422);
        }

        $requestId = Reservation::where('jeko_reference', $reference)->value('jeko_payment_request_id')
            ?? Commande::where('jeko_reference', $reference)->value('jeko_payment_request_id')
            ?? Subscription::where('jeko_reference', $reference)->value('jeko_payment_request_id');

        if (! $requestId) {
            return response()->json(['synced' => false, 'status' => 'not_found'], 404);
        }

        $result = JekoClient::getPaymentRequest($requestId);
        $status = $result['status'] ?? null;

        if ($status === 'success') {
            $this->handlePayment($reference, 'success');
        }

        return response()->json(['synced' => $status === 'success', 'status' => $status]);
    }

    /** Un encaissement (réservation, abonnement ou commande boutique) a été confirmé ou a échoué. */
    private function handlePayment(?string $reference, ?string $status): void
    {
        if (! $reference) {
            return;
        }

        $reservation = Reservation::where('jeko_reference', $reference)->first();
        if ($reservation) {
            if ($status === 'success') {
                // Le reversement prestataire est volontairement retardé jusqu'au check-in réel
                // (ou à la commande planifiée payouts:release-due) : tant que le séjour n'a pas
                // commencé, l'argent doit pouvoir être intégralement remboursé en cas d'annulation.
                app(DashboardController::class)->markReservationPaid($reservation);
            } elseif ($status === 'error' && ! $reservation->paid) {
                $reservation->update(['status' => 'paiement_échoué']);
            }
            return;
        }

        $subscription = Subscription::where('jeko_reference', $reference)->first();
        if ($subscription && $status === 'success' && $subscription->status !== 'active') {
            app(DashboardController::class)->activateSubscriptionFromPayment($subscription);
            return;
        }

        $commande = Commande::where('jeko_reference', $reference)->first();
        if ($commande && $status === 'success' && $commande->statut !== 'payé') {
            // Idem réservations : le reversement prestataire attend la fenêtre d'annulation
            // (payouts:release-due), pour que le remboursement reste possible entre-temps.
            app(CommandeController::class)->markCommandePaid($commande);
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
        if ($reservation) {
            $reservation->update(['payout_status' => $payload['status'] ?? $reservation->payout_status]);
            return;
        }

        if (isset($payload['status'])) {
            \App\Models\LigneCommande::where('payout_transfer_id', $transferId)->update(['payout_status' => $payload['status']]);
        }
    }
}
