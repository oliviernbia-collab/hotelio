<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $query = MenuItem::whereIn('hotel_id', $hotelIds);

        if ($request->filled('hotel_id'))  $query->where('hotel_id', $request->hotel_id);
        if ($request->filled('type'))      $query->where('item_type', $request->type);
        if ($request->filled('available')) $query->where('available', true);

        return response()->json($query->orderBy('category')->orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'name'        => 'required|string',
            'item_type'   => 'required|in:plat,boisson',
            'category'    => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'emoji'       => 'nullable|string',
            'description' => 'nullable|string',
            'available'   => 'boolean',
        ]);

        $this->checkAccess($request, $data['hotel_id']);

        $data['available'] = $data['available'] ?? true;

        return response()->json(MenuItem::create($data), 201);
    }

    public function update(Request $request, MenuItem $item)
    {
        $this->checkAccess($request, $item->hotel_id);

        $data = $request->validate([
            'name'        => 'sometimes|string',
            'item_type'   => 'sometimes|in:plat,boisson',
            'category'    => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'emoji'       => 'nullable|string',
            'description' => 'nullable|string',
            'available'   => 'boolean',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroy(Request $request, MenuItem $item)
    {
        $this->checkAccess($request, $item->hotel_id);
        $item->delete();

        return response()->json(['message' => 'Article supprimé.']);
    }

    private function checkAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
