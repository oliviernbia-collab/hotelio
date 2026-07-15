<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Review;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Hotel $hotel)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:200',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        Review::updateOrCreate(
            ['hotel_id' => $hotel->id, 'user_id' => Auth::id()],
            array_merge($data, ['status' => 'approved'])
        );

        // Recalcule la note moyenne de l'hôtel
        $avg = Review::where('hotel_id', $hotel->id)->avg('rating');
        $hotel->update(['rating' => round($avg, 1)]);

        // Notifier le propriétaire de l'hôtel
        $owner = $hotel->user;
        if ($owner && $owner->id !== Auth::id()) {
            try { $owner->notify(new NewReviewNotification($review)); } catch (\Throwable) {}
        }

        return back()->with('success', 'Votre avis a été publié avec succès.');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();

        if ($review->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $hotel = $review->hotel;
        $review->delete();

        $avg = Review::where('hotel_id', $hotel->id)->avg('rating');
        $hotel->update(['rating' => $avg ? round($avg, 1) : 4.5]);

        return back()->with('success', 'Avis supprimé.');
    }

    public function adminApprove(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'Avis approuvé.');
    }

    public function adminReject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'Avis rejeté.');
    }
}
