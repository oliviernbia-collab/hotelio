<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facturation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacturationController extends Controller
{
    /**
     * Autorise l'accès à une facturation : admin (toutes), ou prestataire/employé dont
     * l'hôtel correspond à `hotel_id`. Miroir exact de FacturationController::authorizeFacturation
     * (web) — ce contrôleur API ne filtrait auparavant par aucun hôtel (IDOR).
     */
    private function authorizeFacturation(Facturation $facturation): void
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return;
        }

        if (! $facturation->hotel_id || ! $user->getHotelIds()->contains($facturation->hotel_id)) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $hotelIds = Auth::user()->getHotelIds();
        $scope = fn ($query) => $query->when(
            Auth::user()->role !== 'admin',
            fn ($q) => $q->whereIn('hotel_id', $hotelIds)
        );

        $query = $scope(Facturation::query());

        if ($request->filled('status'))   $query->where('statut', $request->status);
        if ($request->filled('search'))   $query->where('client', 'like', '%'.$request->search.'%');

        $facturations = $query->latest('date_facture')->paginate(20);

        $stats = [
            'total'    => $scope(Facturation::query())->sum('montant'),
            'payees'   => $scope(Facturation::query())->where('statut', 'paid')->sum('montant'),
            'impayees' => $scope(Facturation::query())->where('statut', 'pending')->sum('montant'),
            'en_retard'=> $scope(Facturation::query())->where('statut', 'overdue')->sum('montant'),
        ];

        return response()->json(['data' => $facturations, 'stats' => $stats]);
    }

    public function show(Facturation $facturation)
    {
        $this->authorizeFacturation($facturation);

        return response()->json($facturation);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'       => 'required|exists:hotels,id',
            'client'         => 'required|string',
            'prestation'     => 'required|string',
            'date_facture'   => 'required|date',
            'date_echeance'  => 'required|date',
            'montant'        => 'required|numeric|min:0',
            'mode_paiement'  => 'nullable|string',
            'notes'          => 'nullable|string',
            'lignes'         => 'nullable|array',
        ]);

        if (! Auth::user()->getHotelIds()->contains($data['hotel_id'])) {
            abort(403);
        }

        $data['numero'] = 'FAC-'.now()->format('Y').'-'.str_pad(Facturation::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['statut'] = 'pending';
        $data['montant_paye'] = 0;

        return response()->json(Facturation::create($data), 201);
    }

    public function update(Request $request, Facturation $facturation)
    {
        $this->authorizeFacturation($facturation);

        $data = $request->validate([
            'hotel_id'       => 'sometimes|exists:hotels,id',
            'client'         => 'sometimes|string',
            'prestation'     => 'sometimes|string',
            'date_facture'   => 'sometimes|date',
            'date_echeance'  => 'sometimes|date',
            'montant'        => 'sometimes|numeric|min:0',
            'mode_paiement'  => 'nullable|string',
            'notes'          => 'nullable|string',
            'lignes'         => 'nullable|array',
        ]);

        if (isset($data['hotel_id']) && ! Auth::user()->getHotelIds()->contains($data['hotel_id'])) {
            abort(403);
        }

        $facturation->update($data);

        return response()->json($facturation->fresh());
    }

    /** Mirror de FacturationController::pay (web) : enregistre un paiement (total ou partiel). */
    public function pay(Request $request, Facturation $facturation)
    {
        $this->authorizeFacturation($facturation);

        $data = $request->validate([
            'montant_paye'  => 'required|numeric|min:0',
            'mode_paiement' => 'nullable|string|max:100',
        ]);

        $paye  = (float) $data['montant_paye'];
        $total = (float) $facturation->montant;

        $statut = match (true) {
            $paye >= $total => 'paid',
            $paye > 0       => 'partial',
            default         => 'pending',
        };

        $facturation->update([
            'montant_paye'  => $paye,
            'statut'        => $statut,
            'mode_paiement' => $data['mode_paiement'] ?? $facturation->mode_paiement,
        ]);

        return response()->json($facturation->fresh());
    }

    public function destroy(Facturation $facturation)
    {
        $this->authorizeFacturation($facturation);

        $facturation->delete();

        return response()->json(['message' => 'Facturation supprimée.']);
    }
}
