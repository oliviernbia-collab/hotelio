<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function premiere()
    {
        $totalHotels    = Hotel::count();
        $featuredHotels = Hotel::orderByDesc('rating')->take(8)->get();
        $cities         = Hotel::distinct()->orderBy('city')->pluck('city');

        // Hôtels par section — fallback sur les mieux notés si la ville est vide
        $allByRating = Hotel::orderByDesc('rating')->get();

        $hotelsAbidjan = $allByRating->filter(fn($h) => stripos($h->city, 'Abidjan') !== false)->take(4);
        if ($hotelsAbidjan->isEmpty()) {
            $hotelsAbidjan = $allByRating->take(4);
        }

        $hotelsYamoussoukro = $allByRating->filter(fn($h) => stripos($h->city, 'Yamoussoukro') !== false)->take(4);
        if ($hotelsYamoussoukro->isEmpty()) {
            $hotelsYamoussoukro = $allByRating->skip(4)->take(4);
        }

        $hotelsSanPedro = $allByRating->filter(
            fn($h) => stripos($h->city, 'Abidjan') === false && stripos($h->city, 'Yamoussoukro') === false
        )->take(4);
        if ($hotelsSanPedro->isEmpty()) {
            $hotelsSanPedro = $allByRating->take(4);
        }

        return view('premiere', compact(
            'totalHotels', 'featuredHotels', 'cities',
            'hotelsAbidjan', 'hotelsYamoussoukro', 'hotelsSanPedro'
        ));
    }

    public function premierePost(Request $request)
    {
        return redirect()->route('hotels.public', $request->only(['ville', 'checkin', 'checkout', 'voyageurs']));
    }

    public function hotelSearch(Request $request)
    {
        $query = Hotel::query()->where('status', '!=', 'Fermé');

        if ($request->filled('ville')) {
            $search = $request->ville;
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                  ->orWhere('quartier', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stars')) {
            $query->where('stars', '>=', (int) $request->stars);
        }

        if ($request->filled('prix_max')) {
            $query->where('price', '<=', (int) $request->prix_max);
        }

        if ($request->filled('categorie')) {
            $query->where('category', $request->categorie);
        }

        $hotels     = $query->orderByDesc('rating')->paginate(12)->withQueryString();
        $cities     = Hotel::distinct()->orderBy('city')->pluck('city');
        $categories = Hotel::distinct()->orderBy('category')->pluck('category');
        $totalCount = Hotel::count();

        $favoriteIds = auth()->check()
            ? auth()->user()->favoriteHotels()->pluck('hotel_id')
            : collect();

        return view('hotels.public', compact('hotels', 'cities', 'categories', 'totalCount', 'favoriteIds'));
    }
}
