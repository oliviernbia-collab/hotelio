@extends('layouts.app')

@section('content')
<style>
.report-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem}
.rbox{background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.14);border-radius:14px;padding:1.25rem}
.rbox .lbl{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:.5rem}
.rbox .val{font-size:2rem;font-weight:700}
.rbox .sub{font-size:.75rem;color:#94a3b8;margin-top:.25rem}
.hotel-bar-row{display:flex;align-items:center;gap:1rem;padding:.75rem 0;border-bottom:1px solid rgba(148,163,184,.1)}
.hotel-bar-row:last-child{border-bottom:none}
.hbar-info{width:180px;flex-shrink:0}
.hbar-name{font-weight:600;font-size:.88rem}
.hbar-city{font-size:.73rem;color:#94a3b8}
.hbar-track{flex:1;height:10px;background:rgba(148,163,184,.15);border-radius:10px;overflow:hidden}
.hbar-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,var(--ci-orange),var(--ci-green))}
.hbar-rev{width:130px;text-align:right;font-size:.85rem;font-weight:700;color:var(--ci-green);flex-shrink:0}
</style>

{{-- En-tête --}}
<div class="card" style="margin-bottom:1.5rem">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <div style="display:flex;align-items:center;gap:1rem">
      <div style="width:48px;height:48px;background:var(--ci-green);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:white">
        <i class="fa-solid fa-chart-bar"></i>
      </div>
      <div>
        <div style="font-size:1.2rem;font-weight:700">{{ __('Rapports & Statistiques') }}</div>
        <p style="margin:.25rem 0 0;color:#94a3b8;font-size:.85rem;">Hôtelio — Côte d'Ivoire</p>
      </div>
    </div>

    {{-- Filtre période + export --}}
    <div style="display:flex;gap:.6rem;flex-wrap:wrap;align-items:center;">
      <form method="GET" action="{{ route('reports.index') }}" style="display:flex;gap:.5rem;align-items:center;">
        @foreach(['day'=>__('Aujourd\'hui'),'week'=>__('Cette semaine'),'month'=>__('Ce mois'),'year'=>__('Cette année'),'all'=>__('Tout')] as $val=>$label)
        <button type="submit" name="period" value="{{ $val }}"
          style="border-radius:999px;padding:.4rem .9rem;font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid rgba(247,127,0,{{ $period===$val ? '1' : '.25' }});background:rgba(247,127,0,{{ $period===$val ? '.25' : '.07' }});color:{{ $period===$val ? '#F77F00' : '#94a3b8' }};">
          {{ $label }}
        </button>
        @endforeach
      </form>
      <a href="{{ route('reports.index', ['period' => $period, 'export' => 'excel']) }}"
         style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(0,154,68,.15);border:1px solid rgba(0,154,68,.35);color:#a7f3d0;border-radius:999px;padding:.4rem 1rem;font-size:.8rem;font-weight:700;">
        <i class="fa-solid fa-file-excel"></i> {{ __('Exporter Excel') }}
      </a>
    </div>
  </div>
</div>

