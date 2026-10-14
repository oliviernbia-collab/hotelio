<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Ancien contrôleur de facturation (modèle `Facture`, distinct de `Facturation` utilisé
 * partout ailleurs). Seule `pdf()` est encore routée (`routes/web.php`) ; les autres
 * méthodes (index/create/store/edit/update/destroy) n'avaient aucune route et, pour
 * edit/update/destroy, aucune vérification d'autorisation — supprimées pour réduire la
 * surface d'attaque en cas de re-routage accidentel futur.
 */
class FactureController extends Controller
{
    public function pdf(Facture $facture)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->role !== 'admin') {
            $reservation = $facture->reservation;

            if (! $reservation) {
                // Facture sans réservation liée : pas de propriétaire identifiable, seul un admin y accède.
                abort(403);
            }

            if ($user->role === 'client') {
                $client = \App\Models\Client::where('user_id', $user->id)->first();
                if ($reservation->client_id !== $client?->id) {
                    abort(403);
                }
            } elseif (! $user->getHotelIds()->contains($reservation->hotel_id)) {
                abort(403);
            }
        }

        $pdf = Pdf::loadView('facturations.pdf', compact('facture'));

        return $pdf->download('facture_'.$facture->numero.'.pdf');
    }
}
