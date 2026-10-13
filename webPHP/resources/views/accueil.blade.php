@extends('layouts.app')

@section('title', __('Trouvez Votre Endroit de Rêve'))

@push('styles')
<style>
/* ══════════════════════════════════════════════════════
   HERO
   ══════════════════════════════════════════════════════ */
.hero {
    position: relative;
    min-height: 480px;
    background: linear-gradient(135deg, #0c1f3d 0%, #1a3328 55%, #0d1018 100%);
    display: flex; align-items: center;
    overflow: hidden;
    padding: 4rem 2rem 3rem;
    margin: -1.5rem -1.75rem 0;
}
.hero-overlay {
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 30% top, rgba(247,127,0,.18) 0%, transparent 60%),
        radial-gradient(ellipse at 70% bottom, rgba(0,154,68,.12) 0%, transparent 60%);
    pointer-events: none;
}
.hero-content {
    position: relative; z-index: 1;
    max-width: 860px; margin: 0 auto; width: 100%;
    text-align: center;
}
.hero-eyebrow {
    font-size: .8rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .14em; color: #fbbf24;
    margin-bottom: .6rem;
}
.hero-title {
    font-size: clamp(1.9rem, 4.5vw, 3.2rem);
    font-weight: 800; color: white;
    line-height: 1.15; margin-bottom: 2rem;
}

/* ── Search bar ── */
.search-bar {
    display: flex; align-items: center;
    background: white; border-radius: 60px;
    padding: .45rem .45rem .45rem 1.1rem;
    box-shadow: 0 8px 48px rgba(0,0,0,.3);
    max-width: 820px; margin: 0 auto;
    gap: .2rem;
}
.search-type { position: relative; flex-shrink: 0; }
.search-type-btn {
    display: flex; align-items: center; gap: .4rem;
    padding: .55rem .8rem; border: none; background: none;
    font-weight: 700; font-size: .88rem; color: #111;
    cursor: pointer; border-radius: 40px;
    transition: background .15s; white-space: nowrap;
    font-family: inherit;
}
.search-type-btn:hover { background: #f5f5f5; }
.search-type-dropdown {
    position: absolute; top: calc(100% + 8px); left: 0;
    background: white; border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,.14);
    padding: .4rem; z-index: 200; display: none; min-width: 160px;
}
.search-type-dropdown.open { display: block; }
.search-type-dropdown a {
    display: block; padding: .6rem 1rem; border-radius: 10px;
    font-size: .88rem; color: #111; text-decoration: none;
    transition: background .15s;
}
.search-type-dropdown a:hover { background: #f5f5f5; }
.search-divider { width: 1px; height: 30px; background: #e0e0e0; flex-shrink: 0; }
.search-field {
    display: flex; flex-direction: column; gap: 1px;
    padding: .35rem .7rem; flex: 1; min-width: 0;
}
.search-field label {
    font-size: .63rem; font-weight: 800; color: #111;
    text-transform: uppercase; letter-spacing: .06em;
    white-space: nowrap;
}
.search-field input {
    border: none !important; outline: none; font-size: .86rem; color: #333;
    background: transparent !important; width: 100%;
    font-family: inherit; padding: 0 !important; border-radius: 0 !important;
    box-shadow: none !important;
}
.search-field input::placeholder { color: #aaa; }
.search-submit {
    width: 46px; height: 46px; border-radius: 50%;
    background: var(--ci-orange); color: white; border: none;
    cursor: pointer; font-size: .95rem;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, transform .15s; flex-shrink: 0;
}
.search-submit:hover { background: var(--ci-orange2); transform: scale(1.06); }

/* ══════════════════════════════════════════════════════
   CATEGORIES
   ══════════════════════════════════════════════════════ */
.categories-section { padding: 1.25rem 0 .5rem; }
.categories-container {
    display: flex; align-items: center; gap: 1rem;
    padding: 0 .25rem;
}
.categories-scroll {
    display: flex; gap: .25rem; overflow-x: auto;
    scrollbar-width: none; flex: 1; padding: .25rem 0;
}
.categories-scroll::-webkit-scrollbar { display: none; }
.category-btn {
    display: flex; flex-direction: column; align-items: center; gap: .3rem;
    padding: .65rem .9rem; border: none; background: none;
    cursor: pointer; color: var(--text-muted);
    font-size: .73rem; font-weight: 600; font-family: inherit;
    border-bottom: 2px solid transparent;
    transition: color .2s, border-color .2s;
    white-space: nowrap; flex-shrink: 0;
}
.category-btn i { font-size: 1.15rem; }
.category-btn:hover { color: var(--text-primary); border-bottom-color: var(--text-secondary); }
.category-btn.active { color: var(--text-primary); border-bottom-color: var(--text-primary); }
.btn-filters {
    display: flex; align-items: center; gap: .45rem;
    padding: .6rem .9rem; border: 1px solid var(--border);
    border-radius: 10px; background: none; cursor: pointer;
    color: var(--text-primary); font-size: .82rem; font-weight: 600;
    font-family: inherit; transition: background .15s; flex-shrink: 0;
}
.btn-filters:hover { background: var(--bg-input); }

/* ══════════════════════════════════════════════════════
   PROPERTY SECTIONS
   ══════════════════════════════════════════════════════ */
.properties-section { margin-bottom: 2rem; }
.section-container { padding: 0 .25rem; }
.section-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1rem;
}
.section-title { font-size: 1.08rem; font-weight: 700; color: var(--text-primary); }
.nav-arrows { display: flex; gap: .4rem; }
.arrow-btn {
    width: 34px; height: 34px; border-radius: 50%;
    border: 1px solid var(--border); background: var(--bg-input);
    color: var(--text-secondary); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; transition: border-color .2s, color .2s, background .2s;
}
.arrow-btn:hover { border-color: var(--ci-orange); color: var(--ci-orange); background: var(--ci-orange-dim); }

/* Cards track — horizontal scroll */
.cards-track {
    display: flex; gap: 1.1rem;
    overflow-x: auto; scroll-snap-type: x mandatory;
    scrollbar-width: none; padding-bottom: .5rem;
    scroll-behavior: smooth;
}
.cards-track::-webkit-scrollbar { display: none; }

/* ══════════════════════════════════════════════════════
   PROPERTY CARD
   ══════════════════════════════════════════════════════ */
.property-card {
    flex: 0 0 272px; scroll-snap-align: start;
    background: var(--bg-card); border: 1px solid var(--card-border);
    border-radius: 20px; overflow: hidden;
    transition: transform .25s, box-shadow .25s;
    display: flex; flex-direction: column;
}
.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 48px rgba(0,0,0,.22);
}

/* Image */
.card-img-wrap {
    position: relative; overflow: hidden;
    aspect-ratio: 4/3; flex-shrink: 0;
}
.card-img-link {
    display: block; width: 100%; height: 100%; overflow: hidden;
}
.card-img-link img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .45s ease;
    display: block;
}
.card-img-link:hover img { transform: scale(1.07); }

