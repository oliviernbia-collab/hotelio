<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'title'   => 'nullable|string|max:255',
            'comment' => 'nullable|string',
        ]);

        $review = Review::updateOrCreate(
            ['hotel_id' => $hotel->id, 'user_id' => $request->user()->id],
            array_merge($data, ['status' => 'pending'])
        );

        return response()->json($review, 201);
    }

    public function destroy(Request $request, Review $review)
    {
        $user = $request->user();
        if ($user->role !== 'admin' && $review->user_id !== $user->id) {
            abort(403);
        }
        $review->delete();

        return response()->json(['message' => 'Avis supprimé.']);
    }

    public function approve(Request $request, Review $review)
    {
        $review->update(['status' => 'approved']);

        $avg = Review::where('hotel_id', $review->hotel_id)->where('status', 'approved')->avg('rating');
        $review->hotel->update(['rating' => round($avg, 1)]);

        return response()->json($review);
    }

    public function reject(Request $request, Review $review)
    {
        $review->update(['status' => 'rejected']);

        return response()->json($review);
    }
}
