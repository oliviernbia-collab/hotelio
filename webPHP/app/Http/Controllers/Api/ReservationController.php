<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\Client;
use App\Models\Reservation;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Reservation::with(['hotel:id,short_name', 'client:id,first_name,last_name', 'room:id,number,type']);

        if ($user->role === 'client') {
            $client = Client::where('user_id', $user->id)->first();
            $query->where('client_id', $client?->id);
        } else {
            $query->whereIn('hotel_id', $user->getHotelIds());
        }

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);
        if ($request->filled('date_from')) $query->whereDate('checkin', '>=', $request->date_from);
        if ($request->filled('date_to'))  $query->whereDate('checkout', '<=', $request->date_to);

        return response()->json($query->latest()->paginate(20));
    }

    public function show(Request $request, Reservation $reservation)
    {
        $this->gate($request, $reservation);

        $reservation->load(['hotel', 'client', 'room']);

        return response()->json($reservation);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'       => 'required|exists:hotels,id',
            'room_id'        => 'required|exists:rooms,id',
            'client_id'      => 'nullable|exists:clients,id',
            'checkin'        => 'required|date|after_or_equal:today',
            'checkout'       => 'required|date|after:checkin',
            'channel'        => 'nullable|string',
            'nationality'    => 'nullable|string',
            'notes'          => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $checkin  = Carbon::parse($data['checkin']);
        $checkout = Carbon::parse($data['checkout']);
        $nights   = $checkin->diffInDays($checkout);

        $room  = \App\Models\Room::findOrFail($data['room_id']);
        $total = $room->price * $nights;

        // Si client non fourni, créer ou retrouver depuis l'utilisateur connecté
        if (empty($data['client_id'])) {
            $user   = $request->user();
            $client = Client::firstOrCreate(
                ['user_id' => $user->id],
                ['first_name' => explode(' ', $user->name)[0], 'last_name' => explode(' ', $user->name)[1] ?? '', 'email' => $user->email]
            );
            $data['client_id'] = $client->id;
        }

        $reservation = Reservation::create(array_merge($data, [
            'code'   => 'RES-'.strtoupper(Str::random(8)),
            'nights' => $nights,
            'price'  => $room->price,
            'total'  => $total,
            'status' => 'en_attente',
            'paid'   => false,
        ]));

        $room->update(['status' => 'réservée']);

        return response()->json($reservation->load(['hotel:id,name', 'room:id,number,type']), 201);
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $this->gate($request, $reservation);

        $request->validate([
            'status' => 'required|in:en_attente,confirmée,annulée,terminée',
        ]);

        if ($request->status === 'annulée' && $reservation->status !== 'annulée' && $reservation->paid) {
            RefundService::reverseCommissionAndStats($reservation);
        }

        $reservation->update(['status' => $request->status]);

        if ($request->status === 'annulée') {
            $reservation->room?->update(['status' => 'disponible']);
        }

        return response()->json($reservation);
    }

    /** Annulation par le client avec remboursement réel (voir RefundService). */
    public function cancel(Request $request, Reservation $reservation)
    {
        $this->gate($request, $reservation);

        $result = RefundService::cancelReservation($reservation);

        if (! $result['ok']) {
            return response()->json(['error' => $result['error'], 'message' => $result['message']], 422);
        }

        $reservation->room?->update(['status' => 'disponible']);

        return response()->json([
            'reservation' => $reservation->fresh(),
            'refund' => [
                'fee' => $result['fee'] ?? null,
                'amount' => $result['refundAmount'] ?? null,
                'status' => $result['refundStatus'] ?? null,
            ],
        ]);
    }

    public function checkIn(Request $request, Reservation $reservation)
    {
        $this->gate($request, $reservation);

        $reservation->update([
            'status'     => 'confirmée',
            'checkin_at' => now(),
        ]);
        $reservation->room?->update(['status' => 'occupée']);

        // Le séjour commence : plus de risque d'annulation, on peut reverser le prestataire.
        if ($reservation->paid) {
            app(DashboardController::class)->triggerOwnerPayout($reservation->fresh());
        }

        return response()->json($reservation);
    }

    public function checkOut(Request $request, Reservation $reservation)
    {
        $this->gate($request, $reservation);

        $reservation->update([
            'status'      => 'terminée',
            'checkout_at' => now(),
        ]);
        $reservation->room?->update(['status' => 'en_nettoyage']);

        // Mettre à jour les stats client
        $client = $reservation->client;
        if ($client) {
            $client->increment('total_stays');
            $client->increment('total_spend', $reservation->total);
            $client->update(['last_visit' => now()->toDateString()]);
        }

        return response()->json($reservation);
    }

    public function processPayment(Request $request, Reservation $reservation)
    {
        $this->gate($request, $reservation);

        $request->validate([
            'payment_method'   => 'required|string',
            'transaction_id'   => 'nullable|string',
        ]);

        $reservation->update([
            'paid'             => true,
            'payment_method'   => $request->payment_method,
            'status'           => 'confirmée',
        ]);

        return response()->json($reservation);
    }

    private function gate(Request $request, Reservation $reservation): void
    {
        $user = $request->user();

        if ($user->role === 'admin') return;

        if ($user->role === 'client') {
            $client = Client::where('user_id', $user->id)->first();
            if ($reservation->client_id !== $client?->id) abort(403);
            return;
        }

        if (! $user->getHotelIds()->contains($reservation->hotel_id)) {
            abort(403);
        }
    }
}
