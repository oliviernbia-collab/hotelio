<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Hotel::query()->withCount(['rooms', 'reservations'])
            ->with(['reviews' => fn($q) => $q->where('status', 'approved')->latest()->limit(3)]);

        // Filtres de recherche
        if ($request->filled('city'))     $query->where('city', 'like', '%'.$request->city.'%');
        if ($request->filled('commune'))  $query->where('commune', 'like', '%'.$request->commune.'%');
        if ($request->filled('quartier')) $query->where('quartier', 'like', '%'.$request->quartier.'%');
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('stars'))    $query->where('stars', $request->stars);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('search')) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('city', 'like', '%'.$request->search.'%')
                ->orWhere('commune', 'like', '%'.$request->search.'%')
                ->orWhere('quartier', 'like', '%'.$request->search.'%')
            );
        }

        // Restriction par rôle
        if ($user && in_array($user->role, ['prestataire', 'employe'])) {
            $query->whereIn('id', $user->getHotelIds());
        }

        $hotels = $query->orderByDesc('rating')->paginate(15);

        return response()->json($hotels);
    }

    public function show(Hotel $hotel)
    {
        $hotel->load([
            'rooms',
            'reviews' => fn($q) => $q->where('status', 'approved')->with('user:id,name')->latest(),
            'staff:id,hotel_id,first_name,last_name,role',
        ]);

        return response()->json($hotel);
    }

    public function store(Request $request)
    {
        $this->authorizePrestataire($request);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'short_name'  => 'required|string|max:50',
            'city'        => 'required|string',
            'commune'     => 'nullable|string',
            'quartier'    => 'nullable|string',
            'stars'       => 'required|integer|between:1,5',
            'category'    => 'required|string',
            'price'       => 'required|numeric|min:0',
            'phone'       => 'nullable|string',
            'email'       => 'nullable|email',
            'description' => 'nullable|string',
            'services'    => 'nullable|array',
            'manager'     => 'nullable|string',
            'opened_at'   => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['status']  = 'actif';

        $hotel = Hotel::create($data);

        return response()->json($hotel, 201);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $this->authorizeHotelAccess($request, $hotel);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'short_name'  => 'sometimes|string|max:50',
            'city'        => 'sometimes|string',
            'commune'     => 'nullable|string',
            'quartier'    => 'nullable|string',
            'stars'       => 'sometimes|integer|between:1,5',
            'category'    => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'phone'       => 'nullable|string',
            'email'       => 'nullable|email',
            'description' => 'nullable|string',
            'services'    => 'nullable|array',
            'status'      => 'sometimes|in:actif,inactif',
        ]);

        $hotel->update($data);

        return response()->json($hotel);
    }

    public function destroy(Request $request, Hotel $hotel)
    {
        $this->authorizeHotelAccess($request, $hotel);
        $hotel->delete();

        return response()->json(['message' => 'Hôtel supprimé.']);
    }

    private function authorizePrestataire(Request $request): void
    {
        if (! in_array($request->user()->role, ['admin', 'prestataire'])) {
            abort(403, 'Action non autorisée.');
        }
    }

    private function authorizeHotelAccess(Request $request, Hotel $hotel): void
    {
        $user = $request->user();
        if ($user->role === 'admin') return;
        if (! $user->getHotelIds()->contains($hotel->id)) {
            abort(403, 'Accès refusé à cet hôtel.');
        }
    }
}
