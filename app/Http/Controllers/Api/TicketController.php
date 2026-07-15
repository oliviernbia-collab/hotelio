<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $query = Ticket::whereIn('hotel_id', $hotelIds)
            ->with(['hotel:id,short_name', 'client:id,first_name,last_name']);

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);

        return response()->json($query->latest('opened_at')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'client_id'   => 'nullable|exists:clients,id',
            'title'       => 'required|string',
            'ticket_type' => 'required|string',
            'priority'    => 'required|in:faible,normal,urgent',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|string',
        ]);

        $this->checkAccess($request, $data['hotel_id']);

        $data['status']    = 'ouvert';
        $data['opened_at'] = now();

        return response()->json(Ticket::create($data), 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->checkAccess($request, $ticket->hotel_id);

        $data = $request->validate([
            'status'      => 'sometimes|in:ouvert,en_cours,résolu,fermé',
            'priority'    => 'sometimes|in:faible,normal,urgent',
            'assigned_to' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (in_array($data['status'] ?? '', ['résolu', 'fermé'])) {
            $data['closed_at'] = now();
        }

        $ticket->update($data);

        return response()->json($ticket);
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $this->checkAccess($request, $ticket->hotel_id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket supprimé.']);
    }

    private function checkAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
