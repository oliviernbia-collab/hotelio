<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    // Liste
    public function index()
    {
        $factures = Facture::latest()->get();

        $stats = [
            'total' => Facture::sum('total_ttc'),
            'payees' => Facture::where('statut', 'payé')->sum('total_ttc'),
            'impayees' => Facture::where('statut', 'non payé')->sum('total_ttc'),
        ];

        return view('facturations.index', compact('factures', 'stats'));
    }

    // Formulaire
    public function create()
    {
        return view('facturations.create');
    }

    // STORE PRO
    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.designation' => 'required',
            'items.*.quantite' => 'required|numeric|min:1',
            'items.*.prix' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $numero = 'FAC-' . date('Y') . '-' . str_pad(Facture::count() + 1, 4, '0', STR_PAD_LEFT);

            $facture = Facture::create([
                'client'          => $request->client,
                'hotel'           => $request->hotel,
                'reservation_id'  => $request->reservation_id,
                'numero'          => $numero,
                'date_facture'    => now(),
                'tva'             => 18,
                'taxe_hoteliere'  => 2,
                'statut'          => 'non payé',
                'mode_paiement'   => $request->mode_paiement,
                'notes'           => $request->notes,
            ]);

            $totalHT = 0;

            foreach ($request->items as $item) {
                $total = $item['quantite'] * $item['prix'];

                $facture->items()->create([
                    'designation' => $item['designation'],
                    'quantite'    => $item['quantite'],
                    'prix'        => $item['prix'],
                    'total'       => $total,
                ]);

                $totalHT += $total;
            }

            // TVA 18% + Taxe Hôtelière 2% = 20% conformément au droit fiscal ivoirien
            $montantTVA = round($totalHT * 18 / 100, 2);
            $montantTH  = round($totalHT * 2 / 100, 2);
            $totalTTC   = $totalHT + $montantTVA + $montantTH;

            $facture->update([
                'total_ht'       => $totalHT,
                'tva'            => $montantTVA,
                'taxe_hoteliere' => $montantTH,
                'total_ttc'      => $totalTTC,
            ]);

            DB::commit();

            return redirect()->route('facturations.index')
                ->with('success', 'Facture créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    // EDIT
    public function edit(Facture $facture)
    {
        return view('facturations.edit', compact('facture'));
    }

    // UPDATE (simplifié)
    public function update(Request $request, Facture $facture)
    {
        $facture->update([
            'client' => $request->client,
            'hotel' => $request->hotel,
            'statut' => $request->statut
        ]);

        return redirect()->route('facturations.index')
            ->with('success', 'Facture modifiée');
    }

    // DELETE
    public function destroy(Facture $facture)
    {
        $facture->delete();

        return back()->with('success', 'Facture supprimée');
    }

    // PDF
    public function pdf(Facture $facture)
    {
        $pdf = Pdf::loadView('facturations.pdf', compact('facture'));

        return $pdf->download('facture_'.$facture->numero.'.pdf');
    }
}