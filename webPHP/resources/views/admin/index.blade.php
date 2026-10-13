@extends('layouts.app')

@section('content')
<style>
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
.stat-box{background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.14);border-radius:14px;padding:1.25rem 1.5rem}
.stat-box .label{font-size:.8rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem}
.stat-box .value{font-size:2rem;font-weight:700;line-height:1}
.stat-box .sub{font-size:.78rem;color:#64748b;margin-top:.4rem}
.stat-orange{border-left:3px solid var(--ci-orange)}
.stat-green{border-left:3px solid var(--ci-green)}
.stat-blue{border-left:3px solid #3b82f6}
.stat-red{border-left:3px solid #e63946}

.section-title{font-size:1rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem}

.hotel-row{display:flex;align-items:center;gap:1rem;padding:.75rem 0;border-bottom:1px solid rgba(148,163,184,.1)}
.hotel-row:last-child{border-bottom:none}
.hotel-rank{width:28px;height:28px;border-radius:50%;background:var(--ci-orange-dim);color:var(--ci-orange);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0}
.hotel-info{flex:1}
.hotel-name{font-weight:600;font-size:.9rem}
.hotel-loc{font-size:.75rem;color:#94a3b8}
.hotel-rev{font-size:.85rem;font-weight:700;color:var(--ci-green)}
.stat-grid-3{grid-template-columns:repeat(3,1fr)}

@media (max-width: 1120px) {
  .stat-grid, .stat-grid-3{grid-template-columns:repeat(2,1fr)}
}
@media (max-width: 600px) {
  .stat-grid, .stat-grid-3{grid-template-columns:1fr}
}
</style>

<!-- ══ SLIDESHOW HERO ══ -->
<div class="dash-slideshow" id="adminSlideshow" style="min-height:220px;margin-bottom:1.5rem">
  <div class="dss-slide" style="background-image:url('{{ asset('images/h10.jpg') }}')"></div>
  <div class="dss-slide" style="background-image:url('{{ asset('images/h13.jpg') }}')"></div>
  <div class="dss-slide" style="background-image:url('{{ asset('images/h14.jpg') }}')"></div>
  <div class="dss-slide" style="background-image:url('{{ asset('images/h15.jpg') }}')"></div>

  <div class="dss-overlay">
    <div class="dss-content">
      <div class="dss-badge"><i class="fa-solid fa-crown"></i> {{ __('Panel Administrateur') }}</div>
      <div class="dss-title">Hôtelio<br>{{ __('Administration') }}</div>
      <div class="dss-sub">{{ __('Tableau de bord') }} — {{ now()->format('d/m/Y') }}</div>
    </div>
    <div class="dss-stats">
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $totalUsers }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Utilisateurs') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $totalHotels }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Hôtels') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $totalReservations }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Réservations') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val" style="font-size:1.1rem"><span data-count="{{ (int)($totalRevenue/1000000) }}" data-suffix="M">0</span></div>
        <div class="dss-stat-lbl">{{ __('FCFA Revenus') }}</div>
      </div>
    </div>
  </div>

  <div class="dss-dots">
    <button class="dss-dot"></button>
    <button class="dss-dot"></button>
    <button class="dss-dot"></button>
    <button class="dss-dot"></button>
  </div>
</div>

<!-- Stats principales -->
<div class="stat-grid">
  <div class="stat-box stat-orange card-hover">
    <div class="label">{{ __('Utilisateurs') }}</div>
    <div class="value"><span data-count="{{ $totalUsers }}">0</span></div>
    <div class="sub">{{ __('Comptes enregistrés') }}</div>
  </div>
  <div class="stat-box stat-green card-hover">
    <div class="label">{{ __('Hôtels') }}</div>
    <div class="value"><span data-count="{{ $totalHotels }}">0</span></div>
    <div class="sub">{{ __('Propriétés actives') }}</div>
  </div>
  <div class="stat-box stat-blue card-hover">
    <div class="label">{{ __('Réservations') }}</div>
    <div class="value"><span data-count="{{ $totalReservations }}">0</span></div>
    <div class="sub">{{ $reservationsByStatus['confirmée'] }} {{ __('confirmée(s)') }}</div>
  </div>
  <div class="stat-box stat-red card-hover">
    <div class="label">{{ __('Revenus totaux') }}</div>
    <div class="value" style="font-size:1.1rem;letter-spacing:-.5px;">{{ number_format($totalRevenue, 0, ',', ' ') }}</div>
    <div class="sub">{{ __('FCFA encaissés') }}</div>
  </div>
</div>

<div class="stat-grid stat-grid-3">
  <div class="stat-box">
    <div class="label">{{ __('Clients enregistrés') }}</div>
    <div class="value">{{ $totalClients }}</div>
    <div class="sub">{{ __('Base de données clients') }}</div>
  </div>
  <div class="stat-box">
    <div class="label">{{ __('Réservations annulées') }}</div>
    <div class="value" style="color:#e63946">{{ $reservationsByStatus['annulée'] }}</div>
    <div class="sub">{{ __("Taux d'annulation") }}</div>
  </div>
  <div class="stat-box">
    <div class="label">{{ __('Avis en attente') }}</div>
    <div class="value" style="color:var(--ci-orange)">{{ $pendingReviews }}</div>
    <div class="sub">{{ __('À modérer') }}</div>
  </div>
</div>

<!-- Grille principale -->
<div class="grid grid-cols-2" style="gap:1.25rem;margin-bottom:1.5rem">

  <!-- Top hôtels -->
  <div class="card">
    <div class="section-title"><i class="fa-solid fa-trophy" style="color:var(--ci-orange)"></i> {{ __('Top 5 Hôtels par Revenus') }}</div>
    @forelse($topHotels as $i => $hotel)
    <div class="hotel-row">
      <div class="hotel-rank">{{ $i + 1 }}</div>
      <div class="hotel-info">
        <div class="hotel-name">{{ $hotel->name }}</div>
        <div class="hotel-loc"><i class="fa-solid fa-location-dot"></i> {{ $hotel->city }}</div>
      </div>
      <div class="hotel-rev">{{ number_format($hotel->revenue, 0, ',', ' ') }} FCFA</div>
    </div>
    @empty
    <p style="color:#94a3b8">{{ __('Aucun hôtel enregistré.') }}</p>
    @endforelse
  </div>

  <!-- Utilisateurs récents -->
  <div class="card">
    <div class="section-title"><i class="fa-solid fa-users" style="color:var(--ci-green)"></i> {{ __('Derniers Utilisateurs') }}</div>
    @forelse($recentUsers as $user)
    <div class="meta-line" style="padding:.6rem 0;border-bottom:1px solid rgba(148,163,184,.1)">
      <div>
        <div style="font-weight:600;font-size:.9rem">{{ $user->name }}</div>
        <div style="font-size:.75rem;color:#94a3b8">{{ $user->email }}</div>
      </div>
      <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'prestataire' ? 'warning' : 'success') }}">
        {{ ucfirst($user->role) }}
      </span>
    </div>
    @empty
    <p style="color:#94a3b8">{{ __('Aucun utilisateur.') }}</p>
    @endforelse
    <a href="{{ route('users.index') }}" class="btn" style="margin-top:1rem;width:100%;text-align:center">{{ __('Gérer les utilisateurs') }}</a>
  </div>
