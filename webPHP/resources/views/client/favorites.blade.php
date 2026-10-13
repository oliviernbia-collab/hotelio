@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem}
.page-title{display:flex;align-items:center;gap:.75rem;font-size:1.4rem;font-weight:700}
.page-title .icon{width:44px;height:44px;border-radius:12px;background:rgba(230,57,70,.15);border:1px solid rgba(230,57,70,.25);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#e63946}

.hotel-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem}
.hotel-card{border-radius:14px;overflow:hidden;background:rgba(148,163,184,.06);border:1px solid rgba(148,163,184,.1);transition:transform .25s,box-shadow .25s;position:relative}
.hotel-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.25)}
.hcard-img{height:160px;overflow:hidden;background:linear-gradient(135deg,#1a3558,#0d2240);display:flex;align-items:center;justify-content:center;font-size:2.5rem;position:relative}
.hcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
.hcard-img-link{display:block;width:100%;height:100%;position:absolute;inset:0;z-index:1}
.hcard-img:hover img{transform:scale(1.06)}
.hcard-body{padding:14px}
.hcard-name{font-weight:700;font-size:.92rem;margin-bottom:3px}
.hcard-loc{font-size:.75rem;color:#94a3b8;display:flex;align-items:center;gap:4px;margin-bottom:8px}
.hcard-stars{display:flex;gap:2px;margin-bottom:8px}
.hcard-stars i{font-size:11px;color:#f4a423}
.hcard-price{font-size:.88rem;font-weight:700;color:var(--ci-orange)}
.hcard-foot{display:flex;align-items:center;justify-content:space-between;margin-top:10px}
.btn-book{display:flex;align-items:center;justify-content:center;gap:.4rem;padding:10px;background:var(--ci-orange);color:white;border-radius:10px;text-align:center;font-size:.82rem;font-weight:700;text-decoration:none;margin-top:10px;transition:background .2s,transform .15s}
.btn-book:hover{background:#e06e00;transform:translateY(-1px)}

.btn-unfav{position:absolute;top:10px;right:10px;z-index:2;width:34px;height:34px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.95rem;transition:all .2s;backdrop-filter:blur(6px);background:rgba(230,57,70,.9);color:white}
.btn-unfav:hover{background:#e63946;transform:scale(1.15)}

.empty-state{text-align:center;padding:4rem 2rem;color:#64748b}
.empty-state .empty-icon{font-size:4rem;opacity:.3;margin-bottom:1rem;display:block}
.empty-state h3{font-size:1.2rem;font-weight:600;margin-bottom:.5rem;color:#94a3b8}
.empty-state p{font-size:.88rem;margin-bottom:1.5rem}
.empty-state a{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;background:var(--ci-orange);color:white;border-radius:10px;text-decoration:none;font-weight:600;transition:background .2s}
.empty-state a:hover{background:#e06e00}

.count-badge{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .8rem;background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.2);border-radius:20px;font-size:.8rem;color:#fca5a5;font-weight:600}
</style>

<div class="page-header">
  <div class="page-title">
    <span class="icon"><i class="fa-solid fa-heart"></i></span>
    {{ __('Mes Favoris') }}
  </div>
  @if($favorites->isNotEmpty())
  <span class="count-badge">
    <i class="fa-solid fa-hotel"></i>
    {{ $favorites->count() }} {{ __('hôtel(s) enregistré(s)') }}
  </span>
  @endif
</div>

@if($favorites->isEmpty())
<div class="card">
  <div class="empty-state">
    <span class="empty-icon">🤍</span>
    <h3>{{ __('Aucun favori pour le moment.') }}</h3>
    <p>{{ __('Parcourez les hôtels et ajoutez vos favoris.') }}</p>
    <a href="{{ route('hotels.public') }}">
      <i class="fa-solid fa-magnifying-glass"></i> {{ __('Explorer les hôtels') }}
    </a>
  </div>
</div>
@else
<div class="card">
  <div class="hotel-grid" id="fav-grid">
    @foreach($favorites as $hotel)
    <div class="hotel-card" id="fav-card-{{ $hotel->id }}">
      <div class="hcard-img">
        @if($hotel->image)
          <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}">
        @else
          {{ $hotel->emoji ?? '🏨' }}
        @endif
        <a href="{{ route('hotels.show', $hotel) }}" class="hcard-img-link" aria-label="{{ __('Voir') }} {{ $hotel->name }}"></a>
        <button class="btn-unfav"
                data-url="{{ route('hotels.favorite', $hotel) }}"
                data-toast="{{ __('Retiré des favoris') }}"
                title="{{ __('Retirer des favoris') }}"
                onclick="removeFav(event, this, {{ $hotel->id }})">
          <i class="fa-solid fa-heart"></i>
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
@endif

@push('scripts')
<script>
function removeFav(e, btn, hotelId) {
  e.preventDefault();
  e.stopPropagation();
  btn.disabled = true;

  fetch(btn.dataset.url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  })
  .then(r => r.json())
  .then(data => {
    const card = document.getElementById('fav-card-' + hotelId);
    card.style.transition = 'opacity .3s, transform .3s';
    card.style.opacity    = '0';
    card.style.transform  = 'scale(.95)';
    setTimeout(() => {
      card.remove();
      const remaining = document.querySelectorAll('#fav-grid .hotel-card').length;
      if (remaining === 0) location.reload();
    }, 300);
    showToast(btn.dataset.toast, 'info');
  })
  .catch(() => { btn.disabled = false; });
}
</script>
@endpush
@endsection
