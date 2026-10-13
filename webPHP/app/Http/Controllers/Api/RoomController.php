<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $hotelIds = $user->getHotelIds();

        $query = Room::whereIn('hotel_id', $hotelIds)->with('hotel:id,short_name');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('type'))     $query->where('type', $request->type);
        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);

        return response()->json($query->orderBy('floor')->orderBy('number')->get());
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'hotel_id'      => 'required|exists:hotels,id',
            'number'        => 'required|string',
            'floor'         => 'required|integer|min:0',
            'type'          => 'required|string',
            'price'         => 'required|numeric|min:0',
            'capacity'      => 'required|integer|min:1',
            'superficie_m2' => 'nullable|numeric',
            'description'   => 'nullable|string',
        ]);

        if (! $user->getHotelIds()->contains($data['hotel_id'])) {
            abort(403);
        }

        $data['status'] = 'disponible';
        $room = Room::create($data);

        return response()->json($room, 201);
    }

    public function updateStatus(Request $request, Room $room)
    {
        $this->gate($request, $room);

        $request->validate([
            'status' => 'required|in:disponible,réservée,occupée,en_nettoyage,hors_service',
        ]);

        $room->update(['status' => $request->status]);

        return response()->json($room);
    }

    public function markCleaned(Request $request, Room $room)
    {
        $this->gate($request, $room);
        $room->update(['status' => 'disponible']);

        return response()->json($room);
    }

    public function destroy(Request $request, Room $room)
    {
        $this->gate($request, $room);
        $room->delete();

        return response()->json(['message' => 'Chambre supprimée.']);
    }

    private function gate(Request $request, Room $room): void
    {
        if (! $request->user()->getHotelIds()->contains($room->hotel_id)) {
            abort(403);
        }
    }
}
