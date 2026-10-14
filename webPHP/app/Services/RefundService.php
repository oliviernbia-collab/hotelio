<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Reservation;
use App\Notifications\ReservationCancelledNotification;
use Illuminate\Support\Facades\Log;

/**
 * Annulation avec remboursement réel d'une réservation ou d'une commande boutique
 * déjà payée. Jèko n'offrant pas de remboursement vers le moyen de paiement
 * d'origine, le client est remboursé via un virement sortant (comme un reversement
 * prestataire) vers son propre contact Jèko enregistré.
 *
 * Répartition fixe sur le montant payé : 5% de frais d'annulation, dont 4% pour
 * l'administrateur de la plateforme (reste simplement sur le compte Jèko de
 * l'app, aucun virement nécessaire) et 1% pour le prestataire concerné ; 95%
 * remboursés au client.
 */
class RefundService
{
    public const FEE_RATE = 5.00;
    public const ADMIN_SHARE_RATE = 4.00;
    public const PROVIDER_SHARE_RATE = 1.00;

    /** Fenêtre d'annulation pour les commandes boutique (pas de date de check-in à utiliser). */
    public const COMMANDE_CANCEL_WINDOW_HOURS = 48;

    /**
     * @return array{fee: int, adminAmount: int, providerAmount: int, refundAmount: int}
     */
    public static function splitAmounts(int $total): array
    {
        $fee = (int) round($total * self::FEE_RATE / 100);
        $adminAmount = (int) round($total * self::ADMIN_SHARE_RATE / 100);
        $providerAmount = $fee - $adminAmount; // évite les écarts d'arrondi, reste ≈ 1%

        return [
            'fee' => $fee,
            'adminAmount' => $adminAmount,
            'providerAmount' => $providerAmount,
            'refundAmount' => $total - $fee,
        ];
    }

    /** Annulation remboursable tant que le séjour n'a pas commencé. */
    public static function canCancelReservation(Reservation $reservation): bool
    {
        if (! in_array($reservation->status, ['en_attente', 'confirmée'], true)) {
            return false;
        }

        if ($reservation->checkin_at) {
            return false;
        }

        return $reservation->checkin->copy()->startOfDay()->greaterThanOrEqualTo(today());
    }

