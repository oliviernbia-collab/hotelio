<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facturation;
use Illuminate\Http\Request;

class FacturationController extends Controller
{
    public function index(Request $request)
    {
        $query = Facturation::query();

        if ($request->filled('status'))   $query->where('statut', $request->status);
        if ($request->filled('search'))   $query->where('client', 'like', '%'.$request->search.'%');

        $facturations = $query->latest('date_facture')->paginate(20);

        $stats = [
            'total'    => Facturation::sum('montant'),
            'payees'   => Facturation::where('statut', 'paid')->sum('montant'),
            'impayees' => Facturation::where('statut', 'pending')->sum('montant'),
            'en_retard'=> Facturation::where('statut', 'overdue')->sum('montant'),
        ];

        return response()->json(['data' => $facturations, 'stats' => $stats]);
    }

    public function show(Facturation $facturation)
    {
        return response()->json($facturation);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client'         => 'required|string',
            'prestation'     => 'required|string',
            'date_facture'   => 'required|date',
            'date_echeance'  => 'required|date',
            'montant'        => 'required|numeric|min:0',
            'mode_paiement'  => 'nullable|string',
            'notes'          => 'nullable|string',
            'lignes'         => 'nullable|array',
        ]);

        $data['numero'] = 'FAC-'.now()->format('Y').'-'.str_pad(Facturation::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['statut'] = 'pending';
        $data['montant_paye'] = 0;

        return response()->json(Facturation::create($data), 201);
    }

    public function destroy(Facturation $facturation)
    {
        $facturation->delete();

        return response()->json(['message' => 'Facturation supprimée.']);
    }
}
