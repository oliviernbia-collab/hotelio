<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favoriteHotels()->latest('favorites.created_at')->get();

        return view('client.favorites', compact('favorites'));
    }

    public function toggle(Hotel $hotel)
    {
        $user = Auth::user();

        if ($user->favoriteHotels()->where('hotel_id', $hotel->id)->exists()) {
            $user->favoriteHotels()->detach($hotel->id);
            $isFavorite = false;
        } else {
            $user->favoriteHotels()->attach($hotel->id, ['created_at' => now()]);
            $isFavorite = true;
        }

        return response()->json([
            'favorite' => $isFavorite,
            'count'    => $user->favoriteHotels()->count(),
        ]);
    }
}
