@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
.stat-box{background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.14);border-radius:14px;padding:1.25rem 1.5rem}
.stat-box .lbl{font-size:.78rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.4rem}
.stat-box .val{font-size:1.9rem;font-weight:700;line-height:1}
.stat-box .sub{font-size:.75rem;color:#64748b;margin-top:.35rem}

.hotel-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem}
.hotel-card{border-radius:14px;overflow:hidden;background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.1);transition:transform .25s,box-shadow .25s}
.hotel-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.25)}
.hcard-img{height:150px;overflow:hidden;background:linear-gradient(135deg,#1a3558,#0d2240);display:flex;align-items:center;justify-content:center;font-size:2.5rem;position:relative}
.hcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
.hcard-img-link{display:block;width:100%;height:100%;position:absolute;inset:0;z-index:1}
.hcard-img:hover img{transform:scale(1.06)}
.btn-fav{position:absolute;top:10px;right:10px;z-index:2;width:34px;height:34px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.95rem;transition:all .2s;backdrop-filter:blur(6px)}
.btn-fav.off{background:rgba(0,0,0,.45);color:rgba(255,255,255,.7)}
.btn-fav.off:hover{background:rgba(230,57,70,.85);color:white;transform:scale(1.15)}
.btn-fav.on{background:rgba(230,57,70,.9);color:white}
.btn-fav.on:hover{background:#e63946;transform:scale(1.15)}
.hcard-body{padding:14px}
.hcard-name{font-weight:700;font-size:.9rem;margin-bottom:3px}
.hcard-loc{font-size:.75rem;color:#94a3b8;display:flex;align-items:center;gap:4px;margin-bottom:8px}
.hcard-stars{display:flex;gap:2px;margin-bottom:8px}
.hcard-stars i{font-size:11px;color:#f4a423}
.hcard-price{font-size:.88rem;font-weight:700;color:var(--ci-orange)}
.hcard-foot{display:flex;align-items:center;justify-content:space-between;margin-top:10px}
.btn-book{display:flex;align-items:center;justify-content:center;gap:.4rem;padding:10px;background:var(--ci-orange);color:white;border-radius:10px;text-align:center;font-size:.82rem;font-weight:700;text-decoration:none;margin-top:10px;transition:background .2s,transform .15s}
.btn-book:hover{background:#e06e00;transform:translateY(-1px)}

.loyalty-bar{height:8px;background:rgba(148,163,184,.2);border-radius:10px;overflow:hidden;margin-top:8px}
.loyalty-fill{height:100%;background:linear-gradient(90deg,var(--ci-orange),var(--ci-green));border-radius:10px;transition:width .5s ease}

@media (max-width: 1120px) {
  .stat-grid{grid-template-columns:repeat(2,1fr)}
}
@media (max-width: 600px) {
  .stat-grid{grid-template-columns:1fr}
}
</style>

<!-- ══ SLIDESHOW HERO Client ══ -->
<div class="dash-slideshow" id="clientSlideshow" style="min-height:200px;margin-bottom:1.5rem">
  <div class="dss-slide" style="background-image:url('{{ asset('images/h14.jpg') }}')"></div>
  <div class="dss-slide" style="background-image:url('{{ asset('images/h15.jpg') }}')"></div>
  <div class="dss-slide" style="background-image:url('{{ asset('images/h16.jpg') }}')"></div>
  <div class="dss-slide" style="background-image:url('{{ asset('images/h10.jpg') }}')"></div>

  <div class="dss-overlay">
    <div class="dss-content">
      <div class="dss-badge"><i class="fa-solid fa-suitcase-rolling"></i> {{ __('Espace Voyageur') }}</div>
      <div class="dss-title">{{ __('Bonjour,') }}<br>{{ auth()->user()->name }} 👋</div>
      <div class="dss-sub">{{ __('Votre espace personnel Hôtelio') }}</div>
    </div>
    <div class="dss-stats">
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $clientReservations->count() }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Réservations') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val"><span data-count="{{ $loyaltyPoints }}">0</span></div>
        <div class="dss-stat-lbl">{{ __('Points fidélité') }}</div>
      </div>
      <div class="dss-stat">
        <div class="dss-stat-val" style="font-size:1.1rem">
          @if($loyaltyPoints >= 5000) 💎
          @elseif($loyaltyPoints >= 2000) 🥇
          @elseif($loyaltyPoints >= 500) 🥈
          @else 🥉
          @endif
        </div>
        <div class="dss-stat-lbl">{{ __('Statut') }}</div>
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

<!-- Stats -->
<div class="stat-grid">
  <div class="stat-box card-hover" style="border-left:3px solid var(--ci-orange)">
    <div class="lbl">{{ __('Mes Réservations') }}</div>
    <div class="val"><span data-count="{{ $clientReservations->count() }}">0</span></div>
    <div class="sub">{{ __('Séjours réservés') }}</div>
  </div>
  <div class="stat-box card-hover" style="border-left:3px solid var(--ci-green)">
    <div class="lbl">{{ __('Points fidélité') }}</div>
    <div class="val"><span data-count="{{ $loyaltyPoints }}">0</span></div>
    <div class="sub">
      <div class="loyalty-bar">
        <div class="loyalty-fill" style="width:{{ min(100, $loyaltyPoints / 100) }}%"></div>
      </div>
    </div>
  </div>
  <div class="stat-box card-hover" style="border-left:3px solid #3b82f6">
    <div class="lbl">{{ __('Total dépensé') }}</div>
    <div class="val" style="font-size:1.1rem;letter-spacing:-.5px;">{{ number_format($totalSpend ?? 0, 0, ',', ' ') }}</div>
    <div class="sub">{{ __('FCFA en séjours') }}</div>
  </div>
  <div class="stat-box" style="border-left:3px solid #a855f7">
    <div class="lbl">{{ __('Statut') }}</div>
    <div class="val" style="font-size:1.3rem">
      @if($loyaltyPoints >= 5000) 💎 {{ __('Platine') }}
      @elseif($loyaltyPoints >= 2000) 🥇 {{ __('Or') }}
      @elseif($loyaltyPoints >= 500) 🥈 {{ __('Argent') }}
      @else 🥉 {{ __('Bronze') }}
      @endif
    </div>
    <div class="sub">{{ __('Niveau de fidélité') }}</div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem">

  <!-- Réservations récentes -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem">
      <div style="font-size:.95rem;font-weight:700"><i class="fa-solid fa-calendar-check" style="color:var(--ci-green)"></i> {{ __('Mes Réservations') }}</div>
      <a href="{{ route('client.reservations') }}" class="btn-secondary" style="font-size:.78rem">{{ __('Voir tout') }}</a>
    </div>
    @forelse($clientReservations as $res)
    <div class="meta-line" style="padding:.7rem 0;border-bottom:1px solid rgba(148,163,184,.08)">
      <div>
        <div style="font-weight:600;font-size:.88rem">{{ $res->hotel->name ?? '—' }}</div>
        <div style="font-size:.73rem;color:#94a3b8">
          <i class="fa-solid fa-location-dot"></i> {{ $res->hotel->city ?? '' }}
          · {{ $res->checkin->format('d/m/Y') }} → {{ $res->checkout->format('d/m/Y') }}
          · {{ $res->nights }} {{ __('nuit(s)') }}
        </div>
      </div>
      <div style="text-align:right">
        <span class="badge badge-{{ $res->status === 'confirmée' ? 'success' : ($res->status === 'annulée' ? 'danger' : 'warning') }}">
          {{ ucfirst($res->status) }}
        </span>
        <div style="font-size:.78rem;font-weight:600;margin-top:3px">{{ number_format($res->total,0,',',' ') }} FCFA</div>
        @if($res->paid)
          <a href="{{ route('reservations.invoice', $res) }}" style="font-size:.7rem;color:var(--ci-green)">
            <i class="fa-solid fa-file-pdf"></i> {{ __('Facture') }}
          </a>
        @endif
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:2rem;color:#94a3b8">
      <i class="fa-solid fa-calendar-xmark" style="font-size:2rem;opacity:.4;margin-bottom:.5rem"></i>
      <p>{{ __('Aucune réservation pour le moment.') }}</p>
    </div>
    @endforelse
  </div>

  <!-- Actions -->
  <div class="card">
    <div style="font-size:.95rem;font-weight:700;margin-bottom:1rem"><i class="fa-solid fa-bolt" style="color:var(--ci-orange)"></i> {{ __('Actions rapides') }}</div>
    <div style="display:flex;flex-direction:column;gap:.75rem">
      <a href="{{ route('hotels.public') }}" style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1rem;border-radius:10px;background:rgba(247,127,0,.08);text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(247,127,0,.15)'" onmouseout="this.style.background='rgba(247,127,0,.08)'">
        <i class="fa-solid fa-magnifying-glass" style="color:var(--ci-orange);font-size:1.1rem"></i>
        <span style="font-weight:600">{{ __('Rechercher des hôtels') }}</span>
      </a>
      <a href="{{ route('client.reservations') }}" style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1rem;border-radius:10px;background:rgba(0,154,68,.08);text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(0,154,68,.15)'" onmouseout="this.style.background='rgba(0,154,68,.08)'">
        <i class="fa-solid fa-calendar-check" style="color:var(--ci-green);font-size:1.1rem"></i>
        <span style="font-weight:600">{{ __('Mes Réservations') }}</span>
      </a>
      <a href="{{ route('boutique.clientboutique') }}" style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1rem;border-radius:10px;background:rgba(59,130,246,.08);text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(59,130,246,.15)'" onmouseout="this.style.background='rgba(59,130,246,.08)'">
        <i class="fa-solid fa-store" style="color:#3b82f6;font-size:1.1rem"></i>
        <span style="font-weight:600">{{ __('Boutique en ligne') }}</span>
      </a>
      <a href="{{ route('profile.index') }}" style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1rem;border-radius:10px;background:rgba(168,85,247,.08);text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(168,85,247,.15)'" onmouseout="this.style.background='rgba(168,85,247,.08)'">
        <i class="fa-solid fa-user-gear" style="color:#a855f7;font-size:1.1rem"></i>
        <span style="font-weight:600">{{ __('Mon Profil') }}</span>
      </a>
    </div>
  </div>
</div>

<!-- Hôtels disponibles -->
<div class="card" style="margin-bottom:1.5rem">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem">
    <div style="font-size:1rem;font-weight:700"><i class="fa-solid fa-hotel" style="color:var(--ci-orange)"></i> {{ __('Hôtels Disponibles') }}</div>
    <a href="{{ route('hotels.public') }}" class="btn-secondary">{{ __('Explorer tout') }}</a>
  </div>
  <div class="hotel-grid">
    @foreach($hotels->take(8) as $hotel)
    <div class="hotel-card">
      <div class="hcard-img">
        @if($hotel->image)
          <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}">
        @else
          {{ $hotel->emoji ?? '🏨' }}
        @endif
        <a href="{{ route('hotels.show', $hotel) }}" class="hcard-img-link" aria-label="{{ __('Voir') }} {{ $hotel->name }}"></a>
        <button class="btn-fav {{ $favoriteIds->contains($hotel->id) ? 'on' : 'off' }}"
                data-id="{{ $hotel->id }}"
                data-url="{{ route('hotels.favorite', $hotel) }}"
                data-add="{{ __('Ajouter aux favoris') }}"
                data-remove="{{ __('Retirer des favoris') }}"
                data-toast-add="{{ __('Ajouté aux favoris ❤️') }}"
                data-toast-remove="{{ __('Retiré des favoris') }}"
                title="{{ $favoriteIds->contains($hotel->id) ? __('Retirer des favoris') : __('Ajouter aux favoris') }}"
                onclick="toggleFav(event, this)">
          <i class="fa-{{ $favoriteIds->contains($hotel->id) ? 'solid' : 'regular' }} fa-heart"></i>
        </button>
      </div>
      <div class="hcard-body">
        <div class="hcard-name">{{ $hotel->name }}</div>
        <div class="hcard-loc"><i class="fa-solid fa-location-dot"></i> {{ $hotel->city }}</div>
        <div class="hcard-stars">
          @for($i=1;$i<=5;$i++)
            <i class="fa-{{ $i <= $hotel->stars ? 'solid' : 'regular' }} fa-star"></i>
          @endfor
        </div>
        <div class="hcard-foot">
          <div class="hcard-price">{{ number_format($hotel->price,0,',',' ') }} FCFA <small style="font-weight:400;color:#94a3b8">{{ __('/ nuit') }}</small></div>
          <div style="font-size:.8rem;display:flex;align-items:center;gap:3px">
            <i class="fa-solid fa-star" style="color:#f4a423;font-size:11px"></i> {{ number_format($hotel->rating,1) }}
          </div>
        </div>
        <a href="{{ route('hotels.show', $hotel) }}" class="btn-book">
          <i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}
        </a>
      </div>
    </div>
    @endforeach
  </div>
</div>
@push('scripts')
<script>
function toggleFav(e, btn) {
  e.preventDefault();
  e.stopPropagation();

  const url       = btn.dataset.url;
  const icon      = btn.querySelector('i');
  const isOn      = btn.classList.contains('on');
  const toastAdd  = btn.dataset.toastAdd;
  const toastRem  = btn.dataset.toastRemove;
  const lblAdd    = btn.dataset.add;
  const lblRem    = btn.dataset.remove;

  btn.disabled = true;
  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.favorite) {
      btn.classList.replace('off', 'on');
      icon.className = 'fa-solid fa-heart';
      btn.title = lblRem;
      showToast(toastAdd, 'success');
    } else {
      btn.classList.replace('on', 'off');
      icon.className = 'fa-regular fa-heart';
      btn.title = lblAdd;
      showToast(toastRem, 'info');
    }
    const badge = document.querySelector('.sidebar .nav-link[href*="mes-favoris"] span[style*="background"]');
    if (badge) badge.textContent = data.count || '';
  })
  .finally(() => { btn.disabled = false; });
}
</script>
@endpush
@endsection
