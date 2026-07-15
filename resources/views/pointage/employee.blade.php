@extends('layouts.app')

@section('content')

@php
$now = now();
$hasCheckedIn  = $today && $today->check_in;
$hasCheckedOut = $today && $today->check_out;
@endphp

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Pointage') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">
                    {{ $staff->first_name }} {{ $staff->last_name }} — {{ $staff->hotel->name ?? '' }}
                </p>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:12px;padding:.85rem 1.1rem;margin-bottom:1.25rem;color:#4ade80;font-size:.9rem;display:flex;align-items:center;gap:.6rem;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:rgba(230,57,70,.1);border:1px solid rgba(230,57,70,.3);border-radius:12px;padding:.85rem 1.1rem;margin-bottom:1.25rem;color:#fca5a5;font-size:.9rem;display:flex;align-items:center;gap:.6rem;">
    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
</div>
@endif

{{-- WIDGET DE POINTAGE --}}
<div class="card pointage-clock-card" style="margin-bottom:1.5rem;text-align:center;">
    <div style="font-size:.85rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.4rem;">
        {{ $now->translatedFormat('l d F Y') }}
    </div>
    <div id="liveClock" class="pointage-clock-time" style="font-weight:800;color:var(--text-primary);font-variant-numeric:tabular-nums;margin-bottom:1.75rem;">
        {{ $now->format('H:i') }}
    </div>

    @if(!$hasCheckedIn)
        <form method="POST" action="{{ route('pointage.check-in') }}">
            @csrf
            <button type="submit" class="pointage-clock-btn" style="border-radius:16px;background:var(--ci-green);border:none;color:#fff;font-size:1.05rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.6rem;box-shadow:0 8px 24px rgba(0,154,68,.3);">
                <i class="fa-solid fa-right-to-bracket"></i> {{ __("Pointer l'arrivée") }}
            </button>
        </form>
    @elseif(!$hasCheckedOut)
        <div style="margin-bottom:1.25rem;color:#4ade80;font-size:.95rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ __('Arrivée pointée à') }} <strong>{{ $today->check_in->format('H:i') }}</strong>
            @if($today->late_minutes > 0)
                <span style="color:#fbbf24;">— {{ __('retard de') }} {{ $today->late_minutes }} min</span>
            @endif
        </div>
        <form method="POST" action="{{ route('pointage.check-out') }}">
            @csrf
            <button type="submit" class="pointage-clock-btn" style="border-radius:16px;background:var(--ci-orange);border:none;color:#fff;font-size:1.05rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.6rem;box-shadow:0 8px 24px rgba(247,127,0,.3);">
                <i class="fa-solid fa-right-from-bracket"></i> {{ __('Pointer le départ') }}
            </button>
        </form>
    @else
        <div style="color:#4ade80;font-size:1rem;">
            <i class="fa-solid fa-circle-check" style="font-size:1.8rem;display:block;margin-bottom:.6rem;"></i>
            {{ __('Journée terminée') }} — {{ $today->check_in->format('H:i') }} → {{ $today->check_out->format('H:i') }}
            <div style="color:var(--text-secondary);font-size:.85rem;margin-top:.3rem;">
                {{ sprintf('%02d:%02d', intdiv($today->worked_minutes, 60), $today->worked_minutes % 60) }} {{ __('travaillées') }}
            </div>
        </div>
    @endif
</div>

{{-- HISTORIQUE --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-clock-rotate-left" style="color:var(--ci-orange);"></i> {{ __('Historique (30 derniers jours)') }}</span>
    </div>
    <div style="overflow-x:auto;">
    <table class="table" style="min-width:560px;">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Arrivée') }}</th>
                <th>{{ __('Départ') }}</th>
                <th>{{ __('Retard') }}</th>
                <th>{{ __('Heures') }}</th>
                <th>{{ __('Statut') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $p)
            <tr>
                <td style="font-size:.85rem;color:var(--text-primary);">{{ $p->date->format('d/m/Y') }}</td>
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
                <td colspan="6" style="text-align:center;color:var(--text-muted);padding:2rem">{{ __('Aucun pointage enregistré.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<style>
.pointage-clock-card { padding: 2.5rem 1.5rem; }
.pointage-clock-time { font-size: 3rem; }
.pointage-clock-btn  { padding: 1.1rem 3rem; width: auto; }

@media (max-width: 480px) {
    .pointage-clock-card { padding: 1.75rem 1rem; }
    .pointage-clock-time { font-size: 2.25rem; }
    .pointage-clock-btn  { padding: 1rem 1.25rem; width: 100%; }
}
</style>

<script>
function tickClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const d = new Date();
    el.textContent = String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
}
setInterval(tickClock, 15000);
</script>

@endsection
