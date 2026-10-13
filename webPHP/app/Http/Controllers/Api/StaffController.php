<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $query = Staff::whereIn('hotel_id', $hotelIds)->with('hotel:id,short_name');

        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);

        return response()->json($query->orderBy('last_name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'  => 'required|exists:hotels,id',
            'first_name'=> 'required|string',
            'last_name' => 'required|string',
            'role'      => 'required|string',
            'phone'     => 'nullable|string',
            'email'     => 'nullable|email',
            'salary'    => 'nullable|numeric',
            'hired_at'  => 'nullable|date',
        ]);

        $this->checkHotelAccess($request, $data['hotel_id']);

        $data['status'] = 'actif';

        return response()->json(Staff::create($data), 201);
    }

    public function update(Request $request, Staff $staff)
    {
        $this->checkHotelAccess($request, $staff->hotel_id);

        $data = $request->validate([
            'first_name' => 'sometimes|string',
            'last_name'  => 'sometimes|string',
            'role'       => 'sometimes|string',
            'phone'      => 'nullable|string',
            'email'      => 'nullable|email',
            'salary'     => 'nullable|numeric',
            'status'     => 'sometimes|in:actif,inactif',
        ]);

        $staff->update($data);

        return response()->json($staff);
    }

    public function destroy(Request $request, Staff $staff)
    {
        $this->checkHotelAccess($request, $staff->hotel_id);
        $staff->delete();

        return response()->json(['message' => 'Membre du personnel supprimé.']);
    }

    public function schedules(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $query = StaffSchedule::with('staff:id,first_name,last_name,role')
            ->whereHas('staff', fn($q) => $q->whereIn('hotel_id', $hotelIds));

        if ($request->filled('date'))    $query->where('date', $request->date);
        if ($request->filled('week')) {
            $start = now()->setISODate(now()->year, $request->week)->startOfWeek();
            $query->whereBetween('date', [$start, $start->copy()->endOfWeek()]);
        }

        return response()->json($query->orderBy('date')->get());
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'staff_id'   => 'required|exists:staff,id',
            'hotel_id'   => 'required|exists:hotels,id',
            'date'       => 'required|date',
            'shift'      => 'required|in:matin,apres_midi,nuit,repos',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i',
            'poste'      => 'nullable|string',
            'notes'      => 'nullable|string',
        ]);

        $this->checkHotelAccess($request, $data['hotel_id']);

        return response()->json(StaffSchedule::create($data), 201);
    }

    private function checkHotelAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
