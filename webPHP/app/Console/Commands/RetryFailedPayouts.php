<?php

namespace App\Console\Commands;

use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Reservation;
use App\Services\PayoutService;
use Illuminate\Console\Command;

class RetryFailedPayouts extends Command
{
    protected $signature = 'payouts:retry-failed';

    protected $description = 'Relance automatiquement les virements Jèko en échec : reversements prestataires (réservations et achats boutique) et remboursements clients.';

    public function handle(DashboardController $dashboard, CommandeController $commandeCtrl): int
    {
        $reservations = Reservation::where('payout_status', 'error')->get();
        foreach ($reservations as $reservation) {
            $reservation->update(['payout_transfer_id' => null, 'payout_status' => null, 'payout_failure_reason' => null]);
            $dashboard->triggerOwnerPayout($reservation->fresh());
        }

        $failedCommandeIds = LigneCommande::where('payout_status', 'error')->pluck('commande_id')->unique();
        foreach ($failedCommandeIds as $commandeId) {
            LigneCommande::where('commande_id', $commandeId)
                ->where('payout_status', 'error')
                ->update(['payout_transfer_id' => null, 'payout_status' => null, 'payout_failure_reason' => null]);

            $commande = Commande::with('lignes.product.user')->find($commandeId);
            if ($commande) {
                $commandeCtrl->triggerOwnerPayouts($commande);
            }
        }

        $failedRefundReservations = Reservation::where('refund_status', 'error')->get();
        foreach ($failedRefundReservations as $reservation) {
            $clientUser = $reservation->client?->user;
            if (! $clientUser || ! $reservation->refund_amount) {
                continue;
            }

            $result = PayoutService::transfer($clientUser, $reservation->refund_amount, 'REFUND-RES-' . $reservation->id, 'Remboursement réservation ' . $reservation->code);
            if ($result['status'] !== null) {
                $reservation->update([
                    'refund_transfer_id' => $result['transfer_id'],
                    'refund_status' => $result['status'],
                    'refund_failure_reason' => $result['failure_reason'],
                ]);
            }
        }

        $failedRefundCommandes = Commande::where('refund_status', 'error')->get();
        foreach ($failedRefundCommandes as $commande) {
            $clientUser = $commande->user;
            if (! $clientUser || ! $commande->refund_amount) {
                continue;
            }

            $result = PayoutService::transfer($clientUser, $commande->refund_amount, 'REFUND-CMD-' . $commande->id, 'Remboursement commande #' . $commande->id);
            if ($result['status'] !== null) {
                $commande->update([
                    'refund_transfer_id' => $result['transfer_id'],
                    'refund_status' => $result['status'],
                    'refund_failure_reason' => $result['failure_reason'],
                ]);
            }
        }

        $this->info(sprintf(
            'Relance terminée : %d réservation(s), %d commande(s), %d remboursement(s) réservation, %d remboursement(s) commande.',
            $reservations->count(),
            $failedCommandeIds->count(),
            $failedRefundReservations->count(),
            $failedRefundCommandes->count()
        ));

        return self::SUCCESS;
    }
}