    /**
     * @return array{ok: bool, error: ?string, message: string, fee?: int, refundAmount?: int, refundStatus?: ?string}
     */
    public static function cancelReservation(Reservation $reservation): array
    {
        if (! $reservation->paid) {
            if ($reservation->checkin_at) {
                return ['ok' => false, 'error' => 'too_late', 'message' => 'Cette réservation ne peut plus être annulée (séjour déjà commencé).'];
            }

            $reservation->update(['status' => 'annulée']);

            return ['ok' => true, 'error' => null, 'message' => 'Réservation annulée.'];
        }

        if (! self::canCancelReservation($reservation)) {
            return ['ok' => false, 'error' => 'too_late', 'message' => "Cette réservation ne peut plus être annulée (séjour commencé ou trop proche)."];
        }

        if (! $reservation->jeko_reference) {
            // Payée en espèces/virement : l'argent n'est jamais passé par Jèko, impossible et
            // incorrect d'y puiser un remboursement — reste géré manuellement par l'hôtel.
            self::reverseCommissionAndStats($reservation);
            $reservation->update(['status' => 'annulée', 'cancelled_at' => now()]);

            return ['ok' => true, 'error' => null, 'message' => 'Réservation annulée. Remboursement à gérer directement avec le client (paiement espèces/virement).'];
        }

        $clientUser = $reservation->client?->user;
        if (! $clientUser || ! $clientUser->hasPayoutContact()) {
            return [
                'ok' => false,
                'error' => 'missing_payout_contact',
                'message' => "Enregistrez d'abord vos coordonnées de paiement (mobile money ou compte bancaire) pour recevoir votre remboursement.",
            ];
        }

        ['fee' => $fee, 'adminAmount' => $adminAmount, 'providerAmount' => $providerAmount, 'refundAmount' => $refundAmount] = self::splitAmounts($reservation->total);

        $refundResult = PayoutService::transfer(
            $clientUser,
            $refundAmount,
            'REFUND-RES-' . $reservation->id,
            'Remboursement réservation ' . $reservation->code
        );

        $owner = $reservation->hotel?->user;
        if ($reservation->payout_transfer_id) {
            // Le prestataire a déjà reçu l'intégralité de son reversement avant l'annulation
            // (ex: check-in pointé juste avant) — Jèko ne permet pas de le récupérer
            // automatiquement, régularisation manuelle nécessaire.
            Log::warning('Annulation après reversement prestataire déjà effectué : régularisation manuelle nécessaire.', [
                'reservation_id' => $reservation->id,
                'payout_transfer_id' => $reservation->payout_transfer_id,
                'provider_share_due' => $providerAmount,
            ]);
        } elseif ($owner) {
            $ownerResult = PayoutService::transfer(
                $owner,
                $providerAmount,
                'PAYOUT-RES-' . $reservation->id . '-CANCEL',
                'Indemnité annulation réservation ' . $reservation->code
            );

            if ($ownerResult['status'] !== null) {
                $reservation->update([
                    'payout_transfer_id' => $ownerResult['transfer_id'],
                    'payout_status' => $ownerResult['status'],
                    'payout_failure_reason' => $ownerResult['failure_reason'],
                ]);
            }
        }

        self::reverseCommissionAndStats($reservation);

        $reservation->update([
            'status' => 'annulée',
            'cancelled_at' => now(),
            'cancellation_fee_amount' => $fee,
            'admin_fee_amount' => $adminAmount,
            'refund_amount' => $refundAmount,
            'refund_transfer_id' => $refundResult['transfer_id'],
            'refund_status' => $refundResult['status'],
            'refund_failure_reason' => $refundResult['failure_reason'],
        ]);

        if ($clientUser) {
            try {
                $clientUser->notify(new ReservationCancelledNotification($reservation->fresh(), $refundAmount, $fee));
            } catch (\Throwable) {
            }
        }

        return [
            'ok' => true,
            'error' => null,
            'message' => 'Réservation annulée, remboursement initié.',
            'fee' => $fee,
            'refundAmount' => $refundAmount,
            'refundStatus' => $refundResult['status'],
        ];
    }

    /**
     * Annule les effets comptables de markReservationPaid() : commission remise à zéro,
     * statistiques hôtel/client décrémentées.
     */
    public static function reverseCommissionAndStats(Reservation $reservation): void
    {
        $reservation->hotel?->decrement('revenue', min($reservation->total, $reservation->hotel->revenue));
        if ($reservation->hotel && $reservation->hotel->checkins > 0) {
            $reservation->hotel->decrement('checkins');
        }

        $client = $reservation->client;
        if ($client) {
            $client->decrement('total_spend', min($reservation->total, $client->total_spend));
            if ($client->total_stays > 0) {
                $client->decrement('total_stays');
            }
        }

        $reservation->update(['commission_rate' => null, 'commission_amount' => null]);
    }

    /** Annulation remboursable tant qu'on est dans la fenêtre fixe après le paiement. */
    public static function canCancelCommande(Commande $commande): bool
    {
        if (! in_array($commande->statut, ['payé', 'payée'], true)) {
            return false;
        }

        return $commande->created_at->greaterThan(now()->subHours(self::COMMANDE_CANCEL_WINDOW_HOURS));
    }

