<?php

namespace App\Console\Commands;

use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Models\Commande;
use App\Models\Reservation;
use App\Services\RefundService;
use Illuminate\Console\Command;

class ReleaseDuePayouts extends Command
{
    protected $signature = 'payouts:release-due';

    protected $description = "Filet de sécurité : reverse les prestataires dont la fenêtre d'annulation est dépassée (date de check-in passée pour une réservation, 48h après l'achat pour une commande) sans que le reversement n'ait déjà été déclenché (normalement fait au check-in).";

    public function handle(DashboardController $dashboard, CommandeController $commandeCtrl): int
    {
        $reservations = Reservation::where('paid', true)
            ->whereNull('payout_transfer_id')
            ->where('status', '!=', 'annulée')
            ->whereDate('checkin', '<=', today())
            ->get();

        foreach ($reservations as $reservation) {
            $dashboard->triggerOwnerPayout($reservation);
        }

        $dueCommandeIds = Commande::where('statut', 'payé')
            ->where('created_at', '<=', now()->subHours(RefundService::COMMANDE_CANCEL_WINDOW_HOURS))
            ->pluck('id');

        foreach ($dueCommandeIds as $commandeId) {
            $commande = Commande::with('lignes.product.user')->find($commandeId);
            if ($commande) {
                $commandeCtrl->triggerOwnerPayouts($commande);
            }
        }

        $this->info(sprintf(
            'Reversements libérés : %d réservation(s), %d commande(s).',
            $reservations->count(),
            $dueCommandeIds->count()
        ));

        return self::SUCCESS;
    }
}
