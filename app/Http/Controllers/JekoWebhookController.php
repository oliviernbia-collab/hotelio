<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\JekoClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class JekoWebhookController extends Controller
{
    /**
     * Reçoit les notifications Jèko (en-tête Jeko-Event).
     * Seul TRANSACTION_COMPLETED nous intéresse ici (confirmation de paiement de réservation) ;
     * les autres événements sont accusés en 200 sans traitement.
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

        if (($payload['transactionType'] ?? null) !== 'payment') {
            return response('ok', 200);
        }

        $reference = $payload['transactionDetails']['reference'] ?? null;
        $status = $payload['status'] ?? null;

        if (! $reference) {
            return response('ok', 200);
        }

        $reservation = Reservation::where('jeko_reference', $reference)->first();

        if (! $reservation) {
            return response('ok', 200);
        }

        if ($status === 'success') {
            app(DashboardController::class)->markReservationPaid($reservation);
        } elseif ($status === 'error' && ! $reservation->paid) {
            $reservation->update(['status' => 'paiement_échoué']);
        }

        return response('ok', 200);
    }
}
