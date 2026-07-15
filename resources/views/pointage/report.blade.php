@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card pointage-header" style="margin-bottom:1.5rem;">
    <div class="pointage-header-inner">
        <div style="display:flex;align-items:center;gap:1rem;min-width:0;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);flex-shrink:0;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div style="min-width:0;">
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Pointage du personnel') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">
                    {{ __('Suivez les arrivées, départs et retards de vos employés.') }}
                </p>
            </div>
        </div>
        <a href="{{ route('pointage.export', request()->query()) }}" class="pointage-export-btn"
           style="padding:.6rem 1.1rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-secondary);text-decoration:none;font-size:.85rem;display:flex;align-items:center;justify-content:center;gap:.5rem;">
            <i class="fa-solid fa-file-csv"></i> {{ __('Exporter CSV') }}
        </a>
    </div>
</div>

{{-- STATS --}}
<div class="pointage-stats-grid" style="margin-bottom:1.5rem;">
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:var(--ci-green);">
            <i class="fa-solid fa-gauge-high"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $stats['punctuality'] !== null ? $stats['punctuality'] . '%' : '—' }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Ponctualité') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#4ade80;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $stats['presentCount'] }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Présences') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#fbbf24;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $stats['lateCount'] }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Retards') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#fca5a5;">
            <i class="fa-solid fa-user-xmark"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $stats['absentCount'] }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Absences') }}</div>
    </div>
</div>

{{-- FILTRES --}}
<div class="card" style="margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('pointage.index') }}" class="pointage-filters">
        <div class="pointage-filter-field">
            <label style="display:block;font-size:.75rem;color:var(--text-secondary);font-weight:600;margin-bottom:.35rem;">{{ __('Du') }}</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}"
                style="width:100%;box-sizing:border-box;padding:.55rem .8rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-primary);font-size:.85rem;">
        </div>
        <div class="pointage-filter-field">
            <label style="display:block;font-size:.75rem;color:var(--text-secondary);font-weight:600;margin-bottom:.35rem;">{{ __('Au') }}</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}"
                style="width:100%;box-sizing:border-box;padding:.55rem .8rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-primary);font-size:.85rem;">
        </div>
        <div class="pointage-filter-field">
            <label style="display:block;font-size:.75rem;color:var(--text-secondary);font-weight:600;margin-bottom:.35rem;">{{ __('Hôtel') }}</label>
            <select name="hotel_id" style="width:100%;box-sizing:border-box;padding:.55rem .8rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-primary);font-size:.85rem;">
                <option value="">{{ __('Tous') }}</option>
                @foreach($hotels as $h)
                <option value="{{ $h->id }}" @selected(request('hotel_id') == $h->id)>{{ $h->short_name ?? $h->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="pointage-filter-field">
            <label style="display:block;font-size:.75rem;color:var(--text-secondary);font-weight:600;margin-bottom:.35rem;">{{ __('Employé') }}</label>
            <select name="staff_id" style="width:100%;box-sizing:border-box;padding:.55rem .8rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-primary);font-size:.85rem;">
                <option value="">{{ __('Tous') }}</option>
                @foreach($staffList as $s)
                <option value="{{ $s->id }}" @selected(request('staff_id') == $s->id)>{{ $s->first_name }} {{ $s->last_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="pointage-filter-submit" style="padding:.6rem 1.3rem;border-radius:10px;background:var(--ci-orange);border:none;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;">
            <i class="fa-solid fa-filter"></i> {{ __('Filtrer') }}
        </button>
    </form>
</div>

{{-- TABLEAU DES POINTAGES --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-list" style="color:var(--ci-orange);"></i> {{ __('Détail des pointages') }}</span>
    </div>
    <div style="overflow-x:auto;">
    <table class="table" style="min-width:720px;">
        <thead>
            <tr>
                <th>{{ __('Employé') }}</th>
                <th>{{ __('Hôtel') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Arrivée') }}</th>
                <th>{{ __('Départ') }}</th>
                <th>{{ __('Retard') }}</th>
                <th>{{ __('Heures') }}</th>
                <th>{{ __('Statut') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pointages as $p)
            <tr>
                <td style="font-size:.85rem;font-weight:600;color:var(--text-primary);">{{ $p->staff->first_name ?? '' }} {{ $p->staff->last_name ?? '' }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ $p->hotel->short_name ?? $p->hotel->name ?? '—' }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ $p->date->format('d/m/Y') }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ $p->check_in?->format('H:i') ?? '—' }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ $p->check_out?->format('H:i') ?? '—' }}</td>
                <td style="font-size:.85rem;color:{{ $p->late_minutes > 0 ? '#fbbf24' : 'var(--text-secondary)' }};">
                    {{ $p->late_minutes > 0 ? $p->late_minutes . ' min' : '—' }}
                </td>
                <td style="font-size:.85rem;color:var(--text-secondary);">
                    {{ $p->worked_minutes !== null ? sprintf('%02d:%02d', intdiv($p->worked_minutes, 60), $p->worked_minutes % 60) : '—' }}
                </td>
                <td>
                    <span class="badge badge-{{ match($p->status) { 'present' => 'success', 'retard' => 'warning', default => 'secondary' } }}">
                        {{ match($p->status) { 'present' => __('Présent'), 'retard' => __('Retard'), default => __('Absent') } }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem">{{ __('Aucun pointage sur cette période.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- ABSENCES --}}
@if($absences->count())
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-user-xmark" style="color:#fca5a5;"></i> {{ __('Absences (shifts planifiés sans pointage)') }}</span>
    </div>
    <div style="overflow-x:auto;">
    <table class="table" style="min-width:480px;">
        <thead>
            <tr>
                <th>{{ __('Employé') }}</th>
                <th>{{ __('Hôtel') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Shift prévu') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absences as $a)
            <tr>
                <td style="font-size:.85rem;font-weight:600;color:var(--text-primary);">{{ $a->staff->first_name ?? '' }} {{ $a->staff->last_name ?? '' }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ $a->hotel->short_name ?? $a->hotel->name ?? '—' }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ \Illuminate\Support\Carbon::parse($a->date)->format('d/m/Y') }}</td>
                <td style="font-size:.85rem;color:var(--text-secondary);">{{ $a->shift_label ?? $a->shift }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

<style>
.pointage-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}
.pointage-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.pointage-export-btn { white-space: nowrap; }
.pointage-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: .85rem;
}
.pointage-filter-field { flex: 1 1 160px; min-width: 140px; }
.pointage-filter-submit { flex-shrink: 0; }

@media (max-width: 720px) {
    .pointage-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .pointage-header-inner { flex-direction: column; align-items: stretch; }
    .pointage-export-btn { justify-content: center; }
    .pointage-filters { flex-direction: column; align-items: stretch; }
    .pointage-filter-field { min-width: 0; }
    .pointage-filter-submit { width: 100%; justify-content: center; display: flex; align-items: center; }
}
@media (max-width: 420px) {
    .pointage-stats-grid { gap: .6rem; }
}
</style>

@endsection
