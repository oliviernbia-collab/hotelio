@extends('layouts.app')

@section('content')

{{-- BANDEAU HÔTEL DE L'EMPLOYÉ --}}
@if(auth()->user()->role === 'employe')
@php $empHotel = auth()->user()->getEmployeeHotel(); @endphp
<div style="display:flex;align-items:center;flex-wrap:wrap;gap:1rem;padding:1rem 1.25rem;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);border-radius:16px;margin-bottom:1.5rem;">
    <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-orange);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:white;flex-shrink:0;">
        <i class="fa-solid fa-hotel"></i>
    </div>
    <div style="flex:1;min-width:150px;">
        <div style="font-weight:700;color:var(--text-primary);">
            {{ $empHotel?->name ?? 'Hôtel non trouvé' }}
        </div>
        <div style="font-size:.82rem;color:var(--text-secondary);">
            {{ $empHotel?->city }} — {{ $empHotel?->quartier }}
            · {{ __("Votre hôtel d'affectation") }}
        </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
        @foreach(auth()->user()->permissions ?? [] as $perm)
        @php $pd = \App\Models\User::availablePermissions()[$perm] ?? null; @endphp
        @if($pd)
        <span style="padding:.2rem .55rem;border-radius:999px;font-size:.7rem;font-weight:700;background:{{ $pd['color'] }}18;color:{{ $pd['color'] }};border:1px solid {{ $pd['color'] }}30;">
            <i class="fa-solid {{ $pd['icon'] }}"></i> {{ $pd['label'] }}
        </span>
        @endif
        @endforeach
    </div>
</div>
@endif

{{-- BANDEAU ABONNEMENT (PRESTATAIRE) --}}
@if(auth()->user()->role === 'prestataire')
@php
    $subColor = match($subscription?->status) {
        'active' => 'var(--ci-green)',
        'past_due' => '#E63946',
        default => 'var(--ci-orange)',
    };
    $subLabel = match($subscription?->status) {
        'active' => __('Actif'),
        'past_due' => __('Paiement en retard'),
        'pending_payment' => __('En attente de paiement'),
        default => __('Aucun abonnement'),
    };
@endphp
<div style="display:flex;align-items:center;flex-wrap:wrap;gap:1rem;padding:1rem 1.25rem;background:{{ $subColor }}18;border:1px solid {{ $subColor }}40;border-radius:16px;margin-bottom:1.5rem;">
    <div style="width:44px;height:44px;border-radius:12px;background:{{ $subColor }};display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:white;flex-shrink:0;">
        <i class="fa-solid fa-credit-card"></i>
    </div>
    <div style="flex:1;min-width:200px;">
        <div style="font-weight:700;color:var(--text-primary);">
            {{ __('Abonnement') }} {{ $subscription?->plan->name ?? '—' }} — <span style="color:{{ $subColor }}">{{ $subLabel }}</span>
        </div>
        <div style="font-size:.82rem;color:var(--text-secondary);">
            @if($subscription?->current_period_end)
                {{ __('Échéance') }} : {{ $subscription->current_period_end->format('d/m/Y') }}
            @else
                {{ __("Aucun paiement enregistré pour l'instant.") }}
            @endif
        </div>
    </div>
    @if($subscription && $subscription->status !== 'active')
    <form method="POST" action="{{ route('subscription.pay') }}" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
        @csrf
        <select name="payment_method" required style="padding:.5rem .7rem;border-radius:10px;border:1px solid rgba(148,163,184,.25);background:rgba(148,163,184,.06);color:inherit;font-size:.82rem;">
            <option value="Orange Money">Orange Money</option>
            <option value="MTN Mobile Money">MTN Mobile Money</option>
            <option value="Wave">Wave</option>
            <option value="Moov Money">Moov Money</option>
            <option value="DJAMO">Djamo ({{ __('carte prépayée') }})</option>
        </select>
        <button type="submit" class="btn" style="padding:.55rem 1.1rem;font-size:.85rem;white-space:nowrap;">
            <i class="fa-solid fa-credit-card"></i> {{ __('Payer mon abonnement') }} ({{ number_format($subscription->plan->price_monthly, 0, ',', ' ') }} FCFA)
        </button>
    </form>
    @endif
</div>

@if(! $subscription)
<div class="card" style="margin-bottom:1.5rem;border-color:var(--ci-orange-border)">
    <div class="card-title"><span><i class="fa-solid fa-layer-group" style="color:var(--ci-orange)"></i> {{ __('Choisissez votre palier pour activer un abonnement') }}</span></div>
    <form method="POST" action="{{ route('subscription.choose') }}">
        @csrf
        <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1rem">
            @foreach($plans as $plan)
            <label style="cursor:pointer;border:2px solid rgba(148,163,184,.15);border-radius:14px;padding:1rem;text-align:center;display:block;">
                <input type="radio" name="plan" value="{{ $plan->slug }}" {{ $loop->first ? 'checked' : '' }} style="margin-bottom:.5rem">
                <div style="font-weight:700">{{ $plan->name }}</div>
                <div style="font-size:1.2rem;font-weight:800;color:var(--ci-orange)">{{ number_format($plan->price_monthly, 0, ',', ' ') }}</div>
                <div style="font-size:.72rem;color:#94a3b8">FCFA / {{ __('mois') }}</div>
            </label>
            @endforeach
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-check"></i> {{ __("Démarrer mon abonnement") }}</button>
    </form>
</div>
@endif
@endif

<style>
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
.stat-box{background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.14);border-radius:14px;padding:1.25rem 1.5rem}
.stat-box .lbl{font-size:.78rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.4rem}
.stat-box .val{font-size:1.9rem;font-weight:700;line-height:1}
.stat-box .sub{font-size:.75rem;color:#64748b;margin-top:.35rem}
.ob{border-left:3px solid var(--ci-orange)}
.gb{border-left:3px solid var(--ci-green)}
.bb{border-left:3px solid #3b82f6}
.rb{border-left:3px solid #a855f7}

.hotel-mini{display:flex;align-items:center;gap:1rem;padding:.9rem 0;border-bottom:1px solid rgba(148,163,184,.1)}
.hotel-mini:last-child{border-bottom:none}
.hotel-mini-img{width:52px;height:52px;border-radius:10px;overflow:hidden;background:linear-gradient(135deg,#1a3558,#0d2240);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.4rem}
.hotel-mini-img img{width:100%;height:100%;object-fit:cover}
.hm-name{font-weight:600;font-size:.9rem}
.hm-city{font-size:.75rem;color:#94a3b8}
.hm-stars{display:flex;gap:2px;margin-top:3px}
.hm-stars i{font-size:10px;color:#f4a423}

@media (max-width: 1120px) {
  .stat-grid{grid-template-columns:repeat(2,1fr)}
}
@media (max-width: 600px) {
  .stat-grid{grid-template-columns:1fr}
}
</style>

<!-- ══ SLIDESHOW HERO Prestataire ══ -->
<div class="dash-slideshow" id="prestSlideshow" style="min-height:210px;margin-bottom:1.5rem">
  @php
    $imgs = $myHotels->filter(fn($h)=>$h->image)->take(4);
    $fallbacks = ['images/h1.jpg','images/h2.jpg','images/h5.jpg','images/h10.jpg'];
  @endphp
  @if($imgs->count())
    @foreach($imgs as $h)
    <div class="dss-slide" style="background-image:url('{{ asset('storage/'.$h->image) }}')"></div>
    @endforeach
  @else
    @foreach($fallbacks as $fb)
    <div class="dss-slide" style="background-image:url('{{ asset($fb) }}')"></div>
    @endforeach
  @endif

  <div class="dss-overlay">
    <div class="dss-content">
      <div class="dss-badge"><i class="fa-solid fa-building"></i> {{ __('Espace Prestataire') }}</div>
      <div class="dss-title">{{ __('Bonjour,') }}<br>{{ auth()->user()->name }}</div>
      <div class="dss-sub">{{ now()->translatedFormat('l d F Y') }}</div>
    </div>
    <div class="dss-stats">
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $todayCheckins }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Arrivées auj.') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $todayCheckouts }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Départs auj.') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $activeReservations }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Réservations') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $availableRooms }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Chambres libres') }}</div>
      </div>
    </div>
  </div>

  <div class="dss-dots">
    @foreach(range(1, max(1, $imgs->count() ?: 4)) as $d)
    <button class="dss-dot"></button>
    @endforeach
  </div>
</div>

<!-- Stats -->
<div class="stat-grid">
  <div class="stat-box ob card-hover">
    <div class="lbl">{{ __('Mes Hôtels') }}</div>
    <div class="val"><span data-count="{{ $myHotels->count() }}">0</span></div>
    <div class="sub">{{ __('Propriétés gérées') }}</div>
  </div>
  <div class="stat-box gb card-hover">
    <div class="lbl">{{ __('Réservations actives') }}</div>
    <div class="val"><span data-count="{{ $activeReservations }}">0</span></div>
    <div class="sub">{{ __('Confirmées en cours') }}</div>
  </div>
  <div class="stat-box bb card-hover">
    <div class="lbl">{{ __('Chambres disponibles') }}</div>
    <div class="val"><span data-count="{{ $availableRooms }}">0</span> <span style="font-size:.9rem;font-weight:400;color:#94a3b8">/ {{ $totalRooms }}</span></div>
    <div class="sub">{{ __('Taux de disponibilité') }}</div>
  </div>
  <div class="stat-box rb card-hover">
    <div class="lbl">{{ __('Revenus totaux') }}</div>
    <div class="val" style="font-size:1.1rem;letter-spacing:-.5px;">{{ number_format($totalRevenue, 0, ',', ' ') }}</div>
    <div class="sub">{{ __('FCFA encaissés') }}</div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1fr 1.5fr;gap:1.25rem;margin-bottom:1.5rem">

  <!-- Mes hôtels -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem">
      <div style="font-size:.95rem;font-weight:700"><i class="fa-solid fa-hotel" style="color:var(--ci-orange)"></i> {{ __('Mes Hôtels') }}</div>
      <a href="{{ route('hotels.index') }}" class="btn-secondary" style="font-size:.78rem">{{ __('Gérer') }}</a>
    </div>
    @forelse($myHotels as $hotel)
    <div class="hotel-mini">
      <div class="hotel-mini-img">
        @if($hotel->image)
          <img src="{{ $hotel->image_url }}" alt="">
        @else
          {{ $hotel->emoji ?? '🏨' }}
        @endif
      </div>
      <div style="flex:1">
        <div class="hm-name">{{ $hotel->name }}</div>
        <div class="hm-city"><i class="fa-solid fa-location-dot"></i> {{ $hotel->city }}</div>
        <div class="hm-stars">
          @for($i=1;$i<=5;$i++)
            <i class="fa-{{ $i <= $hotel->stars ? 'solid' : 'regular' }} fa-star"></i>
          @endfor
        </div>
      </div>
      <div style="text-align:right">
        <div style="font-size:.85rem;font-weight:600;color:var(--ci-green)">{{ number_format($hotel->price,0,',',' ') }} FCFA</div>
        <div style="font-size:.7rem;color:#94a3b8">{{ __('/ nuit') }}</div>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:2rem;color:#94a3b8">
      <i class="fa-solid fa-hotel" style="font-size:2rem;margin-bottom:.5rem;opacity:.4"></i>
      <p>{{ __('Aucun hôtel enregistré.') }}</p>
      <a href="{{ route('hotels.index') }}" class="btn" style="margin-top:.75rem">{{ __('Ajouter un hôtel') }}</a>
    </div>
    @endforelse
  </div>

  <!-- Réservations récentes -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem">
      <div style="font-size:.95rem;font-weight:700"><i class="fa-solid fa-calendar-check" style="color:var(--ci-green)"></i> {{ __('Réservations Récentes') }}</div>
      <a href="{{ route('reservations.index') }}" class="btn-secondary" style="font-size:.78rem">{{ __('Voir tout') }}</a>
    </div>
    @forelse($recentReservations as $res)
    <div class="meta-line" style="padding:.7rem 0;border-bottom:1px solid rgba(148,163,184,.08)">
      <div>
        <div style="font-weight:600;font-size:.88rem">{{ $res->client->first_name ?? '—' }} {{ $res->client->last_name ?? '' }}</div>
        <div style="font-size:.73rem;color:#94a3b8">
          {{ $res->hotel->short_name ?? '' }} — Ch. {{ $res->room }}
          · {{ $res->checkin->format('d/m') }} → {{ $res->checkout->format('d/m') }}
        </div>
      </div>
      <div style="text-align:right">
        <span class="badge badge-{{ $res->status === 'confirmée' ? 'success' : ($res->status === 'annulée' ? 'danger' : 'warning') }}">
          {{ ucfirst($res->status) }}
        </span>
        <div style="font-size:.78rem;font-weight:600;margin-top:3px">{{ number_format($res->total,0,',',' ') }} FCFA</div>
      </div>
    </div>
    @empty
    <p style="color:#94a3b8;text-align:center;padding:1.5rem">{{ __('Aucune réservation pour vos hôtels.') }}</p>
    @endforelse
  </div>
</div>

<!-- Actions rapides -->
<div class="grid grid-cols-6" style="gap:1rem">
  <a href="{{ route('hotels.index') }}" class="card"
     style="text-align:center;padding:1.25rem;text-decoration:none;transition:transform .2s"
     onmouseover="this.style.transform='translateY(-3px)'"
     onmouseout="this.style.transform='none'">
    <i class="fa-solid fa-hotel" style="font-size:1.6rem;color:var(--ci-orange);margin-bottom:.5rem"></i>
    <div style="font-weight:600;font-size:.85rem">{{ __('Mes Hôtels') }}</div>
  </a>
  @php $links = [
    ['route' => 'reservations.index', 'icon' => 'fa-calendar-check', 'label' => __('Réservations'), 'color' => 'var(--ci-green)'],
    ['route' => 'rooms.index', 'icon' => 'fa-door-open', 'label' => __('Chambres'), 'color' => '#3b82f6'],
    ['route' => 'clients.index', 'icon' => 'fa-users', 'label' => __('Clients'), 'color' => 'var(--ci-orange)'],
    ['route' => 'staff.index', 'icon' => 'fa-user-tie', 'label' => __('Personnel'), 'color' => '#a855f7'],
    ['route' => 'calendar.index', 'icon' => 'fa-calendar', 'label' => __('Calendrier'), 'color' => '#e63946'],
  ] @endphp
  @foreach($links as $l)
  <a href="{{ route($l['route']) }}" class="card"
     style="text-align:center;padding:1.25rem;text-decoration:none;transition:transform .2s"
     onmouseover="this.style.transform='translateY(-3px)'"
     onmouseout="this.style.transform='none'">
    <i class="fa-solid {{ $l['icon'] }}" style="font-size:1.6rem;color:{{ $l['color'] }};margin-bottom:.5rem"></i>
    <div style="font-weight:600;font-size:.85rem">{{ $l['label'] }}</div>
  </a>
  @endforeach
</div>
@endsection
