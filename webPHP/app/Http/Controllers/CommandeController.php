<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Product;
use App\Services\JekoClient;
use App\Services\RefundService;
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

        $commande->update([
            'total'             => $total,
            'mode_paiement'     => $request->payment_method,
            'payment_reference' => $this->buildPaymentReference($request),
        ]);

        return $this->initiateCommandePayment($commande, $request->payment_method, keepCartOnFailure: true);
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
            'user_id'           => Auth::id(),
            'total'             => $product->price * $qty,
            'statut'            => 'en_attente',
            'mode_paiement'     => $request->payment_method,
            'payment_reference' => $this->buildPaymentReference($request),
        ]);

        LigneCommande::create([
            'commande_id' => $commande->id,
            'product_id'  => $product->id,
            'quantite'    => $qty,
            'prix'        => $product->price,
        ]);

        $product->decrement('stock', $qty);

        return $this->initiateCommandePayment($commande, $request->payment_method, keepCartOnFailure: false);
    }

    /**
     * Lance le paiement d'une commande boutique : confirmation immédiate pour
     * Espèces/Carte (Jèko ne les gère pas), sinon redirection vers la page sécurisée
     * Jèko. Même schéma que DashboardController::initiatePayment() pour les réservations.
     */
    private function initiateCommandePayment(Commande $commande, string $paymentMethod, bool $keepCartOnFailure)
    {
        $jekoMethod = JekoClient::mapPaymentMethod($paymentMethod);

        if (! $jekoMethod) {
            $this->markCommandePaid($commande);
            session()->forget('panier');

            return redirect()->route('commande.success', $commande->id);
        }

        $reference = 'HOTELIO-CMD-' . $commande->id . '-' . now()->timestamp;

        $result = JekoClient::createPaymentRequest([
            'storeId'     => config('services.jeko.store_id'),
            'amountCents' => (int) round($commande->total * 100),
            'currency'    => 'XOF',
            'reference'   => $reference,
            'paymentDetails' => [
                'type' => 'redirect',
                'data' => [
                    'paymentMethod' => $jekoMethod,
                    'successUrl'    => route('commande.payment.callback', ['commande' => $commande, 'result' => 'success']),
                    'errorUrl'      => route('commande.payment.callback', ['commande' => $commande, 'result' => 'error']),
                ],
            ],
        ]);

        if (empty($result['redirectUrl'])) {
            // Annule la commande en attente : on évite de laisser du stock réservé indéfiniment
            // pour un paiement qui n'a jamais démarré, et le panier reste intact pour réessayer.
            foreach ($commande->lignes as $ligne) {
                if ($ligne->product) {
                    $ligne->product->increment('stock', $ligne->quantite);
                }
            }
            $commande->lignes()->delete();
            $commande->delete();

            $error = $result['message'] ?? 'Impossible de lancer le paiement pour le moment. Réessayez ou choisissez un autre moyen de paiement.';

            return $keepCartOnFailure
                ? redirect()->route('commande.create')->with('error', $error)
                : back()->with('error', $error);
        }

        $commande->update([
            'jeko_reference'          => $reference,
            'jeko_payment_request_id' => $result['id'] ?? null,
        ]);

        session()->forget('panier');

        return redirect()->away($result['redirectUrl']);
    }

    /** Confirme le paiement effectif d'une commande : statut, transaction_id, décrément déjà fait à la création. */
    public function markCommandePaid(Commande $commande): void
    {
        if ($commande->statut === 'payé') {
            return;
        }

        $commande->update([
            'statut'         => 'payé',
            'transaction_id' => $commande->transaction_id ?? uniqid('TXN_'),
        ]);

        $this->applyCommandeCommissions($commande);
    }

    /** Calcule et enregistre la commission Hôtelio sur chaque ligne d'une commande payée. */
    private function applyCommandeCommissions(Commande $commande): void
    {
        foreach ($commande->lignes()->with('product.user')->get() as $ligne) {
            if (! $ligne->product) {
                continue;
            }

            $rate = \App\Services\PayoutService::rateForUser($ligne->product->user);

            $ligne->update([
                'commission_rate'   => $rate,
                'commission_amount' => (int) round($ligne->quantite * $ligne->prix * $rate / 100),
            ]);
        }
    }

    /**
     * Reverse automatiquement à chaque prestataire concerné sa part (lignes − commission)
     * d'une commande payée — un virement Jèko par propriétaire de produit, puisqu'un panier
     * peut mélanger des produits de prestataires différents. Appelé depuis le webhook Jèko
     * après confirmation du paiement — jamais depuis le paiement Espèces/Carte (le prestataire
     * a déjà l'argent en main dans ce cas, rien à reverser).
     */
    public function triggerOwnerPayouts(Commande $commande): void
    {
        $lignes = $commande->lignes()->with('product.user')->get()
            ->filter(fn ($l) => $l->product && $l->product->user_id && ! $l->payout_transfer_id);

        foreach ($lignes->groupBy(fn ($l) => $l->product->user_id) as $ownerLignes) {
            $owner = $ownerLignes->first()->product->user;
            if (! $owner) {
                continue;
            }

            $netAmount = (int) $ownerLignes->sum(fn ($l) => ($l->quantite * $l->prix) - ($l->commission_amount ?? 0));
            $reference = 'PAYOUT-CMD-' . $commande->id . '-' . $owner->id;

            $result = \App\Services\PayoutService::transfer($owner, $netAmount, $reference, 'Reversement commande #' . $commande->id);

            if ($result['status'] === null) {
                continue; // pas de contact de paiement enregistré ou montant sous le minimum
            }

            LigneCommande::whereIn('id', $ownerLignes->pluck('id'))->update([
                'payout_transfer_id'    => $result['transfer_id'],
                'payout_status'         => $result['status'],
                'payout_failure_reason' => $result['failure_reason'],
            ]);
        }
    }

    /** Relance un virement automatique Jèko qui a échoué pour un prestataire sur une commande. */
    public function retryLignePayout(LigneCommande $ligneCommande)
    {
        $commande = $ligneCommande->commande;
        $ownerId  = $ligneCommande->product?->user_id;

        LigneCommande::where('commande_id', $commande->id)
            ->when($ownerId, fn ($q) => $q->whereHas('product', fn ($p) => $p->where('user_id', $ownerId)))
            ->update(['payout_transfer_id' => null, 'payout_status' => null, 'payout_failure_reason' => null]);

        $this->triggerOwnerPayouts($commande->fresh());

        if ($ligneCommande->fresh()->payout_status === 'error') {
            return back()->with('error', 'Le virement a de nouveau échoué : ' . ($ligneCommande->fresh()->payout_failure_reason ?? 'raison inconnue'));
        }

        return back()->with('success', 'Virement relancé.');
    }

    /**
     * Retour du client depuis la page de paiement Jèko (succès ou échec).
     * La redirection seule ne prouve rien : on interroge l'API pour un retour immédiat,
     * la confirmation définitive arrive via le webhook (voir JekoWebhookController).
     */
    public function paymentCallback(Request $request, Commande $commande)
    {
        $this->authorizeCommande($commande);

        if ($commande->statut !== 'payé' && $commande->jeko_payment_request_id) {
            $result = JekoClient::getPaymentRequest($commande->jeko_payment_request_id);
            if (($result['status'] ?? null) === 'success') {
                $this->markCommandePaid($commande);
            }
        }

        if ($commande->fresh()->statut === 'payé') {
            return redirect()->route('commande.success', $commande->id)
                ->with('success', 'Paiement confirmé. Un email de confirmation vous a été envoyé.');
        }

        return redirect()->route('boutique.clientboutique')
            ->with('error', $request->query('result') === 'error'
                ? 'Le paiement a échoué ou a été annulé.'
                : 'Paiement en cours de confirmation. Si le statut ne change pas sous peu, contactez le support.');
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

    /** Vérifie que la commande appartient à l'utilisateur connecté (ou qu'il est admin). */
    private function authorizeCommande(Commande $commande): void
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $commande->user_id !== $user->id) {
            abort(403);
        }
    }

    // ✅ succès
    public function success($id)
    {
        $commande = Commande::with('lignes.product')->findOrFail($id);
        $this->authorizeCommande($commande);

        return view('commande.success', compact('commande'));
    }

    public function facture($id)
    {
        $commande = Commande::with('lignes')->findOrFail($id);
        $this->authorizeCommande($commande);

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

    /**
     * Annulation d'une commande par son client avec remboursement réel (voir RefundService) :
     * réintègre le stock, rembourse 95% du montant au client, verse 1% au(x) prestataire(s).
     */
    public function cancel(Commande $commande)
    {
        if ($commande->user_id !== Auth::id()) {
            abort(403);
        }

        $result = RefundService::cancelCommande($commande);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
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