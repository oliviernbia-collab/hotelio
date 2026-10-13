<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PointageController extends Controller
{
    /**
     * Calcule le retard (en minutes) d'un pointage par rapport à l'horaire prévu.
     */
    private function computeLateMinutes(int $staffId, int $hotelId, Carbon $checkIn): int
    {
        $schedule = StaffSchedule::where('staff_id', $staffId)
            ->where('hotel_id', $hotelId)
            ->whereDate('date', $checkIn->toDateString())
            ->whereNotNull('start_time')
            ->first();

        if (!$schedule) return 0;

        $expected = Carbon::parse($checkIn->toDateString() . ' ' . $schedule->start_time);
        $late = $expected->diffInMinutes($checkIn, false);

        return max(0, (int) $late);
    }

    /** Page principale : widget de pointage (employé) ou rapport (propriétaire/admin) */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'employe') {
            $staff = $user->staff;
            if (!$staff) {
                return redirect()->route('prestataire.index')
                    ->with('error', "Aucune fiche employé n'est associée à votre compte.");
            }

            $today = Pointage::where('staff_id', $staff->id)->whereDate('date', today())->first();
            $history = Pointage::where('staff_id', $staff->id)
                ->orderByDesc('date')
                ->limit(30)
                ->get();

            return view('pointage.employee', compact('staff', 'today', 'history'));
        }

        // ── Propriétaire / admin : rapport ──────────────────────────────────────
        $hotelIds = $user->getHotelIds();

        $from = $request->filled('from') ? Carbon::parse($request->from) : today()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)   : today();

        $hotels = \App\Models\Hotel::whereIn('id', $hotelIds)->orderBy('name')->get();
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

        // Absences : jours planifiés (staff_schedules) sans pointage correspondant, dans la période.
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

        $stats = compact('totalShifts', 'presentCount', 'lateCount', 'absentCount', 'punctuality');

        return view('pointage.report', compact('pointages', 'absences', 'hotels', 'staffList', 'stats', 'from', 'to'));
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        if (!$staff) {
            return back()->with('error', "Aucune fiche employé n'est associée à votre compte.");
        }

        $existing = Pointage::where('staff_id', $staff->id)->whereDate('date', today())->first();
        if ($existing && $existing->check_in) {
            return back()->with('error', 'Vous avez déjà pointé votre arrivée aujourd\'hui.');
        }

        $now = now();
        $lateMinutes = $this->computeLateMinutes($staff->id, $staff->hotel_id, $now);

        Pointage::updateOrCreate(
            ['staff_id' => $staff->id, 'date' => today()->toDateString()],
            [
                'hotel_id'         => $staff->hotel_id,
                'check_in'         => $now,
                'check_in_source'  => 'web',
                'late_minutes'     => $lateMinutes,
            ]
        );

        $msg = $lateMinutes > 0
            ? "Arrivée enregistrée avec {$lateMinutes} min de retard."
            : 'Arrivée enregistrée. Bonne journée !';

        return back()->with('success', $msg);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        if (!$staff) {
            return back()->with('error', "Aucune fiche employé n'est associée à votre compte.");
        }

        $pointage = Pointage::where('staff_id', $staff->id)->whereDate('date', today())->first();
        if (!$pointage || !$pointage->check_in) {
            return back()->with('error', "Vous devez d'abord pointer votre arrivée.");
        }
        if ($pointage->check_out) {
            return back()->with('error', 'Vous avez déjà pointé votre départ aujourd\'hui.');
        }

        $pointage->update(['check_out' => now(), 'check_out_source' => 'web']);

        return back()->with('success', 'Départ enregistré. À bientôt !');
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $hotelIds = $user->getHotelIds();

        $from = $request->filled('from') ? Carbon::parse($request->from) : today()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)   : today();

        $query = Pointage::whereIn('hotel_id', $hotelIds)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->with(['staff', 'hotel']);
        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);
        if ($request->filled('staff_id')) $query->where('staff_id', $request->staff_id);

        $pointages = $query->orderBy('date')->get();

        $filename = 'pointages_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($pointages) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM pour Excel
            fputcsv($out, ['Employé', 'Hôtel', 'Date', 'Arrivée', 'Départ', 'Retard (min)', 'Heures travaillées', 'Statut']);
            foreach ($pointages as $p) {
                $worked = $p->worked_minutes !== null ? sprintf('%02d:%02d', intdiv($p->worked_minutes, 60), $p->worked_minutes % 60) : '';
                fputcsv($out, [
                    $p->staff->first_name . ' ' . $p->staff->last_name,
                    $p->hotel->name ?? '',
                    $p->date->format('d/m/Y'),
                    $p->check_in?->format('H:i') ?? '',
                    $p->check_out?->format('H:i') ?? '',
                    $p->late_minutes,
                    $worked,
                    ucfirst($p->status),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
