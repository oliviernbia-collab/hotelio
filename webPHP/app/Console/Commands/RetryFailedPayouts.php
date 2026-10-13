<?php

namespace App\Console\Commands;

use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Reservation;
use Illuminate\Console\Command;

class RetryFailedPayouts extends Command
{
    protected $signature = 'payouts:retry-failed';

    protected $description = 'Relance automatiquement les virements de reversement Jèko en échec (réservations et achats boutique).';

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

        $this->info(sprintf(
            'Relance terminée : %d réservation(s), %d commande(s).',
            $reservations->count(),
            $failedCommandeIds->count()
        ));

        return self::SUCCESS;
    }
}