{{-- KPIs principaux --}}
<div class="report-grid">
  <div class="rbox" style="border-left:3px solid var(--ci-orange)">
    <div class="lbl">{{ __("Chiffre d'affaires") }}</div>
    <div class="val" style="color:#F77F00;">{{ $totalRevenue >= 1000000 ? number_format($totalRevenue/1000000,1).'M' : number_format($totalRevenue/1000,0).'K' }}</div>
    <div class="sub">{{ __('FCFA encaissés') }} ({{ ucfirst(str_replace(['day','week','month','year','all'],[__("aujourd'hui"),__('semaine'),__('mois'),__('année'),__('total')], $period)) }})</div>
  </div>
  <div class="rbox" style="border-left:3px solid var(--ci-green)">
    <div class="lbl">{{ __('Réservations') }}</div>
    <div class="val" style="color:#009A44;">{{ $totalReservations }}</div>
    <div class="sub">{{ $confirmedCount }} {{ __('confirmées') }} · {{ $cancelledCount }} {{ __('annulées') }}</div>
  </div>
  <div class="rbox" style="border-left:3px solid #3b82f6">
    <div class="lbl">{{ __('Note moyenne') }}</div>
    <div class="val" style="color:#3b82f6;">{{ number_format($avgRating ?? 4.5, 1) }} ★</div>
    <div class="sub">{{ __('Sur') }} {{ \App\Models\Review::count() }} {{ __('avis') }}</div>
  </div>
  <div class="rbox" style="border-left:3px solid #a855f7">
    <div class="lbl">{{ __('Commissions Hôtelio') }}</div>
    <div class="val" style="color:#a855f7;">{{ $totalCommissions >= 1000000 ? number_format($totalCommissions/1000000,1).'M' : number_format($totalCommissions/1000,0).'K' }}</div>
    <div class="sub">{{ __('FCFA sur réservations apportées par la plateforme') }}</div>
  </div>
</div>

<div class="report-grid">
  <div class="rbox">
    <div class="lbl">{{ __('Hôtels actifs') }}</div>
    <div class="val">{{ \App\Models\Hotel::where('status','Ouvert')->count() }}</div>
    <div class="sub">{{ __('Sur') }} {{ \App\Models\Hotel::count() }} {{ __('au total') }}</div>
  </div>
  <div class="rbox">
    <div class="lbl">{{ __('Clients enregistrés') }}</div>
    <div class="val">{{ \App\Models\Client::count() }}</div>
    <div class="sub">{{ \App\Models\Client::where('status','VIP')->count() }} {{ __('clients VIP') }}</div>
  </div>
  <div class="rbox">
    <div class="lbl">{{ __('Chambres disponibles') }}</div>
    <div class="val">{{ \App\Models\Room::where('status','disponible')->count() }}</div>
    <div class="sub">{{ __('Sur') }} {{ \App\Models\Room::count() }} {{ __('chambres') }}</div>
  </div>
</div>

{{-- Classement hôtels --}}
<div class="card" style="margin-bottom:1.5rem">
  <div class="card-title"><span><i class="fa-solid fa-trophy" style="color:var(--ci-orange)"></i> {{ __('Classement des hôtels par revenus') }}</span></div>
  @php $maxRev = $hotels->max('revenue') ?: 1; @endphp
  @foreach($hotels as $hotel)
  <div class="hotel-bar-row">
    <div class="hbar-info">
      <div class="hbar-name">{{ $hotel->name }}</div>
      <div class="hbar-city"><i class="fa-solid fa-location-dot"></i> {{ $hotel->city }} · {{ $hotel->stars }}★</div>
    </div>
    <div class="hbar-track">
      <div class="hbar-fill" style="width:{{ ($hotel->revenue / $maxRev) * 100 }}%"></div>
    </div>
    <div class="hbar-rev">{{ number_format($hotel->revenue / 1000000, 1) }}M FCFA</div>
    <span class="badge badge-{{ $hotel->occupancy >= 80 ? 'success' : ($hotel->occupancy >= 60 ? 'warning' : 'secondary') }}" style="width:48px;text-align:center;">{{ $hotel->occupancy }}%</span>
  </div>
  @endforeach
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">

  {{-- Statuts réservations --}}
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-calendar-check" style="color:var(--ci-green)"></i> {{ __('Réservations par statut') }}</span></div>
    @foreach(['confirmée'=>[__('Confirmées'),'#009A44'],'en_attente'=>[__('En attente'),'#F77F00'],'annulée'=>[__('Annulées'),'#e63946'],'arrivée'=>[__('Arrivées'),'#3b82f6'],'départie'=>[__('Départs'),'#94a3b8']] as $status=>$cfg)
    @php $cnt = \App\Models\Reservation::where('status',$status)->count(); @endphp
    <div class="meta-line" style="padding:.6rem 0">
      <div style="display:flex;align-items:center;gap:.75rem">
        <div style="width:10px;height:10px;border-radius:50%;background:{{ $cfg[1] }}"></div>
        <span>{{ $cfg[0] }}</span>
      </div>
      <strong>{{ $cnt }}</strong>
    </div>
    @endforeach
  </div>

  {{-- Méthodes de paiement --}}
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-credit-card" style="color:#3b82f6"></i> {{ __('Méthodes de paiement') }}</span></div>
    @php $methods = \App\Models\Reservation::where('paid',true)->selectRaw('payment_method, COUNT(*) as cnt, SUM(total) as tot')->groupBy('payment_method')->get(); @endphp
    @forelse($methods as $m)
    <div class="meta-line" style="padding:.6rem 0">
      <span>{{ $m->payment_method ?: __('Non spécifié') }}</span>
      <div style="text-align:right">
        <div style="font-weight:700">{{ $m->cnt }} {{ __('réserv.') }}</div>
        <div style="font-size:.75rem;color:#94a3b8">{{ number_format($m->tot/1000,0) }} K FCFA</div>
      </div>
    </div>
    @empty
    <p style="color:#94a3b8;text-align:center;padding:1rem">{{ __('Aucune donnée.') }}</p>
    @endforelse
  </div>