    /**
     * @return array{ok: bool, error: ?string, message: string, fee?: int, refundAmount?: int, refundStatus?: ?string}
     */
    public static function cancelCommande(Commande $commande): array
    {
        if (! in_array($commande->statut, ['payé', 'payée'], true)) {
            return ['ok' => false, 'error' => 'not_paid', 'message' => 'Cette commande ne peut plus être annulée.'];
        }

        if (! self::canCancelCommande($commande)) {
            return ['ok' => false, 'error' => 'too_late', 'message' => "Le délai d'annulation (48h après l'achat) est dépassé."];
        }

        if (! $commande->jeko_reference) {
            // Payée en espèces/virement : l'argent n'est jamais passé par Jèko, impossible et
            // incorrect d'y puiser un remboursement — reste géré manuellement par le vendeur.
            foreach ($commande->lignes as $ligne) {
                if ($ligne->product) {
                    $ligne->product->increment('stock', $ligne->quantite);
                }
                $ligne->update(['commission_rate' => null, 'commission_amount' => null]);
            }
            $commande->update(['statut' => 'annulée', 'cancelled_at' => now()]);

            return ['ok' => true, 'error' => null, 'message' => 'Commande annulée, articles remis en stock. Remboursement à gérer directement avec le client (paiement espèces/virement).'];
        }

        $clientUser = $commande->user;
        if (! $clientUser || ! $clientUser->hasPayoutContact()) {
            return [
                'ok' => false,
                'error' => 'missing_payout_contact',
                'message' => "Enregistrez d'abord vos coordonnées de paiement (mobile money ou compte bancaire) pour recevoir votre remboursement.",
            ];
        }

        ['fee' => $fee, 'adminAmount' => $adminAmount, 'providerAmount' => $providerAmount, 'refundAmount' => $refundAmount] = self::splitAmounts($commande->total);

        $refundResult = PayoutService::transfer(
            $clientUser,
            $refundAmount,
            'REFUND-CMD-' . $commande->id,
            'Remboursement commande #' . $commande->id
        );

        $lignes = $commande->lignes()->with('product.user')->get();

        foreach ($lignes as $ligne) {
            if ($ligne->product) {
                $ligne->product->increment('stock', $ligne->quantite);
            }
        }

        $cancellableLignes = $lignes->filter(fn ($l) => $l->product && $l->product->user_id && ! $l->payout_transfer_id);

        foreach ($cancellableLignes->groupBy(fn ($l) => $l->product->user_id) as $ownerLignes) {
            $owner = $ownerLignes->first()->product->user;
            if (! $owner) {
                continue;
            }

            $ownerLineTotal = (int) $ownerLignes->sum(fn ($l) => $l->quantite * $l->prix);
            $ownerShare = $commande->total > 0 ? (int) round($providerAmount * $ownerLineTotal / $commande->total) : 0;

            $ownerResult = PayoutService::transfer(
                $owner,
                $ownerShare,
                'PAYOUT-CMD-' . $commande->id . '-' . $owner->id . '-CANCEL',
                'Indemnité annulation commande #' . $commande->id
            );

            if ($ownerResult['status'] !== null) {
                LigneCommande::whereIn('id', $ownerLignes->pluck('id'))->update([
                    'payout_transfer_id' => $ownerResult['transfer_id'],
                    'payout_status' => $ownerResult['status'],
                    'payout_failure_reason' => $ownerResult['failure_reason'],
                ]);
            }
        }

        $alreadyPaidOutLignes = $lignes->filter(fn ($l) => $l->payout_transfer_id);
        if ($alreadyPaidOutLignes->isNotEmpty()) {
            Log::warning('Annulation de commande après reversement prestataire déjà effectué : régularisation manuelle nécessaire.', [
                'commande_id' => $commande->id,
                'ligne_ids' => $alreadyPaidOutLignes->pluck('id')->all(),
            ]);
        }

        foreach ($lignes as $ligne) {
            $ligne->update(['commission_rate' => null, 'commission_amount' => null]);
        }

        $commande->update([
            'statut' => 'annulée',
            'cancelled_at' => now(),
            'cancellation_fee_amount' => $fee,
            'admin_fee_amount' => $adminAmount,
            'refund_amount' => $refundAmount,
            'refund_transfer_id' => $refundResult['transfer_id'],
            'refund_status' => $refundResult['status'],
            'refund_failure_reason' => $refundResult['failure_reason'],
        ]);

        return [
            'ok' => true,
            'error' => null,
            'message' => 'Commande annulée, remboursement initié, les articles ont été remis en stock.',
            'fee' => $fee,
            'refundAmount' => $refundAmount,
            'refundStatus' => $refundResult['status'],
        ];
    }
}
