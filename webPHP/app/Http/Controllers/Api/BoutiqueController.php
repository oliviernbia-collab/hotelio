<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Product;
use App\Services\CloudinaryUploader;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoutiqueController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search'))   $query->where('name', 'like', '%'.$request->search.'%');
        if ($request->filled('category')) $query->where('category', $request->category);

        return response()->json($query->where('stock', '>', 0)->get());
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    /** Produits du prestataire connecté — mirror backendMobile GET /boutique/products/mine. */
    public function mine(Request $request)
    {
        return response()->json(
            Product::where('user_id', $request->user()->id)->latest()->get()
        );
    }

    /** Mirror de DashboardController::storeProduct (web). */
    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'required|string|max:100',
            'emoji'       => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'products');
        }

        $data['user_id'] = $request->user()->id;

        return response()->json(Product::create($data), 201);
    }

    /** Mirror de DashboardController::update (produits, web). */
    public function updateProduct(Request $request, Product $product)
    {
        $user = $request->user();
        if ($user->role !== 'admin' && $product->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'price'       => 'sometimes|integer|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'category'    => 'sometimes|string|max:100',
            'emoji'       => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            CloudinaryUploader::destroy($product->image);
            $data['image'] = CloudinaryUploader::upload($request->file('image'), 'products');
        }

        $product->update($data);

        return response()->json($product);
    }

    /** Mirror de DashboardController::destroy (produits, web). */
    public function destroyProduct(Request $request, Product $product)
    {
        $user = $request->user();
        if ($user->role !== 'admin' && $product->user_id !== $user->id) {
            abort(403);
        }

        CloudinaryUploader::destroy($product->image);
        $product->delete();

        return response()->json(['message' => 'Produit supprimé.']);
    }

    public function cart(Request $request)
    {
        $cart = session('cart_'.$request->user()->id, []);

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $qty;
                $items[]  = ['product' => $product, 'quantity' => $qty, 'subtotal' => $subtotal];
                $total   += $subtotal;
            }
        }

        return response()->json(['items' => $items, 'total' => $total]);
    }

    public function addToCart(Request $request, $id)
    {
        $request->validate(['quantity' => 'sometimes|integer|min:1']);
        $qty = $request->quantity ?? 1;

        $product = Product::findOrFail($id);
        $key     = 'cart_'.$request->user()->id;
        $cart    = session($key, []);

        $cart[$id] = ($cart[$id] ?? 0) + $qty;
        session([$key => $cart]);

        return response()->json(['message' => 'Produit ajouté au panier.', 'quantity' => $cart[$id]]);
    }

    public function removeFromCart(Request $request, $id)
    {
        $key  = 'cart_'.$request->user()->id;
        $cart = session($key, []);
        unset($cart[$id]);
        session([$key => $cart]);

        return response()->json(['message' => 'Produit retiré du panier.']);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $key  = 'cart_'.$request->user()->id;
        $cart = session($key, []);

        if (empty($cart)) {
            return response()->json(['message' => 'Panier vide.'], 422);
        }

        DB::transaction(function () use ($cart, $request) {
            $total    = 0;
            $commande = Commande::create([
                'user_id'         => $request->user()->id,
                'total'           => 0,
                'statut'          => 'en_attente',
                'mode_paiement'   => $request->payment_method,
            ]);

            foreach ($cart as $productId => $qty) {
                $product   = Product::findOrFail($productId);
                $subtotal  = $product->price * $qty;
                $total    += $subtotal;

                LigneCommande::create([
                    'commande_id' => $commande->id,
                    'product_id'  => $productId,
                    'quantite'    => $qty,
                    'prix'        => $product->price,
                ]);

                $product->decrement('stock', $qty);
            }

            $commande->update(['total' => $total]);
        });

        session()->forget($key);

        return response()->json(['message' => 'Commande passée avec succès.']);
    }

    public function myOrders(Request $request)
    {
        $orders = Commande::where('user_id', $request->user()->id)
            ->with(['lignes.product'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function orderItems(Request $request, $id)
    {
        $commande = Commande::with('lignes.product')->findOrFail($id);
        $this->authorizeCommande($request, $commande);

        return response()->json($commande);
    }

    /**
     * Mirror de CommandeController::cancel (web) : remboursement réel via RefundService
     * (95% au client, 1% au(x) prestataire(s), stock réintégré) — jamais une simple annulation
     * de statut.
     */
    public function cancelOrder(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        $this->authorizeCommande($request, $commande);

        $result = RefundService::cancelCommande($commande);

        return response()->json(['message' => $result['message']], $result['ok'] ? 200 : 422);
    }

    private function authorizeCommande(Request $request, Commande $commande): void
    {
        if ($commande->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
