<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function premiere()
    {
        $totalHotels    = Hotel::count();
        $featuredHotels = Hotel::orderByDesc('rating')->take(8)->get();
        $cities         = Hotel::distinct()->orderBy('city')->pluck('city');

        // Hôtels par section — strictement filtrés par ville (pas de repli sur une
        // autre ville : un hôtel d'Abidjan ne doit jamais apparaître dans la section
        // San-Pédro juste parce que cette dernière est vide).
        $allByRating = Hotel::orderByDesc('rating')->get();

        $hotelsAbidjan = $allByRating->filter(fn($h) => stripos($h->city, 'Abidjan') !== false)->take(4);

        $hotelsYamoussoukro = $allByRating->filter(fn($h) => stripos($h->city, 'Yamoussoukro') !== false)->take(4);

        $hotelsSanPedro = $allByRating->filter(
            fn($h) => stripos($h->city, 'San') !== false && (stripos($h->city, 'Pédro') !== false || stripos($h->city, 'Pedro') !== false)
        )->take(4);

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

    public function hotelDetails(Hotel $hotel)
    {
        $rooms = Room::where('hotel_id', $hotel->id)->get();

        $reviews = Review::where('hotel_id', $hotel->id)
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->get();

        $userReview = (auth()->check() && auth()->user()->role === 'client')
            ? Review::where('hotel_id', $hotel->id)->where('user_id', auth()->id())->first()
            : null;

        $bookedRooms = Reservation::whereIn('room_id', $rooms->pluck('id'))
            ->whereNotIn('status', ['annulée', 'cancelled', 'refusée'])
            ->whereDate('checkout', '>=', now())
            ->select('room_id', 'checkin', 'checkout')
            ->get();

        return view('hotels.details', compact('hotel', 'rooms', 'reviews', 'userReview', 'bookedRooms'));
    }

    public function pricing()
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('pricing.index', compact('plans'));
    }
}
