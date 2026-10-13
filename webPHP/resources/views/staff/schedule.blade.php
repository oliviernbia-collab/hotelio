@extends('layouts.app')

@section('content')

@php
$shifts = [
    'matin'      => ['label' => __('Matin'),       'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,.15)', 'icon' => '🌅'],
    'apres_midi' => ['label' => __('Après-midi'),  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.15)', 'icon' => '☀️'],
    'nuit'       => ['label' => __('Nuit'),        'color' => '#8b5cf6', 'bg' => 'rgba(139,92,246,.15)', 'icon' => '🌙'],
    'repos'      => ['label' => __('Repos'),       'color' => '#6b7280', 'bg' => 'rgba(107,114,128,.15)', 'icon' => '💤'],
];
$dayNames = [__('Lun'), __('Mar'), __('Mer'), __('Jeu'), __('Ven'), __('Sam'), __('Dim')];
$prevWeek = $week > 1 ? $week - 1 : 52;
$prevYear = $week > 1 ? $year : $year - 1;
$nextWeek = $week < 52 ? $week + 1 : 1;
$nextYear = $week < 52 ? $year : $year + 1;
@endphp

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Planning du personnel') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">
                    {{ __('Semaine') }} {{ $week }} — {{ __('du') }} {{ $startOfWeek->format('d/m') }} {{ __('au') }} {{ $startOfWeek->copy()->endOfWeek()->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;">
            <a href="{{ route('staff.schedule', ['week' => $prevWeek, 'year' => $prevYear, 'hotel_id' => $hotelId]) }}"
               style="padding:.5rem .9rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-secondary);text-decoration:none;font-size:.88rem;display:flex;align-items:center;gap:.4rem;">
                <i class="fa-solid fa-chevron-left"></i> {{ __('Préc.') }}
            </a>
            <a href="{{ route('staff.schedule', ['week' => now()->weekOfYear, 'year' => now()->year, 'hotel_id' => $hotelId]) }}"
               style="padding:.5rem .9rem;border-radius:10px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);color:var(--ci-orange);text-decoration:none;font-size:.85rem;font-weight:600;">
                {{ __("Aujourd'hui") }}
            </a>
            <a href="{{ route('staff.schedule', ['week' => $nextWeek, 'year' => $nextYear, 'hotel_id' => $hotelId]) }}"
               style="padding:.5rem .9rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-secondary);text-decoration:none;font-size:.88rem;display:flex;align-items:center;gap:.4rem;">
                {{ __('Suiv.') }} <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:12px;padding:.85rem 1.1rem;margin-bottom:1.25rem;color:#4ade80;font-size:.9rem;display:flex;align-items:center;gap:.6rem;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

{{-- FILTRES --}}
<div class="card" style="margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('staff.schedule') }}" style="display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;">
        <input type="hidden" name="week" value="{{ $week }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <label style="font-size:.8rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Hôtel') }}</label>
        <select name="hotel_id" onchange="this.form.submit()"
            style="padding:.5rem .9rem;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-primary);font-size:.9rem;cursor:pointer;">
            @foreach($hotels as $h)
            <option value="{{ $h->id }}" @selected($h->id == $hotelId)>{{ $h->short_name ?? $h->name }}</option>
            @endforeach
        </select>
        <div style="margin-left:auto;display:flex;gap:1rem;font-size:.8rem;color:var(--text-secondary);">
            @foreach($shifts as $key => $s)
            <span style="display:flex;align-items:center;gap:.35rem;">
                <span style="width:10px;height:10px;border-radius:3px;background:{{ $s['color'] }};display:inline-block;"></span>
                {{ $s['icon'] }} {{ $s['label'] }}
            </span>
            @endforeach
        </div>
    </form>
</div>

{{-- GRILLE PLANNING --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:780px;">
            <thead>
                <tr style="background:var(--bg-input);">
                    <th style="padding:.85rem 1.1rem;text-align:left;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-secondary);border-bottom:1px solid var(--border);min-width:160px;">
                        {{ __('Personnel') }}
                    </th>
                    @foreach($days as $i => $day)
                    <th style="padding:.85rem .75rem;text-align:center;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:{{ $day->isToday() ? 'var(--ci-orange)' : 'var(--text-secondary)' }};border-bottom:1px solid var(--border);border-left:1px solid var(--border);min-width:110px;background:{{ $day->isToday() ? 'var(--ci-orange-dim)' : 'transparent' }};">
                        <div>{{ $dayNames[$i] }}</div>
                        <div style="font-size:.95rem;font-weight:700;color:{{ $day->isToday() ? 'var(--ci-orange)' : 'var(--text-primary)' }};">{{ $day->format('d/m') }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($staffList as $member)
                <tr style="border-bottom:1px solid var(--border);" class="sched-row">
                    <td style="padding:.75rem 1.1rem;vertical-align:middle;">
                        <div style="display:flex;align-items:center;gap:.65rem;">
                            @if($member->photo)
                                <img src="{{ $member->photo_url }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid var(--ci-orange-border);">
                            @else
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--ci-orange-dim);border:2px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.78rem;color:var(--ci-orange);flex-shrink:0;">
                                    {{ strtoupper(substr($member->first_name,0,1)) }}{{ strtoupper(substr($member->last_name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-size:.88rem;font-weight:600;color:var(--text-primary);">{{ $member->first_name }} {{ $member->last_name }}</div>
                                <div style="font-size:.75rem;color:var(--text-secondary);">{{ $member->poste ?? __('Employé') }}</div>
                            </div>
                        </div>
                    </td>
                    @foreach($days as $day)
                    @php
                        $key = $member->id . '_' . $day->format('Y-m-d');
                        $sched = $schedules->get($key);
                        $shiftInfo = $sched ? ($shifts[$sched->first()->shift] ?? null) : null;
                    @endphp
                    <td style="padding:.4rem;text-align:center;border-left:1px solid var(--border);vertical-align:middle;background:{{ $day->isToday() ? 'rgba(247,127,0,.03)' : 'transparent' }};">
                        @if($shiftInfo && $sched->first())
                        @php $sc = $sched->first(); @endphp
                        <button onclick="openEdit('{{ $member->id }}','{{ $day->format('Y-m-d') }}','{{ $sc->shift }}','{{ $sc->start_time ?? '' }}','{{ $sc->end_time ?? '' }}','{{ addslashes($sc->poste ?? '') }}','{{ $hotelId }}')"
                            style="width:100%;padding:.45rem .35rem;border-radius:8px;background:{{ $shiftInfo['bg'] }};border:1px solid {{ $shiftInfo['color'] }}33;color:{{ $shiftInfo['color'] }};font-size:.75rem;font-weight:600;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:.15rem;line-height:1.2;">
                            <span>{{ $shiftInfo['icon'] }}</span>
                            <span>{{ $shiftInfo['label'] }}</span>
                            @if($sc->start_time)
                            <span style="font-size:.68rem;opacity:.8;">{{ substr($sc->start_time,0,5) }}–{{ substr($sc->end_time ?? '',0,5) }}</span>
                            @endif
                        </button>
                        @else
                        <button onclick="openEdit('{{ $member->id }}','{{ $day->format('Y-m-d') }}','','','','','{{ $hotelId }}')"
                            style="width:100%;padding:.45rem .35rem;border-radius:8px;background:transparent;border:1px dashed var(--border);color:var(--text-secondary);font-size:.72rem;cursor:pointer;display:flex;align-items:center;justify-content:center;opacity:.5;transition:opacity .2s;"
                            onmouseover="this.style.opacity='1';this.style.borderColor='var(--ci-orange-border)'"
                            onmouseout="this.style.opacity='.5';this.style.borderColor='var(--border)'">
                            <i class="fa-solid fa-plus" style="font-size:.65rem;"></i>
                        </button>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($days) + 1 }}" style="padding:2.5rem;text-align:center;color:var(--text-secondary);font-size:.9rem;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size:1.8rem;opacity:.3;display:block;margin-bottom:.6rem;"></i>
                        {{ __('Aucun employé dans cet hôtel.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL ÉDITION --}}
<div id="schedModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:20px;padding:2rem;width:100%;max-width:420px;box-shadow:0 24px 64px rgba(0,0,0,.5);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <h3 style="margin:0;font-size:1.1rem;color:var(--text-primary);display:flex;align-items:center;gap:.6rem;">
                <i class="fa-solid fa-calendar-pen" style="color:var(--ci-orange);"></i>
                <span id="modalTitle">{{ __('Modifier le planning') }}</span>
            </h3>
            <button onclick="closeModal()" style="background:transparent;border:none;color:var(--text-secondary);font-size:1.1rem;cursor:pointer;padding:.3rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('staff.schedule.store') }}" id="schedForm">
            @csrf
            <input type="hidden" name="staff_id" id="f_staff_id">
            <input type="hidden" name="hotel_id" id="f_hotel_id">
            <input type="hidden" name="date" id="f_date">

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-secondary);font-weight:600;margin-bottom:.4rem;">{{ __('Date') }}</label>
                <div id="f_date_label" style="font-size:.95rem;font-weight:600;color:var(--text-primary);padding:.6rem 1rem;background:var(--bg-input);border-radius:10px;border:1px solid var(--border);">—</div>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-secondary);font-weight:600;margin-bottom:.6rem;">{{ __('Shift') }}</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;" id="shiftGrid">
                    @foreach($shifts as $key => $s)
                    <label style="cursor:pointer;">
                        <input type="radio" name="shift" value="{{ $key }}" style="display:none;" onchange="updateShiftStyle()">
                        <div class="shift-opt" data-shift="{{ $key }}"
                            style="padding:.65rem .75rem;border-radius:10px;background:{{ $s['bg'] }};border:2px solid transparent;color:{{ $s['color'] }};font-size:.85rem;font-weight:600;text-align:center;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:.4rem;">
                            {{ $s['icon'] }} {{ $s['label'] }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem;">
                <div>
                    <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-secondary);font-weight:600;margin-bottom:.4rem;">{{ __('Début') }}</label>
                    <input type="time" name="start_time" id="f_start"
                        style="width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:10px;padding:.65rem .85rem;color:var(--text-primary);font-size:.9rem;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-secondary);font-weight:600;margin-bottom:.4rem;">{{ __('Fin') }}</label>
                    <input type="time" name="end_time" id="f_end"
                        style="width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:10px;padding:.65rem .85rem;color:var(--text-primary);font-size:.9rem;box-sizing:border-box;">
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:var(--text-secondary);font-weight:600;margin-bottom:.4rem;">{{ __('Poste / Note') }}</label>
                <input type="text" name="poste" id="f_poste" placeholder="{{ __('Ex: Réception, Restauration…') }}"
                    style="width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:10px;padding:.65rem .85rem;color:var(--text-primary);font-size:.9rem;box-sizing:border-box;">
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="button" onclick="closeModal()"
                    style="flex:1;padding:.8rem;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);color:var(--text-secondary);font-size:.9rem;cursor:pointer;">
                    {{ __('Annuler') }}
                </button>
                <button type="submit"
                    style="flex:2;padding:.8rem;border-radius:12px;background:var(--ci-orange);border:none;color:#fff;font-size:.9rem;font-weight:700;cursor:pointer;">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

@php
$daysFrJs = [__('Dimanche'), __('Lundi'), __('Mardi'), __('Mercredi'), __('Jeudi'), __('Vendredi'), __('Samedi')];
$monthsFrJs = [__('jan'), __('fév'), __('mar'), __('avr'), __('mai'), __('jun'), __('jul'), __('aoû'), __('sep'), __('oct'), __('nov'), __('déc')];
@endphp
<script>
const SHIFTS = @json($shifts);
const DAYS_FR = @json($daysFrJs);
const MONTHS_FR = @json($monthsFrJs);

function openEdit(staffId, date, shift, start, end, poste, hotelId) {
    document.getElementById('f_staff_id').value = staffId;
    document.getElementById('f_hotel_id').value = hotelId;
    document.getElementById('f_date').value = date;
    document.getElementById('f_start').value = start;
    document.getElementById('f_end').value = end;
    document.getElementById('f_poste').value = poste;

    const d = new Date(date + 'T12:00:00');
    document.getElementById('f_date_label').textContent =
        DAYS_FR[d.getDay()] + ' ' + d.getDate() + ' ' + MONTHS_FR[d.getMonth()] + ' ' + d.getFullYear();

    // Set radio
    document.querySelectorAll('#shiftGrid input[type=radio]').forEach(r => {
        r.checked = (r.value === shift);
    });
    updateShiftStyle();

    document.getElementById('schedModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('schedModal').style.display = 'none';
}

function updateShiftStyle() {
    document.querySelectorAll('.shift-opt').forEach(el => {
        const key = el.dataset.shift;
        const radio = document.querySelector(`input[name=shift][value="${key}"]`);
        if (radio && radio.checked) {
            el.style.border = '2px solid ' + (SHIFTS[key]?.color ?? '#F77F00');
            el.style.boxShadow = '0 0 0 3px ' + (SHIFTS[key]?.color ?? '#F77F00') + '22';
        } else {
            el.style.border = '2px solid transparent';
            el.style.boxShadow = 'none';
        }
    });
}

document.getElementById('schedModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

@endsection
