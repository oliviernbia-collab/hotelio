@extends('layouts.app')

@section('content')
<style>
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:1.5rem}
.cal-day{min-height:80px;border-radius:10px;padding:.5rem;background:rgba(148,163,184,.05);border:1px solid rgba(148,163,184,.1);font-size:.8rem}
.cal-day.today{border-color:var(--ci-orange);background:rgba(247,127,0,.08)}
.cal-day.other-month{opacity:.4}
.cal-day-num{font-weight:700;color:#94a3b8;margin-bottom:4px}
.cal-day.today .cal-day-num{color:var(--ci-orange)}
.cal-event{font-size:.68rem;padding:2px 6px;border-radius:4px;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;cursor:pointer}
.cal-header{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:6px}
.cal-header div{text-align:center;font-size:.75rem;font-weight:600;text-transform:uppercase;color:#64748b;padding:.25rem}

.event-list{display:flex;flex-direction:column;gap:.5rem}
.event-item{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border-radius:10px;background:rgba(148,163,184,.06);border-left:3px solid}
.event-date{font-size:.75rem;font-weight:600;min-width:60px;color:#94a3b8}
.event-title{font-size:.88rem;font-weight:600;flex:1}
.event-hotel{font-size:.73rem;color:#94a3b8}
</style>

<div class="card" style="margin-bottom:1.5rem">
  <div style="display:flex;align-items:center;justify-content:space-between">
    <div>
      <div style="font-size:1.2rem;font-weight:700"><i class="fa-solid fa-calendar-days" style="color:var(--ci-orange)"></i> {{ __('Calendrier des réservations') }}</div>
      <p style="margin:.25rem 0 0;color:#94a3b8">{{ now()->format('F Y') }} — {{ __('Arrivées et départs planifiés.') }}</p>
    </div>
    <div style="display:flex;gap:.5rem">
      <span style="display:flex;align-items:center;gap:.4rem;font-size:.8rem;color:#94a3b8">
        <span style="width:10px;height:10px;border-radius:3px;background:var(--ci-green);display:inline-block"></span> {{ __('Arrivée') }}
      </span>
      <span style="display:flex;align-items:center;gap:.4rem;font-size:.8rem;color:#94a3b8">
        <span style="width:10px;height:10px;border-radius:3px;background:var(--ci-orange);display:inline-block"></span> {{ __('Départ') }}
      </span>
      <span style="display:flex;align-items:center;gap:.4rem;font-size:.8rem;color:#94a3b8">
        <span style="width:10px;height:10px;border-radius:3px;background:#e63946;display:inline-block"></span> {{ __('Annulé') }}
      </span>
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1.8fr 1fr;gap:1.25rem">

  <!-- Mini calendrier -->
  <div class="card">
    @php
      $today = now();
      $startOfMonth = $today->copy()->startOfMonth();
      $endOfMonth = $today->copy()->endOfMonth();
      $startCal = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
      $endCal = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

      // Index events by date
      $eventsByDate = [];
      foreach($events as $event) {
          $date = $event['date'];
          $eventsByDate[$date][] = $event;
      }
    @endphp

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
      <div style="font-weight:700;font-size:1rem">{{ $today->format('F Y') }}</div>
      <div style="font-size:.8rem;color:#94a3b8">{{ count($events) }} {{ __('événements ce mois') }}</div>
    </div>

    <div class="cal-header">
      @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $day)
        <div>{{ __($day) }}</div>
      @endforeach
    </div>

    <div class="cal-grid">
      @php $current = $startCal->copy(); @endphp
      @while($current <= $endCal)
      @php
        $dateStr = $current->format('Y-m-d');
        $isToday = $current->isToday();
        $isOtherMonth = $current->month !== $today->month;
        $dayEvents = $eventsByDate[$dateStr] ?? [];
      @endphp
      <div class="cal-day {{ $isToday ? 'today' : '' }} {{ $isOtherMonth ? 'other-month' : '' }}">
        <div class="cal-day-num">{{ $current->day }}</div>
        @foreach(array_slice($dayEvents, 0, 2) as $ev)
          <div class="cal-event" style="background:{{ $ev['color'] }}22;color:{{ $ev['color'] }}" title="{{ $ev['title'] }}">
            {{ Str::limit($ev['title'], 15) }}
          </div>
        @endforeach
        @if(count($dayEvents) > 2)
          <div style="font-size:.65rem;color:#94a3b8">+{{ count($dayEvents) - 2 }} {{ __('autres') }}</div>
        @endif
      </div>
      @php $current->addDay(); @endphp
      @endwhile
    </div>
  </div>

  <!-- Liste des événements -->
  <div class="card">
    <div style="font-weight:700;font-size:.95rem;margin-bottom:1rem">
      <i class="fa-solid fa-list" style="color:var(--ci-green)"></i> {{ __('Événements à venir') }}
    </div>

    @php
      $upcoming = collect($events)
        ->filter(fn($e) => $e['date'] >= now()->toDateString())
        ->sortBy('date')
        ->take(12);
    @endphp

    @forelse($upcoming as $event)
    <div class="event-item" style="border-color:{{ $event['color'] }}">
      <div style="min-width:50px;text-align:center">
        <div style="font-size:1.1rem;font-weight:700;color:{{ $event['color'] }}">
          {{ \Carbon\Carbon::parse($event['date'])->format('d') }}
        </div>
        <div style="font-size:.68rem;color:#64748b;text-transform:uppercase">
          {{ \Carbon\Carbon::parse($event['date'])->format('M') }}
        </div>
      </div>
      <div style="flex:1">
        <div class="event-title">{{ $event['title'] }}</div>
        @if(isset($event['end']))
        <div class="event-hotel">
          <i class="fa-solid fa-arrow-right" style="font-size:.6rem"></i>
          {{ __('Départ:') }} {{ \Carbon\Carbon::parse($event['end'])->format('d/m') }}
        </div>
        @endif
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:2rem;color:#94a3b8">
      <i class="fa-solid fa-calendar-xmark" style="font-size:2rem;opacity:.4;margin-bottom:.5rem"></i>
      <p>{{ __('Aucun événement à venir.') }}</p>
    </div>
    @endforelse

    @php $past = collect($events)->filter(fn($e) => $e['date'] < now()->toDateString())->count(); @endphp
    @if($past > 0)
    <div style="margin-top:1rem;padding:.75rem;background:rgba(148,163,184,.06);border-radius:8px;text-align:center;font-size:.8rem;color:#64748b">
      <i class="fa-solid fa-clock-rotate-left"></i> {{ $past }} {{ __('événement(s) passé(s) non affichés') }}
    </div>
    @endif
  </div>
</div>

<!-- Stats du jour -->
<div class="grid" style="grid-template-columns:repeat(4,1fr);gap:1rem;margin-top:1.25rem">
  @php
    $user = auth()->user();
    $hotelIds = $user->role === 'prestataire'
      ? \App\Models\Hotel::where('user_id', $user->id)->pluck('id')
      : \App\Models\Hotel::pluck('id');

    $todayCheckins  = \App\Models\Reservation::whereIn('hotel_id', $hotelIds)->whereDate('checkin', today())->count();
    $todayCheckouts = \App\Models\Reservation::whereIn('hotel_id', $hotelIds)->whereDate('checkout', today())->count();
    $weekCheckins   = \App\Models\Reservation::whereIn('hotel_id', $hotelIds)->whereBetween('checkin', [now()->startOfWeek(), now()->endOfWeek()])->count();
    $monthTotal     = \App\Models\Reservation::whereIn('hotel_id', $hotelIds)->whereMonth('checkin', now()->month)->count();
  @endphp
  @foreach([
    ['icon' => 'fa-right-to-bracket', 'color' => 'var(--ci-green)', 'val' => $todayCheckins, 'label' => __('Arrivées aujourd\'hui')],
    ['icon' => 'fa-right-from-bracket', 'color' => 'var(--ci-orange)', 'val' => $todayCheckouts, 'label' => __('Départs aujourd\'hui')],
    ['icon' => 'fa-calendar-week', 'color' => '#3b82f6', 'val' => $weekCheckins, 'label' => __('Arrivées cette semaine')],
    ['icon' => 'fa-calendar-check', 'color' => '#a855f7', 'val' => $monthTotal, 'label' => __('Réservations ce mois')],
  ] as $s)
  <div class="card" style="text-align:center;padding:1.25rem">
    <i class="fa-solid {{ $s['icon'] }}" style="font-size:1.6rem;color:{{ $s['color'] }};margin-bottom:.5rem"></i>
    <div style="font-size:1.8rem;font-weight:700">{{ $s['val'] }}</div>
    <div style="font-size:.78rem;color:#94a3b8">{{ $s['label'] }}</div>
  </div>
  @endforeach
</div>
@endsection
