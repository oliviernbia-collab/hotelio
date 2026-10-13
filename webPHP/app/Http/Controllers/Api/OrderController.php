<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $query = Order::whereIn('hotel_id', $hotelIds)
            ->with(['client:id,first_name,last_name', 'menuItem:id,name,emoji,item_type']);

        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);
        if ($request->filled('status'))   $query->where('status', $request->status);

        return response()->json($query->latest('ordered_at')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'client_id'    => 'nullable|exists:clients,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string',
        ]);

        $this->checkAccess($request, $data['hotel_id']);

        $item          = \App\Models\MenuItem::findOrFail($data['menu_item_id']);
        $data['total'] = $item->price * $data['quantity'];
        $data['status']    = 'en_attente';
        $data['ordered_at'] = now();

        return response()->json(Order::create($data), 201);
    }

    public function update(Request $request, Order $order)
    {
        $this->checkAccess($request, $order->hotel_id);

        $data = $request->validate([
            'status'       => 'sometimes|in:en_attente,en_preparation,servi,annulé',
            'delivered_at' => 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        if (($data['status'] ?? null) === 'servi') {
            $data['delivered_at'] = $data['delivered_at'] ?? now();
        }

        $order->update($data);

        return response()->json($order);
    }

    public function destroy(Request $request, Order $order)
    {
        $this->checkAccess($request, $order->hotel_id);
        $order->delete();

        return response()->json(['message' => 'Commande supprimée.']);
    }

    private function checkAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
