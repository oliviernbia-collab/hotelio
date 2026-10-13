<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Logique de commission et de reversement automatique partagée entre les réservations
 * (DashboardController) et les achats boutique (CommandeController), pour éviter de dupliquer
 * le calcul du taux et l'appel au virement Jèko dans les deux contrôleurs.
 */
class PayoutService
{
    /** Channels considérés comme "apportés par Hôtelio" (commissionnables) */
    public const COMMISSIONABLE_CHANNELS = ['Site web', 'App mobile'];
    public const DEFAULT_COMMISSION_RATE = 10.00;

    /** Taux de commission applicable au prestataire : celui de son abonnement actif, sinon le taux par défaut. */
    public static function rateForUser(?User $user): float
    {
        if ($user) {
            $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();
            if ($subscription && $subscription->plan) {
                return (float) $subscription->plan->commission_rate;
            }
        }

        return self::DEFAULT_COMMISSION_RATE;
    }

    /**
     * Déclenche un virement Jèko automatique de $netAmount (FCFA) vers le contact enregistré
     * du propriétaire. Retourne ['status' => null] sans rien tenter si le propriétaire n'a pas
     * de contact de paiement enregistré ou si le montant est sous le minimum Jèko — ce n'est
     * pas une erreur, juste rien à reverser pour l'instant (en attente que le propriétaire
     * renseigne ses coordonnées).
     *
     * @return array{status: ?string, transfer_id: ?string, failure_reason: ?string}
     */
    public static function transfer(User $owner, int $netAmount, string $reference, string $description): array
    {
        if (! $owner->hasPayoutContact() || $netAmount < 5) {
            return ['status' => null, 'transfer_id' => null, 'failure_reason' => null];
        }

        $result = JekoClient::createTransfer([
            'storeId'     => config('services.jeko.store_id'),
            'contactId'   => $owner->jeko_contact_id,
            'amountCents' => $netAmount * 100,
            'currency'    => 'XOF',
            'description' => $description,
            'reference'   => $reference,
        ]);

        if (isset($result['status'])) {
            return ['status' => $result['status'], 'transfer_id' => $result['id'] ?? null, 'failure_reason' => null];
        }

        Log::error('Échec du virement de reversement Jèko', ['reference' => $reference, 'result' => $result]);

        return [
            'status'         => 'error',
            'transfer_id'    => null,
            'failure_reason' => $result['message'] ?? ($result['id'] ?? 'Erreur inconnue'),
        ];
    }
}
