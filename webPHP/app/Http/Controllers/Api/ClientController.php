<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $hotelIds = $user->getHotelIds();

        $query = Client::whereIn(
            'id',
            \App\Models\Reservation::whereIn('hotel_id', $hotelIds)->pluck('client_id')
        );

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('first_name', 'like', "%$s%")
                ->orWhere('last_name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%")
            );
        }

        return response()->json($query->latest()->paginate(20));
    }

    /** Même rattachement que index() : admin, ou client lié à une réservation dans l'un de mes hôtels. */
    private function authorizeClient(Request $request, Client $client): void
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return;
        }

        $linked = \App\Models\Reservation::where('client_id', $client->id)
            ->whereIn('hotel_id', $user->getHotelIds())
            ->exists();

        if (! $linked) {
            abort(403);
        }
    }

    public function show(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $client->load(['reservations.hotel:id,short_name', 'reservations.room:id,number,type']);

        return response()->json($client);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'  => 'required|string',
            'last_name'   => 'required|string',
            'email'       => 'nullable|email|unique:clients,email',
            'phone'       => 'nullable|string',
            'city'        => 'nullable|string',
            'quartier'    => 'nullable|string',
            'nationality' => 'nullable|string',
            'note'        => 'nullable|string',
        ]);

        $data['code']   = 'CLI-'.strtoupper(\Illuminate\Support\Str::random(6));
        $data['status'] = 'actif';

        return response()->json(Client::create($data), 201);
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $data = $request->validate([
            'first_name'  => 'sometimes|string',
            'last_name'   => 'sometimes|string',
            'email'       => 'nullable|email|unique:clients,email,'.$client->id,
            'phone'       => 'nullable|string',
            'city'        => 'nullable|string',
            'nationality' => 'nullable|string',
            'note'        => 'nullable|string',
            'status'      => 'sometimes|in:actif,inactif',
        ]);

        $client->update($data);

        return response()->json($client);
    }

    public function destroy(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);
        $client->delete();

        return response()->json(['message' => 'Client supprimé.']);
    }

    /** Mirror de DashboardController::addClientPoints (web). */
    public function addPoints(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $data = $request->validate(['points' => 'required|integer']);

        $client->update([
            'points_fidelite' => max(0, $client->points_fidelite + $data['points']),
        ]);

        return response()->json($client);
    }
}