</div>

<!-- Réservations récentes -->
<div class="card">
  <div class="section-title"><i class="fa-solid fa-calendar-check" style="color:#3b82f6"></i> {{ __('Réservations Récentes') }}</div>
  <div class="table-responsive">
  <table class="table">
    <thead>
      <tr>
        <th>{{ __('Code') }}</th><th>{{ __('Client') }}</th><th>{{ __('Hôtel') }}</th><th>{{ __('Check-in') }}</th><th>{{ __('Total') }}</th><th>{{ __('Statut') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse($recentReservations as $res)
      <tr>
        <td><code>{{ $res->code }}</code></td>
        <td>{{ $res->client->first_name ?? '—' }} {{ $res->client->last_name ?? '' }}</td>
        <td>{{ $res->hotel->short_name ?? '—' }}</td>
        <td>{{ $res->checkin->format('d/m/Y') }}</td>
        <td>{{ number_format($res->total, 0, ',', ' ') }} FCFA</td>
        <td>
          <span class="badge badge-{{ $res->status === 'confirmée' ? 'success' : ($res->status === 'annulée' ? 'danger' : 'warning') }}">
            {{ ucfirst($res->status) }}
          </span>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:#94a3b8">{{ __('Aucune réservation.') }}</td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>

<!-- Actions rapides -->
<div class="grid grid-cols-4" style="gap:1rem;margin-top:1.25rem">
  <a href="{{ route('users.index') }}" class="card" style="text-align:center;padding:1.5rem;text-decoration:none;transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
    <i class="fa-solid fa-users" style="font-size:1.8rem;color:var(--ci-orange);margin-bottom:.5rem"></i>
    <div style="font-weight:600">{{ __('Utilisateurs') }}</div>
  </a>
  <a href="{{ route('reports.index') }}" class="card" style="text-align:center;padding:1.5rem;text-decoration:none;transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
    <i class="fa-solid fa-chart-bar" style="font-size:1.8rem;color:var(--ci-green);margin-bottom:.5rem"></i>
    <div style="font-weight:600">{{ __('Rapports') }}</div>
  </a>
  <a href="{{ route('settings.index') }}" class="card" style="text-align:center;padding:1.5rem;text-decoration:none;transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
    <i class="fa-solid fa-gear" style="font-size:1.8rem;color:#3b82f6;margin-bottom:.5rem"></i>
    <div style="font-weight:600">{{ __('Paramètres') }}</div>
  </a>
  <a href="{{ route('admin.commandes') }}" class="card" style="text-align:center;padding:1.5rem;text-decoration:none;transition:transform .2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
    <i class="fa-solid fa-shopping-bag" style="font-size:1.8rem;color:#a855f7;margin-bottom:.5rem"></i>
    <div style="font-weight:600">{{ __('Commandes') }}</div>
  </a>
</div>
@endsection