</div>

{{-- Statistiques par nationalité (exigence mémoire §2.3.5) --}}
<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;">
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-globe-africa" style="color:#F77F00"></i> {{ __('Fréquentation par nationalité') }}</span></div>
    @php $maxNat = $byNationality->max('total') ?: 1; @endphp
    @forelse($byNationality as $nat)
    <div style="padding:.55rem 0;border-bottom:1px solid rgba(148,163,184,.08);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem;">
        <span style="font-size:.85rem;color:#e2e8f0;">{{ $nat->nationality ?: __('Non renseignée') }}</span>
        <span style="font-size:.8rem;font-weight:700;color:#F77F00;">{{ $nat->total }}</span>
      </div>
      <div style="height:5px;background:rgba(148,163,184,.12);border-radius:99px;overflow:hidden;">
        <div style="height:100%;width:{{ ($nat->total/$maxNat)*100 }}%;background:#F77F00;border-radius:99px;"></div>
      </div>
      <div style="font-size:.72rem;color:#64748b;margin-top:.2rem;">{{ number_format($nat->revenue/1000,0) }} K FCFA</div>
    </div>
    @empty
    <p style="color:#94a3b8;text-align:center;padding:1.5rem;font-size:.85rem;">{{ __('Aucune donnée de nationalité.') }}</p>
    @endforelse
  </div>

  {{-- Types de chambres --}}
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-bed" style="color:#009A44"></i> {{ __('Répartition par type de chambre') }}</span></div>
    @php $totalRooms = $byRoomType->sum('total') ?: 1; @endphp
    @forelse($byRoomType as $rt)
    @php $pct = round(($rt->total / $totalRooms) * 100); @endphp
    <div style="padding:.55rem 0;border-bottom:1px solid rgba(148,163,184,.08);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem;">
        <span style="font-size:.85rem;color:#e2e8f0;">{{ $rt->type }}</span>
        <span style="font-size:.8rem;font-weight:700;color:#009A44;">{{ $rt->total }} ({{ $pct }}%)</span>
      </div>
      <div style="height:5px;background:rgba(148,163,184,.12);border-radius:99px;overflow:hidden;">
        <div style="height:100%;width:{{ $pct }}%;background:#009A44;border-radius:99px;"></div>
      </div>
    </div>
    @empty
    <p style="color:#94a3b8;text-align:center;padding:1.5rem;font-size:.85rem;">{{ __('Aucune chambre enregistrée.') }}</p>
    @endforelse
  </div>
</div>

@endsection
