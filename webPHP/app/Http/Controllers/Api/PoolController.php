<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        return response()->json(
            Pool::whereIn('hotel_id', $hotelIds)->with('hotel:id,short_name')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'name'        => 'required|string',
            'status'      => 'required|string',
            'area'        => 'nullable|numeric',
            'depth'       => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $this->checkAccess($request, $data['hotel_id']);

        return response()->json(Pool::create($data), 201);
    }

    public function update(Request $request, Pool $pool)
    {
        $this->checkAccess($request, $pool->hotel_id);

        $data = $request->validate([
            'name'        => 'sometimes|string',
            'status'      => 'sometimes|string',
            'area'        => 'nullable|numeric',
            'depth'       => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $pool->update($data);

        return response()->json($pool);
    }

    private function checkAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
