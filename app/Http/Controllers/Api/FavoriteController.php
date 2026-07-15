<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $hotels = $request->user()->favoriteHotels()
            ->withCount('rooms')
            ->get(['hotels.id', 'name', 'short_name', 'city', 'stars', 'price', 'rating', 'image', 'category']);

        return response()->json($hotels);
    }

    public function toggle(Request $request, Hotel $hotel)
    {
        $result = $request->user()->favoriteHotels()->toggle($hotel->id);

        $added = ! empty($result['attached']);

        return response()->json([
            'favorited' => $added,
            'message'   => $added ? 'Ajouté aux favoris.' : 'Retiré des favoris.',
        ]);
    }
}
