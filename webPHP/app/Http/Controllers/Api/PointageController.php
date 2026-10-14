<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Pointage;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Mirror de App\Http\Controllers\PointageController (web) — même logique métier (retard,
 * absences, calcul de ponctualité), réponses JSON au lieu de vues/redirections. Cf. ce
 * contrôleur web pour toute évolution de règle à garder en phase.
 */
class PointageController extends Controller
{
    private function computeLateMinutes(int $staffId, int $hotelId, Carbon $checkIn): int
    {
        $schedule = StaffSchedule::where('staff_id', $staffId)
            ->where('hotel_id', $hotelId)
            ->whereDate('date', $checkIn->toDateString())
            ->whereNotNull('start_time')
            ->first();

        if (! $schedule) return 0;

        $expected = Carbon::parse($checkIn->toDateString() . ' ' . $schedule->start_time);
        $late = $expected->diffInMinutes($checkIn, false);

        return max(0, (int) $late);
    }

    /** Pointage du jour + historique 30 jours — rôle `employe`. */
    public function me(Request $request)
    {
        $staff = $request->user()->staff;
        if (! $staff) {
            return response()->json(['message' => "Aucune fiche employé n'est associée à votre compte."], 404);
        }

        $today   = Pointage::where('staff_id', $staff->id)->whereDate('date', today())->first();
        $history = Pointage::where('staff_id', $staff->id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return response()->json(['staff' => $staff, 'today' => $today, 'history' => $history]);
    }

    public function checkIn(Request $request)
    {
        $staff = $request->user()->staff;
        if (! $staff) {
            return response()->json(['message' => "Aucune fiche employé n'est associée à votre compte."], 422);
        }

        $existing = Pointage::where('staff_id', $staff->id)->whereDate('date', today())->first();
        if ($existing && $existing->check_in) {
            return response()->json(['message' => 'Vous avez déjà pointé votre arrivée aujourd\'hui.'], 422);
        }

        $now = now();
        $lateMinutes = $this->computeLateMinutes($staff->id, $staff->hotel_id, $now);

        $pointage = Pointage::updateOrCreate(
            ['staff_id' => $staff->id, 'date' => today()->toDateString()],
            [
                'hotel_id'        => $staff->hotel_id,
                'check_in'        => $now,
                'check_in_source' => 'mobile',
                'late_minutes'    => $lateMinutes,
            ]
        );

        return response()->json($pointage);
    }

    public function checkOut(Request $request)
    {
        $staff = $request->user()->staff;
        if (! $staff) {
            return response()->json(['message' => "Aucune fiche employé n'est associée à votre compte."], 422);
        }

        $pointage = Pointage::where('staff_id', $staff->id)->whereDate('date', today())->first();
        if (! $pointage || ! $pointage->check_in) {
            return response()->json(['message' => "Vous devez d'abord pointer votre arrivée."], 422);
        }
        if ($pointage->check_out) {
            return response()->json(['message' => 'Vous avez déjà pointé votre départ aujourd\'hui.'], 422);
        }

        $pointage->update(['check_out' => now(), 'check_out_source' => 'mobile']);

        return response()->json($pointage);
    }

    /** Rapport propriétaire/admin — mirror de la branche non-employé de PointageController::index (web). */
    public function report(Request $request)
    {
        $user     = $request->user();
        $hotelIds = $user->getHotelIds();

        $from = $request->filled('from') ? Carbon::parse($request->from) : today()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)   : today();

        $staffQuery = Staff::whereIn('hotel_id', $hotelIds);
        if ($request->filled('hotel_id')) $staffQuery->where('hotel_id', $request->hotel_id);
        if ($request->filled('staff_id')) $staffQuery->where('id', $request->staff_id);
        $staffList = $staffQuery->orderBy('first_name')->get();

        $pointages = Pointage::whereIn('hotel_id', $hotelIds)
            ->whereIn('staff_id', $staffList->pluck('id'))
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with(['staff', 'hotel'])
            ->orderByDesc('date')
            ->get();

        $schedules = StaffSchedule::whereIn('hotel_id', $hotelIds)
            ->whereIn('staff_id', $staffList->pluck('id'))
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->where('shift', '!=', 'repos')
            ->where('date', '<', today())
            ->with(['staff', 'hotel'])
            ->get();
        $pointedKeys = $pointages->filter(fn($p) => $p->check_in)->map(fn($p) => $p->staff_id . '_' . $p->date->toDateString())->flip();
        $absences = $schedules->filter(fn($s) => !isset($pointedKeys[$s->staff_id . '_' . $s->date->toDateString()]));

        $totalShifts  = $schedules->count();
        $presentCount = $pointages->filter(fn($p) => $p->check_in && $p->late_minutes === 0)->count();
        $lateCount    = $pointages->filter(fn($p) => $p->check_in && $p->late_minutes > 0)->count();
        $absentCount  = $absences->count();
        $punctuality  = $totalShifts > 0 ? round(($presentCount / $totalShifts) * 100) : null;

        return response()->json([
            'hotels'     => Hotel::whereIn('id', $hotelIds)->orderBy('name')->get(),
            'staff_list' => $staffList,
            'pointages'  => $pointages->values(),
            'absences'   => $absences->values(),
            'stats'      => compact('totalShifts', 'presentCount', 'lateCount', 'absentCount', 'punctuality'),
            'from'       => $from->toDateString(),
            'to'         => $to->toDateString(),
        ]);
    }
}
