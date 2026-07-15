<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    // 🛒 Afficher page checkout
    public function create()
    {
        $panier = session('panier', []);

        $produits = [];
        $total = 0;

        foreach ($panier as $id => $qty) {
            $product = Product::find($id);

            if ($product) {
                $product->qty = $qty;
                $product->subtotal = $product->price * $qty;

                $total += $product->subtotal;

                $produits[] = $product;
            }
        }

        return view('commande.create', compact('produits', 'total'));
    }

    // 💳 Valider commande (depuis le panier)
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:carte_bancaire,orange_money,mtn_momo,moov_money,wave',
            'phone_number'   => 'nullable|string|max:20',
            'card_holder'    => 'nullable|string|max:100',
            'card_number'    => 'nullable|string|max:25',
        ]);

        $panier = session('panier', []);

        if (empty($panier)) {
            return back()->with('error', 'Panier vide');
        }

        $total    = 0;
        $commande = Commande::create([
            'user_id' => Auth::id(),
            'total'   => 0,
            'statut'  => 'en_attente',
        ]);

        foreach ($panier as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                LigneCommande::create([
                    'commande_id' => $commande->id,
                    'product_id'  => $id,
                    'quantite'    => $qty,
                    'prix'        => $product->price,
                ]);
                $total += $product->price * $qty;
                $product->decrement('stock', $qty);
            }
        }

        session()->forget('panier');

        $commande->update([
            'total'              => $total,
            'statut'             => 'payé',
            'mode_paiement'      => $request->payment_method,
            'transaction_id'     => uniqid('TXN_'),
            'payment_reference'  => $this->buildPaymentReference($request),
        ]);

        return redirect()->route('commande.success', $commande->id);
    }

    // ⚡ Achat direct depuis la page produit
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|integer|exists:products,id',
            'qty'            => 'required|integer|min:1',
            'payment_method' => 'required|string|in:carte_bancaire,orange_money,mtn_momo,moov_money,wave',
            'phone_number'   => 'nullable|string|max:20',
            'card_holder'    => 'nullable|string|max:100',
            'card_number'    => 'nullable|string|max:25',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty     = (int) $request->qty;

        if ($product->stock < $qty) {
            return back()->with('error', 'Stock insuffisant.');
        }

        $commande = Commande::create([
            'user_id'            => Auth::id(),
            'total'              => $product->price * $qty,
            'statut'             => 'payé',
            'mode_paiement'      => $request->payment_method,
            'transaction_id'     => uniqid('TXN_'),
            'payment_reference'  => $this->buildPaymentReference($request),
        ]);

        LigneCommande::create([
            'commande_id' => $commande->id,
            'product_id'  => $product->id,
            'quantite'    => $qty,
            'prix'        => $product->price,
        ]);

        $product->decrement('stock', $qty);

        return redirect()->route('commande.success', $commande->id);
    }

    private function buildPaymentReference(Request $request): ?string
    {
        if ($request->payment_method === 'carte_bancaire') {
            $raw    = preg_replace('/\s+/', '', $request->card_number ?? '');
            $masked = strlen($raw) >= 4 ? '**** **** **** ' . substr($raw, -4) : '****';
            $holder = strtoupper(trim($request->card_holder ?? ''));
            return $holder ? $holder . ' · ' . $masked : $masked;
        }

        $phone = trim($request->phone_number ?? '');
        return $phone ? '+225 ' . $phone : null;
    }

    // ✅ succès
    public function success($id)
    {
        $commande = Commande::with('lignes.product')->findOrFail($id);

        return view('commande.success', compact('commande'));
    }

    public function facture($id)
    {
        $commande = Commande::with('lignes')->findOrFail($id);

        $pdf = Pdf::loadView('commande.facture', compact('commande'));

        return $pdf->download('facture_'.$id.'.pdf');
    }

    public function commandes()
    {
        $commandes = Commande::latest()->get();

        $total = Commande::sum('total');
        $nb = Commande::count();

        return view('admin.commandes', compact('commandes', 'total', 'nb'));
    }

    public function mesCommandes(Request $request)
    {
        $query = Commande::with('lignes.product')
            ->where('user_id', Auth::id());

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $commandes = $query->latest()->get();

        $total  = $commandes->sum('total');
        $nb     = $commandes->count();
        $payees = $commandes->whereIn('statut', ['payé', 'payée'])->count();

        return view('client.commandes', compact('commandes', 'total', 'nb', 'payees'));
    }
}