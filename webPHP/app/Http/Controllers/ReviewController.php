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

        // Comme côté mobile : l'avis part en modération, il n'impacte la note
        // publique qu'une fois approuvé par un admin.
        $review = Review::updateOrCreate(
            ['hotel_id' => $hotel->id, 'user_id' => Auth::id()],
            array_merge($data, ['status' => 'pending'])
        );

        // Notifier le propriétaire de l'hôtel
        $owner = $hotel->user;
        if ($owner && $owner->id !== Auth::id()) {
            try { $owner->notify(new NewReviewNotification($review)); } catch (\Throwable) {}
        }

        return back()->with('success', 'Votre avis a été soumis et sera publié après validation.');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();

        if ($review->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $hotel = $review->hotel;
        $review->delete();
        $this->recalcHotelRating($hotel);

        return back()->with('success', 'Avis supprimé.');
    }

    /** Liste des avis pour modération (admin). */
    public function adminIndex(Request $request)
    {
        $status  = $request->query('status', 'pending');
        $reviews = Review::with(['hotel', 'user'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        $counts = [
            'pending'  => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'status', 'counts'));
    }

    public function adminApprove(Review $review)
    {
        $review->update(['status' => 'approved']);
        $this->recalcHotelRating($review->hotel);
        return back()->with('success', 'Avis approuvé.');
    }

    public function adminReject(Review $review)
    {
        $wasApproved = $review->status === 'approved';
        $review->update(['status' => 'rejected']);
        if ($wasApproved) {
            $this->recalcHotelRating($review->hotel);
        }
        return back()->with('success', 'Avis rejeté.');
    }

    /** Recalcule la note moyenne de l'hôtel à partir des avis approuvés uniquement. */
    private function recalcHotelRating(Hotel $hotel): void
    {
        $avg = Review::where('hotel_id', $hotel->id)->where('status', 'approved')->avg('rating');
        $hotel->update(['rating' => $avg ? round($avg, 1) : 4.5]);
    }
}
