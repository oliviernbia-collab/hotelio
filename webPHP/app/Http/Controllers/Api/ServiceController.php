<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\Hotel;
use App\Models\HotelService;
use Illuminate\Http\Request;

/**
 * Mirror de DashboardController::services/storeService/updateService/toggleService/
 * destroyService (web) — mêmes règles, réponses JSON au lieu de vues/redirections.
 */
class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $services = HotelService::with('hotel:id,short_name')
            ->when($hotelIds->isNotEmpty(), fn ($q) => $q->whereIn('hotel_id', $hotelIds))
            ->orderBy('category')->orderBy('name')
            ->get();

        return response()->json([
            'data'       => $services,
            'categories' => DashboardController::SERVICE_CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'    => 'required|exists:hotels,id',
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price'       => 'nullable|numeric|min:0',
            'emoji'       => 'nullable|string|max:20',
        ]);

        $this->checkAccess($request, $data['hotel_id']);
        $data['price'] = $data['price'] ?? 0;
        $data['emoji'] = $data['emoji'] ?: '✨';

        return response()->json(HotelService::create($data), 201);
    }

    public function update(Request $request, HotelService $service)
    {
        $this->checkAccess($request, $service->hotel_id);

        $data = $request->validate([
            'hotel_id'    => 'sometimes|exists:hotels,id',
            'name'        => 'sometimes|string|max:255',
            'category'    => 'sometimes|string|max:100',
            'description' => 'nullable|string|max:500',
            'price'       => 'nullable|numeric|min:0',
            'emoji'       => 'nullable|string|max:20',
        ]);

        if (isset($data['hotel_id'])) {
            $this->checkAccess($request, $data['hotel_id']);
        }
        if (array_key_exists('price', $data)) {
            $data['price'] = $data['price'] ?? 0;
        }
        if (array_key_exists('emoji', $data)) {
            $data['emoji'] = $data['emoji'] ?: '✨';
        }

        $service->update($data);

        return response()->json($service);
    }

    public function toggle(Request $request, HotelService $service)
    {
        $this->checkAccess($request, $service->hotel_id);
        $service->update(['available' => ! $service->available]);

        return response()->json($service);
    }

    public function destroy(Request $request, HotelService $service)
    {
        $this->checkAccess($request, $service->hotel_id);
        $service->delete();

        return response()->json(['message' => 'Service supprimé.']);
    }

    private function checkAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
