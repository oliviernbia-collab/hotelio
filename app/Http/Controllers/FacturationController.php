<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturation;

class FacturationController extends Controller
{
    public function index()
    {
        // All records for stats (never cut off by pagination)
        $allFactures = Facturation::latest()->get();

        // Paginated records for the table display
        $factures = Facturation::latest()->paginate(15)->withQueryString();

        $annee = now()->year;
        $mois  = now()->month;

        $facturesMois = $allFactures->filter(
            fn($f) => $f->date_facture && $f->date_facture->year == $annee && $f->date_facture->month == $mois
        );

        $moisPrec  = now()->subMonthNoOverflow()->month;
        $anneePrec = now()->subMonthNoOverflow()->year;
        $countMoisPrec = Facturation::whereYear('date_facture', $anneePrec)
            ->whereMonth('date_facture', $moisPrec)->count();

        $totalMontant = (float) $allFactures->sum('montant');
        $totalPaye    = (float) $allFactures->sum('montant_paye');

        $enAttente = $allFactures->whereIn('statut', ['pending', 'overdue'])
            ->sum(fn($f) => max(0, (float)$f->montant - (float)$f->montant_paye));

        $stats = [
            'count_mois'        => $facturesMois->count(),
            'count_payees_mois' => $facturesMois->where('statut', 'paid')->count(),
            'encaisse'          => $totalPaye,
            'en_attente'        => $enAttente,
            'impayees_count'    => $allFactures->whereIn('statut', ['pending', 'overdue'])->count(),
            'taux_recouvrement' => $totalMontant > 0 ? round($totalPaye / $totalMontant * 100) : 0,
            'delta_count'       => $facturesMois->count() - $countMoisPrec,
        ];

        // Répartition des modes de paiement (sur montants encaissés)
        $grouped    = $allFactures->where('montant_paye', '>', 0)->groupBy('mode_paiement');
        $grandTotal = max(1, $allFactures->sum('montant_paye'));
        $paymentModes = $grouped->map(fn($grp, $mode) => [
            'mode'  => $mode,
            'total' => (float) $grp->sum('montant_paye'),
            'pct'   => round((float)$grp->sum('montant_paye') / $grandTotal * 100),
        ])->sortByDesc('total')->values();

        // Activité récente (10 dernières factures modifiées)
        $recentActivity = Facturation::latest('updated_at')->limit(10)->get()->map(fn($f) => [
            'numero'     => $f->numero,
            'client'     => $f->client,
            'montant'    => (float) $f->montant,
            'statut'     => $f->statut,
            'updated_at' => $f->updated_at,
        ]);

        $clients = Facturation::select('client')->distinct()->orderBy('client')->pluck('client');

        // Pre-mapped for JS injection (avoids Blade parser issues with multi-line closures)
        $facturesJs = collect($factures->items())->map(function ($f) {
            return [
                'id'         => $f->numero,
                'dbId'       => $f->id,
                'client'     => $f->client,
                'prestation' => $f->prestation,
                'date'       => $f->date_facture  ? $f->date_facture->format('Y-m-d')  : '',
                'due'        => $f->date_echeance ? $f->date_echeance->format('Y-m-d') : '',
                'amount'     => (float) $f->montant,
                'paid'       => (float) $f->montant_paye,
                'status'     => $f->statut,
                'pay'        => $f->mode_paiement,
                'notes'      => $f->notes ?? '',
                'lines'      => $f->lignes ?? [],
            ];
        })->values();

        return view('facturations.index', compact('factures', 'facturesJs', 'stats', 'paymentModes', 'recentActivity', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client'        => 'required|string|max:255',
            'prestation'    => 'required|string|max:500',
            'date_facture'  => 'required|date',
            'date_echeance' => 'required|date',
            'montant'       => 'required|numeric|min:0',
            'montant_paye'  => 'nullable|numeric|min:0',
            'statut'        => 'required|in:paid,pending,overdue,partial,draft',
            'mode_paiement' => 'required|string|max:100',
            'notes'         => 'nullable|string',
            'lignes'        => 'nullable|array',
        ]);

        $year  = now()->year;
        $count = Facturation::whereYear('created_at', $year)->count() + 1;
        $validated['numero']       = 'FAC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['montant_paye'] = $validated['montant_paye'] ?? 0;

        $facture = Facturation::create($validated);
        $facture->refresh();

        return response()->json([
            'success' => true,
            'facture' => [
                'id'            => $facture->id,
                'numero'        => $facture->numero,
                'client'        => $facture->client,
                'prestation'    => $facture->prestation,
                'date_facture'  => $facture->date_facture?->format('Y-m-d'),
                'date_echeance' => $facture->date_echeance?->format('Y-m-d'),
                'montant'       => (float) $facture->montant,
                'montant_paye'  => (float) $facture->montant_paye,
                'statut'        => $facture->statut,
                'mode_paiement' => $facture->mode_paiement,
                'notes'         => $facture->notes ?? '',
                'lignes'        => $facture->lignes ?? [],
            ],
        ]);
    }

    public function show(Facturation $facturation)
    {
        return response()->json($facturation);
    }

    public function destroy(int $id)
    {
        Facturation::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