/* Wishlist button — au-dessus du lien image */
.btn-wishlist {
    position: absolute; top: 10px; right: 10px; z-index: 10;
    width: 32px; height: 32px; border-radius: 50%;
    background: rgba(255,255,255,.88); border: none;
    cursor: pointer; font-size: .88rem; color: #222;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s, color .15s;
    backdrop-filter: blur(4px);
}
.btn-wishlist:hover { background: white; color: #e63946; }

/* Card body */
.card-body {
    display: flex; align-items: center; justify-content: space-between;
    padding: .85rem 1rem .4rem; flex: 1;
}
.card-info { flex: 1; min-width: 0; }
.card-name {
    font-weight: 700; font-size: .9rem; color: var(--text-primary);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: .15rem;
}
.card-price {
    font-size: .8rem; color: var(--text-secondary);
    display: flex; align-items: baseline; gap: .2rem;
    margin-bottom: .2rem;
}
.price-amount { font-weight: 700; color: var(--text-primary); }
.price-duration { color: var(--text-muted); }
.card-rating {
    display: flex; align-items: center; gap: .3rem;
    font-size: .78rem; color: var(--text-primary); font-weight: 600;
}
.card-rating i { color: #fbbf24; font-size: .72rem; }
.btn-card-arrow {
    width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
    background: var(--ci-orange-dim); color: var(--ci-orange);
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; text-decoration: none;
    transition: background .2s, transform .15s;
}
.btn-card-arrow:hover { background: var(--ci-orange); color: white; transform: scale(1.1); }

/* ── Bouton "Voir & Réserver" ── */
.btn-view-reserve {
    display: flex; align-items: center; justify-content: center;
    gap: .45rem; margin: .3rem .75rem .75rem;
    padding: .6rem 1rem; border-radius: 12px;
    background: var(--ci-orange); color: white;
    font-weight: 700; font-size: .83rem; text-decoration: none;
    transition: background .2s, transform .15s;
    letter-spacing: .02em;
}
.btn-view-reserve:hover { background: var(--ci-orange2); transform: translateY(-1px); }
.btn-view-reserve i { font-size: .82rem; }

/* ══════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════ */
@media (max-width: 1120px) {
    .hero { margin: -1.5rem -1.75rem 0; }
}
@media (max-width: 720px) {
    .hero { min-height: 380px; padding: 2.5rem 1rem 2rem; margin: -1rem -1rem 0; }
    .hero-title { font-size: 1.7rem; }
    .search-bar {
        flex-wrap: wrap; border-radius: 18px; padding: .75rem;
        gap: .5rem;
    }
    .search-type { width: 100%; }
    .search-type-btn { width: 100%; justify-content: space-between; }
    .search-divider { display: none; }
    .search-field { min-width: calc(50% - .5rem); padding: .35rem .5rem; }
    .search-field input { font-size: .82rem; }
    .search-submit { width: 100%; border-radius: 12px; height: 42px; }
    .property-card { flex: 0 0 230px; }
    .btn-view-reserve { font-size: .78rem; margin: .2rem .6rem .6rem; }
}
</style>
@endpush

@section('content')

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <p class="hero-eyebrow">{{ __('Trouvez Votre Endroit de Rêve') }}</p>
        <h1 class="hero-title">{{ __('Pour une Meilleure Expérience') }}</h1>

        <!-- BARRE DE RECHERCHE -->
        <div class="search-bar">
            <div class="search-type">
                <button class="search-type-btn" id="searchTypeBtn">
                    {{ __('Tous') }} <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="search-type-dropdown" id="searchTypeDropdown">
                    <a href="#">{{ __('Hôtels') }}</a>
                    <a href="#">{{ __('Villas') }}</a>
                    <a href="#">{{ __('Appartements') }}</a>
                    <a href="#">{{ __('Maisons') }}</a>
                </div>
            </div>
            <div class="search-divider"></div>
            <div class="search-field">
                <label>{{ __('Où') }}</label>
                <input type="text" placeholder="{{ __('Rechercher des destinations') }}" name="destination">
            </div>
            <div class="search-divider"></div>
            <div class="search-field">
                <label>{{ __('Arrivée') }}</label>
                <input type="date" name="checkin">
            </div>
            <div class="search-divider"></div>
            <div class="search-field">
                <label>{{ __('Départ') }}</label>
                <input type="date" name="checkout">
            </div>
            <div class="search-divider"></div>
            <div class="search-field">
                <label>{{ __('Voyageurs') }}</label>
                <input type="text" placeholder="{{ __('Ajouter des voyageurs') }}" name="guests">
            </div>
            <button class="search-submit" aria-label="{{ __('Rechercher') }}">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
</section>

<!-- ===== FILTRES PAR CATÉGORIE ===== -->
<section class="categories-section">
    <div class="categories-container">
        <div class="categories-scroll">
            @php
                $categories = [
                    ['icon' => 'fa-border-all', 'label' => 'Tous', 'active' => true],
                    ['icon' => 'fa-water', 'label' => 'Lac'],
                    ['icon' => 'fa-tree', 'label' => 'Parcs Nationaux'],
                    ['icon' => 'fa-house', 'label' => 'Chalets'],
                    ['icon' => 'fa-island-tropical', 'label' => 'Îles'],
                    ['icon' => 'fa-umbrella-beach', 'label' => 'Plages'],
                    ['icon' => 'fa-bolt', 'label' => 'Mini Maisons'],
                    ['icon' => 'fa-tent', 'label' => 'Camping'],
                    ['icon' => 'fa-person-swimming', 'label' => 'Piscines'],
                ];
            @endphp

            @foreach($categories as $cat)
            <button class="category-btn {{ $cat['active'] ?? false ? 'active' : '' }}">
                <i class="fa-solid {{ $cat['icon'] }}"></i>
                <span>{{ __($cat['label']) }}</span>
            </button>
            @endforeach
        </div>

        <button class="btn-filters">
            <i class="fa-solid fa-sliders"></i> {{ __('Filtres') }}
        </button>
    </div>
</section>

<!-- ===== SECTION 1 : TOP RATED ===== -->
<section class="properties-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">{{ __('Séjours les Mieux Notés à Abidjan') }}</h2>
            <div class="nav-arrows">
                <button class="arrow-btn" data-target="toprated" data-dir="prev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="arrow-btn" data-target="toprated" data-dir="next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="cards-track" id="toprated">
            @foreach($topRatedHotels as $hotel)
                <div class="property-card">
                    <div class="card-img-wrap">
                        <a href="{{ route('hotels.show', $hotel['id']) }}" class="card-img-link" aria-label="{{ $hotel['name'] }}">
                            <img src="{{ $hotel['image'] }}" alt="{{ $hotel['name'] }}" loading="lazy">
                        </a>
                        <button class="btn-wishlist" aria-label="{{ __('Favoris') }}">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="card-info">
                            <p class="card-name">{{ $hotel['name'] }}</p>
                            <p class="card-price">
                                <span class="price-amount">{{ number_format($hotel['price']) }} FCFA</span>
                                <span class="price-duration">{{ __('/ 2 nuits') }}</span>
                            </p>
                            <div class="card-rating">
                                <i class="fa-solid fa-star"></i>
                                <span>{{ $hotel['rating'] }}</span>
                            </div>
                        </div>
                        <a href="{{ route('hotels.show', $hotel['id']) }}" class="btn-card-arrow" aria-label="{{ __('Voir & Réserver') }}">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <a href="{{ route('hotels.show', $hotel['id']) }}" class="btn-view-reserve">
                        <i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== SECTION 2 : SÉJOURS WEEK-END ===== -->
<section class="properties-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">{{ __('Séjours Week-End Disponibles à Yamoussoukro') }}</h2>
            <div class="nav-arrows">
                <button class="arrow-btn" data-target="weekend" data-dir="prev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="arrow-btn" data-target="weekend" data-dir="next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="cards-track" id="weekend">
            @foreach($weekendHotels as $hotel)
                <div class="property-card">
                    <div class="card-img-wrap">
                        <a href="{{ route('hotels.show', $hotel['id']) }}" class="card-img-link" aria-label="{{ $hotel['name'] }}">
                            <img src="{{ $hotel['image'] }}" alt="{{ $hotel['name'] }}" loading="lazy">
                        </a>
                        <button class="btn-wishlist" aria-label="{{ __('Favoris') }}">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="card-info">
                            <p class="card-name">{{ $hotel['name'] }}</p>
                            <p class="card-price">
                                <span class="price-amount">{{ number_format($hotel['price']) }} FCFA</span>
                                <span class="price-duration">{{ __('/ 2 nuits') }}</span>
                            </p>
                            <div class="card-rating">
                                <i class="fa-solid fa-star"></i>
                                <span>{{ $hotel['rating'] }}</span>
                            </div>
                        </div>
                        <a href="{{ route('hotels.show', $hotel['id']) }}" class="btn-card-arrow" aria-label="{{ __('Voir & Réserver') }}">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <a href="{{ route('hotels.show', $hotel['id']) }}" class="btn-view-reserve">
                        <i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== SECTION 3 : BELLES MAISONS ===== -->
<section class="properties-section" id="experiences">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">{{ __('Belles Maisons à San-Pédro') }}</h2>
            <div class="nav-arrows">
                <button class="arrow-btn" data-target="homes" data-dir="prev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="arrow-btn" data-target="homes" data-dir="next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="cards-track" id="homes">
            @foreach($beautifulHomes as $hotel)
                <div class="property-card">
                    <div class="card-img-wrap">
                        <a href="{{ route('hotels.show', $hotel['id']) }}" class="card-img-link" aria-label="{{ $hotel['name'] }}">
                            <img src="{{ $hotel['image'] }}" alt="{{ $hotel['name'] }}" loading="lazy">
                        </a>
                        <button class="btn-wishlist" aria-label="{{ __('Favoris') }}">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="card-info">
                            <p class="card-name">{{ $hotel['name'] }}</p>
                            <p class="card-price">
                                <span class="price-amount">{{ number_format($hotel['price']) }} FCFA</span>
                                <span class="price-duration">{{ __('/ 2 nuits') }}</span>
                            </p>
                            <div class="card-rating">
                                <i class="fa-solid fa-star"></i>
                                <span>{{ $hotel['rating'] }}</span>
                            </div>
                        </div>
                        <a href="{{ route('hotels.show', $hotel['id']) }}" class="btn-card-arrow" aria-label="{{ __('Voir & Réserver') }}">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <a href="{{ route('hotels.show', $hotel['id']) }}" class="btn-view-reserve">
                        <i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
/* ── Dropdown type de recherche ── */
document.getElementById('searchTypeBtn').addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('searchTypeDropdown').classList.toggle('open');
});
document.addEventListener('click', function() {
    document.getElementById('searchTypeDropdown').classList.remove('open');
});

/* ── Flèches de navigation des cartes ── */
document.querySelectorAll('.arrow-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const trackId  = btn.dataset.target;
        const dir      = btn.dataset.dir;
        const track    = document.getElementById(trackId);
        if (!track) return;
        const cardW    = track.querySelector('.property-card')?.offsetWidth || 280;
        const scroll   = cardW + 18;
        track.scrollBy({ left: dir === 'next' ? scroll : -scroll, behavior: 'smooth' });
    });
});

/* ── Wishlist animation ── */
document.querySelectorAll('.btn-wishlist').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault(); e.stopPropagation();
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-regular');
        icon.classList.toggle('fa-solid');
        this.style.color = icon.classList.contains('fa-solid') ? '#e63946' : '';
    });
});
</script>
@endpush

@endsection
