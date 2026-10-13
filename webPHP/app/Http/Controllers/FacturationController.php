<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturation;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

class FacturationController extends Controller
{
    /**
     * Autorise l'accès à une facturation : admin (toutes), ou prestataire/employé
     * dont l'hôtel correspond à `hotel_id`. Une facturation sans hôtel rattaché
     * (ancien enregistrement créé avant que ce champ soit obligatoire) n'est
     * visible/modifiable que par un admin.
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

    public function index()
    {
        $hotelIds = Auth::user()->getHotelIds();
        $scope = fn ($query) => $query->when(
            Auth::user()->role !== 'admin',
            fn ($q) => $q->whereIn('hotel_id', $hotelIds)
        );

        // All records for stats (never cut off by pagination)
        $allFactures = $scope(Facturation::query())->latest()->get();

        // Paginated records for the table display
        $factures = $scope(Facturation::query())->latest()->paginate(15)->withQueryString();

        $annee = now()->year;
        $mois  = now()->month;

        $facturesMois = $allFactures->filter(
            fn($f) => $f->date_facture && $f->date_facture->year == $annee && $f->date_facture->month == $mois
        );

        $moisPrec  = now()->subMonthNoOverflow()->month;
        $anneePrec = now()->subMonthNoOverflow()->year;
        $countMoisPrec = $scope(Facturation::query())
            ->whereYear('date_facture', $anneePrec)
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
        $recentActivity = $scope(Facturation::query())->latest('updated_at')->limit(10)->get()->map(fn($f) => [
            'numero'     => $f->numero,
            'client'     => $f->client,
            'montant'    => (float) $f->montant,
            'statut'     => $f->statut,
            'updated_at' => $f->updated_at,
        ]);

        $clients = $scope(Facturation::query())->select('client')->distinct()->orderBy('client')->pluck('client');
        $hotels  = Hotel::whereIn('id', $hotelIds)->get();

        // Pre-mapped for JS injection (avoids Blade parser issues with multi-line closures)
        $facturesJs = collect($factures->items())->map(function ($f) {
            return [
                'id'         => $f->numero,
                'dbId'       => $f->id,
                'hotelId'    => $f->hotel_id,
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

        return view('facturations.index', compact('factures', 'facturesJs', 'stats', 'paymentModes', 'recentActivity', 'clients', 'hotels'));
    }

    private function facturationValidationRules(): array
    {
        return [
            'hotel_id'      => 'required|exists:hotels,id',
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
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->facturationValidationRules());

        if (! Auth::user()->getHotelIds()->contains($validated['hotel_id'])) {
            abort(403);
        }

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
                'hotel_id'      => $facture->hotel_id,
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
        $this->authorizeFacturation($facturation);

        return response()->json($facturation);
    }

    public function update(Request $request, Facturation $facturation)
    {
        $this->authorizeFacturation($facturation);

        $validated = $request->validate($this->facturationValidationRules());

        if (! Auth::user()->getHotelIds()->contains($validated['hotel_id'])) {
            abort(403);
        }
        $validated['montant_paye'] = $validated['montant_paye'] ?? 0;

        $facturation->update($validated);
        $facturation->refresh();

        return response()->json([
            'success' => true,
            'facture' => [
                'id'            => $facturation->id,
                'numero'        => $facturation->numero,
                'hotel_id'      => $facturation->hotel_id,
                'client'        => $facturation->client,
                'prestation'    => $facturation->prestation,
                'date_facture'  => $facturation->date_facture?->format('Y-m-d'),
                'date_echeance' => $facturation->date_echeance?->format('Y-m-d'),
                'montant'       => (float) $facturation->montant,
                'montant_paye'  => (float) $facturation->montant_paye,
                'statut'        => $facturation->statut,
                'mode_paiement' => $facturation->mode_paiement,
                'notes'         => $facturation->notes ?? '',
                'lignes'        => $facturation->lignes ?? [],
            ],
        ]);
    }

    /** Enregistre un paiement (total ou partiel) — le statut est déduit automatiquement. */
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

        return response()->json([
            'success' => true,
            'facture' => [
                'id'           => $facturation->id,
                'montant_paye' => (float) $facturation->montant_paye,
                'statut'       => $facturation->statut,
                'mode_paiement'=> $facturation->mode_paiement,
            ],
        ]);
    }

    public function destroy(int $id)
    {
        $facturation = Facturation::findOrFail($id);
        $this->authorizeFacturation($facturation);
        $facturation->delete();

        return response()->json(['success' => true]);
    }
}
