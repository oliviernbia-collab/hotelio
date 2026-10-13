<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin'       => $this->adminDashboard(),
            'prestataire' => $this->prestataireDashboard($user),
            'employe'     => $this->employeDashboard($user),
            default       => $this->clientDashboard($user),
        };
    }

    private function adminDashboard(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'role'  => 'admin',
            'stats' => [
                'total_users'        => User::count(),
                'total_hotels'       => Hotel::count(),
                'total_reservations' => Reservation::count(),
                'total_revenue'      => Reservation::where('paid', true)->sum('total'),
                'total_clients'      => Client::count(),
                'pending_reviews'    => Review::where('status', 'pending')->count(),
            ],
            'reservation_status' => [
                'confirmée'  => Reservation::where('status', 'confirmée')->count(),
                'en_attente' => Reservation::where('status', 'en_attente')->count(),
                'annulée'    => Reservation::where('status', 'annulée')->count(),
            ],
            'recent_reservations' => Reservation::with(['hotel:id,short_name', 'client:id,first_name,last_name'])
                ->latest()->limit(8)->get(),
            'top_hotels' => Hotel::orderByDesc('revenue')->take(5)
                ->get(['id', 'name', 'short_name', 'city', 'stars', 'revenue', 'occupancy']),
            'recent_users' => User::latest()->limit(6)
                ->get(['id', 'name', 'email', 'role', 'status', 'created_at']),
        ]);
    }

    private function prestataireDashboard(User $user): \Illuminate\Http\JsonResponse
    {
        $hotelIds = $user->getHotelIds();

        return response()->json([
            'role'  => 'prestataire',
            'stats' => [
                'my_hotels'           => Hotel::whereIn('id', $hotelIds)->count(),
                'total_rooms'         => Room::whereIn('hotel_id', $hotelIds)->count(),
                'available_rooms'     => Room::whereIn('hotel_id', $hotelIds)->where('status', 'disponible')->count(),
                'active_reservations' => Reservation::whereIn('hotel_id', $hotelIds)->where('status', 'confirmée')->count(),
                'today_checkins'      => Reservation::whereIn('hotel_id', $hotelIds)->whereDate('checkin', today())->count(),
                'today_checkouts'     => Reservation::whereIn('hotel_id', $hotelIds)->whereDate('checkout', today())->count(),
                'total_revenue'       => Reservation::whereIn('hotel_id', $hotelIds)->where('paid', true)->sum('total'),
                'total_staff'         => Staff::whereIn('hotel_id', $hotelIds)->count(),
            ],
            'my_hotels' => Hotel::whereIn('id', $hotelIds)
                ->withCount(['rooms', 'reservations'])
                ->get(),
            'recent_reservations' => Reservation::whereIn('hotel_id', $hotelIds)
                ->with(['client:id,first_name,last_name', 'hotel:id,short_name', 'room:id,number,type'])
                ->latest()->limit(10)->get(),
        ]);
    }

    private function employeDashboard(User $user): \Illuminate\Http\JsonResponse
    {
        $hotel    = $user->getEmployeeHotel();
        $hotelId  = $hotel?->id;

        return response()->json([
            'role'  => 'employe',
            'hotel' => $hotel,
            'stats' => $hotelId ? [
                'total_rooms'     => Room::where('hotel_id', $hotelId)->count(),
                'available_rooms' => Room::where('hotel_id', $hotelId)->where('status', 'disponible')->count(),
                'occupied_rooms'  => Room::where('hotel_id', $hotelId)->where('status', 'occupée')->count(),
                'today_checkins'  => Reservation::where('hotel_id', $hotelId)->whereDate('checkin', today())->count(),
                'today_checkouts' => Reservation::where('hotel_id', $hotelId)->whereDate('checkout', today())->count(),
            ] : [],
            'today_reservations' => $hotelId
                ? Reservation::where('hotel_id', $hotelId)
                    ->with(['client:id,first_name,last_name', 'room:id,number'])
                    ->whereDate('checkin', today())
                    ->orWhereDate('checkout', today())
                    ->limit(10)->get()
                : [],
        ]);
    }

    private function clientDashboard(User $user): \Illuminate\Http\JsonResponse
    {
        $client = \App\Models\Client::where('user_id', $user->id)->first();

        return response()->json([
            'role'   => 'client',
            'client' => $client,
            'stats'  => [
                'total_stays'   => $client?->total_stays ?? 0,
                'total_spend'   => $client?->total_spend ?? 0,
                'loyalty_points' => $client?->points_fidelite ?? 0,
                'reservations'  => $client ? Reservation::where('client_id', $client->id)->count() : 0,
            ],
            'recent_reservations' => $client
                ? Reservation::where('client_id', $client->id)
                    ->with(['hotel:id,name,short_name,city', 'room:id,number,type'])
                    ->latest()->limit(5)->get()
                : [],
            'featured_hotels' => Hotel::where('status', 'actif')
                ->orderByDesc('rating')->limit(6)
                ->get(['id', 'name', 'short_name', 'city', 'stars', 'price', 'rating', 'image', 'category']),
        ]);
    }
}
