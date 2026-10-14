<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoutiqueController extends Controller
{
    /* ===================== BOUTIQUE ===================== */

    public function boutique()
    {
        $produits = Product::latest()->paginate(12);

        if (auth()->check() && auth()->user()->role === 'prestataire') {
            return view('boutique.index', compact('produits'));
        }

        return view('boutique.clientboutique', compact('produits'));
    }

    /* ===================== PRODUITS ===================== */

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:100',
            'emoji' => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('images/products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produit ajouté.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Product $product, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:100',
            'emoji' => 'nullable|string|max:4',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')
                ->store('images/products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé.');
    }

    /* ===================== PANIER ===================== */

    public function add($id)
    {
        $panier = session()->get('panier', []);

        $panier[$id] = ($panier[$id] ?? 0) + 1;

        session()->put('panier', $panier);

        return back();
    }

    public function decrease($id)
    {
        $panier = session()->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }

        session()->put('panier', $panier);

        return back();
    }

    public function show($id)
    {
        $produit = Product::findOrFail($id);

        return view('show', compact('produit'));
    }

    /* ===================== CALENDRIER ===================== */

    public function calendar()
    {
        $events = [
            ['date' => now()->addDays(2)->format('Y-m-d'), 'title' => 'Réservation', 'color' => '#5A67D8'],
            ['date' => now()->addDays(4)->format('Y-m-d'), 'title' => 'VIP', 'color' => '#F6AD55'],
            ['date' => now()->addDays(7)->format('Y-m-d'), 'title' => 'Inspection', 'color' => '#48BB78'],
        ];

        return view('calendar.index', compact('events'));
    }

    /* ===================== USERS ===================== */

    public function users()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    /* ===================== HOTELS ===================== */

    public function editHotel($id)
    {
        $hotel = Hotel::findOrFail($id);

        return view('hotels.edit', compact('hotel'));
    }

    public function create()
    {
        $panier = session()->get('panier', []);

        $produits = [];
        $total = 0;

        foreach ($panier as $id => $qty) {

            $product = \App\Models\Product::find($id);

            if ($product) {
                $produits[] = (object)[
                    'name' => $product->name,
                    'qty' => $qty,
                    'subtotal' => $product->price * $qty
                ];

                $total += $product->price * $qty;
            }
        }

        return view('commande.create', compact('produits', 'total'));
    }

        public function store(Request $request)
        {
            $produits = session()->get('panier', []);
            $total = 0;

            foreach ($produits as $id => $quantity) {
                $product = Product::find($id);
                if ($product) {
                    $total += $product->price * $quantity;
                }
            }

            // Logique de création de la commande et de la facture ici

            session()->forget('panier');

            return redirect()->route('commande.success', ['id' => 1])->with('success', 'Commande passée avec succès.');
        }
}