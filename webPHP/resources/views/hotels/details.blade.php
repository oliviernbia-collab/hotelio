<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $hotel->name }} — Hôtelio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --ocean:#0C2340;--sand:#F8F0E3;--sand2:#F2E8D6;
  --coral:#F77F00;--primary:#F77F00;--primary-light:#e06e00;
  --ci-green:#009A44;
  --white:#FFFFFF;--text:#1A1A1A;--text-2:#4A4A4A;--text-3:#7A7A7A;
  --border:#E8E0D4;
  --bg-page:#F8F5F0;
}
html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg-page);color:var(--text);padding-top:76px}
a{text-decoration:none;color:inherit}
body::before{content:"";position:fixed;top:0;left:0;width:100%;height:4px;z-index:1000;background:linear-gradient(to right,#F77F00 33%,#FFFFFF 33% 66%,#009E60 66%)}
[data-theme="dark"] body{background:#0f172a;color:#e2e8f0}

.wrap{max-width:1100px;margin:0 auto;padding:28px 24px 60px}

/* En-tête */
.hd-title{font-family:'Fraunces',serif;font-size:clamp(24px,3.4vw,34px);font-weight:500;color:var(--ocean);margin-bottom:6px}
[data-theme="dark"] .hd-title{color:#e2e8f0}
.hd-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;color:var(--text-3);font-size:14px;margin-bottom:18px}
.hd-stars i{color:#f4a423;font-size:12px}
.hd-price{font-size:18px;font-weight:700;color:var(--ci-green)}
.back-link{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--text-3);margin-bottom:14px}
.back-link:hover{color:var(--coral)}

/* Grille principale */
.main-grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px}
@media (max-width:860px){.main-grid{grid-template-columns:1fr}}

.card{background:white;border-radius:18px;padding:20px;box-shadow:0 2px 12px rgba(12,31,61,.07);border:1px solid var(--border);margin-bottom:20px}
[data-theme="dark"] .card{background:#1e293b;border-color:rgba(148,163,184,.12)}
.card h2{font-size:15px;font-weight:700;color:var(--ocean);margin-bottom:14px;display:flex;align-items:center;gap:8px}
[data-theme="dark"] .card h2{color:#e2e8f0}
.card p{color:var(--text-2);line-height:1.7;font-size:14px}
[data-theme="dark"] .card p{color:#94a3b8}

.main-img{width:100%;height:320px;object-fit:cover;border-radius:14px;cursor:pointer;display:block}
.main-img-hint{margin-top:10px;font-size:12.5px;color:var(--text-3);text-align:center}

.services-wrap{display:flex;flex-wrap:wrap;gap:8px}
.svc-tag{font-size:12.5px;padding:5px 12px;border-radius:999px;background:rgba(247,127,0,.1);color:var(--coral);font-weight:500}

/* Chambres */
.rooms-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px}
.room-card{border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:box-shadow .2s}
.room-card:hover{box-shadow:0 8px 24px rgba(12,31,61,.1)}
.room-img{width:100%;height:120px;object-fit:cover;cursor:pointer;display:block}
.room-img-ph{width:100%;height:120px;background:var(--sand);display:flex;align-items:center;justify-content:center;color:var(--text-3)}
.room-body{padding:12px}
.room-number{font-weight:700;font-size:14px;color:var(--ocean)}
[data-theme="dark"] .room-number{color:#e2e8f0}
.room-type{font-size:12.5px;color:var(--text-3);margin:4px 0 8px}
.room-price{font-weight:700;color:var(--ci-green);font-size:14px}
.room-badge{display:inline-block;font-size:11px;font-weight:600;padding:2px 9px;border-radius:999px;margin-top:6px}
.room-badge.dispo{background:rgba(0,154,68,.12);color:#0a7a37}
.room-badge.autre{background:rgba(148,163,184,.15);color:var(--text-3)}

/* Galerie modale */
#galleryModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:1000;align-items:center;justify-content:center;flex-direction:column;padding:1.5rem}
#galleryModal button.close-btn{position:absolute;top:1rem;right:1.5rem;background:none;border:none;color:#fff;font-size:1.8rem;cursor:pointer;line-height:1}
#galleryMainImg{max-width:90vw;max-height:65vh;object-fit:contain;border-radius:.75rem}
#galleryLabel{color:#fff;margin-top:.75rem;font-size:.95rem}
#galleryThumbs{display:flex;gap:.5rem;margin-top:1rem;flex-wrap:wrap;justify-content:center;max-width:90vw}
#galleryThumbs img{width:64px;height:48px;object-fit:cover;border-radius:.4rem;cursor:pointer}

/* Réservation */
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--text-2);margin-bottom:6px}
[data-theme="dark"] .form-group label{color:#cbd5e1}
.form-group input,.form-group select,.form-group textarea{
  width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:10px;
  font-size:14px;font-family:inherit;background:var(--bg-page);color:var(--text);
}
[data-theme="dark"] .form-group input,[data-theme="dark"] .form-group select,[data-theme="dark"] .form-group textarea{
  background:#0f172a;border-color:rgba(148,163,184,.2);color:#e2e8f0;
}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media (max-width:480px){.grid2{grid-template-columns:1fr}}
.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;background:var(--coral);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;width:100%}
.btn-primary:hover{background:var(--primary-light)}
.cta-login{text-align:center;padding:24px 10px}
.cta-login p{color:var(--text-3);margin-bottom:14px}
.cta-login-actions{display:flex;flex-wrap:wrap;justify-content:center;gap:10px}
.cta-login a{display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:10px;font-weight:700;font-size:14px}
.cta-login .l1{background:var(--coral);color:white;border:1.5px solid var(--coral)}
.cta-login .l1:hover{background:var(--primary-light);border-color:var(--primary-light)}
.cta-login .l2{background:white;border:1.5px solid var(--ocean);color:var(--ocean)}
.cta-login .l2:hover{background:var(--ocean);color:white}
.pm-card{border:2px solid var(--border);border-radius:12px;padding:.6rem .8rem;cursor:pointer;display:flex;align-items:center;gap:.5rem;font-size:13px}
.pm-card.pm-active{border-color:var(--coral);background:rgba(247,127,0,.08)}
#avail-badge{display:none;font-size:12px;font-weight:700;padding:3px 10px;border-radius:999px}

/* Avis */
.review-row{padding:14px 0;border-bottom:1px solid var(--border)}
[data-theme="dark"] .review-row{border-color:rgba(148,163,184,.12)}
.review-ava{width:36px;height:36px;border-radius:50%;background:var(--coral);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0}
.alert{border-radius:10px;padding:12px 14px;margin-bottom:14px;font-size:13.5px}
.alert-err{background:rgba(230,57,70,.1);border:1px solid rgba(230,57,70,.25);color:#b91c1c}
.alert-ok{background:rgba(0,154,68,.1);border:1px solid rgba(0,154,68,.25);color:#0a7a37}
</style>
</head>
<body>

@include('partials.site-navbar')

<div class="wrap">
    <a href="{{ route('hotels.public') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> {{ __('Retour à la recherche') }}</a>

    <div class="hd-title">{{ $hotel->name }}</div>
    <div class="hd-meta">
        <span><i class="fa-solid fa-location-dot"></i> {{ $hotel->quartier }}, {{ $hotel->city }}</span>
        <span class="hd-stars">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa-{{ $i <= $hotel->stars ? 'solid' : 'regular' }} fa-star"></i>
            @endfor
        </span>
        <span><i class="fa-solid fa-star" style="color:#f4a423"></i> {{ number_format($hotel->rating, 1) }} ({{ $reviews->count() }} {{ __('avis') }})</span>
        <span class="hd-price">{{ number_format($hotel->price, 0, ',', ' ') }} FCFA / {{ __('nuit') }}</span>
    </div>

    @php
        $galleryItems = collect();
        if ($hotel->image) {
            $galleryItems->push(['src' => $hotel->image_url, 'label' => $hotel->name]);
        }
        foreach ($rooms as $room) {
            if ($room->image_url) {
                $galleryItems->push(['src' => $room->image_url, 'label' => __('Chambre') . ' ' . $room->number . ' — ' . $room->type]);
            }
        }
    @endphp

    <div class="main-grid">
        <div>
            <div class="card">
                @if($hotel->image)
                    <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" class="main-img" onclick="openGallery(0)">
                    <div class="main-img-hint"><i class="fa-solid fa-images"></i> {{ __("Cliquez sur l'image pour voir les chambres en photos") }}</div>
                @else
                    <div class="room-img-ph" style="height:320px;border-radius:14px;">{{ __('Aucune image') }}</div>
                @endif
            </div>

            <div class="card">
                <h2><i class="fa-solid fa-circle-info" style="color:var(--coral)"></i> {{ __('Description') }}</h2>
                <p>{{ $hotel->description ?: __('Aucune description disponible.') }}</p>
            </div>

            @if($hotel->services && count($hotel->services) > 0)
            <div class="card">
                <h2><i class="fa-solid fa-concierge-bell" style="color:var(--coral)"></i> {{ __('Services') }}</h2>
                <div class="services-wrap">
                    @foreach($hotel->services as $service)
                        <span class="svc-tag">{{ $service }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card">
                <h2><i class="fa-solid fa-bed" style="color:var(--coral)"></i> {{ __('Chambres') }}</h2>
                @if($rooms->isEmpty())
                    <p>{{ __('Aucune chambre renseignée pour le moment.') }}</p>
                @else
                <div class="rooms-grid">
                    @foreach($rooms as $room)
                    <div class="room-card">
                        @if($room->image_url)
                            <img src="{{ $room->image_url }}" alt="{{ $room->number }}" class="room-img" onclick="openGallery(0)">
                        @else
                            <div class="room-img-ph"><i class="fa-solid fa-bed"></i></div>
                        @endif
                        <div class="room-body">
                            <div class="room-number">{{ $room->number }}</div>
                            <div class="room-type">{{ $room->type }} &middot; {{ $room->capacity }} {{ __('pers.') }}</div>
                            <div class="room-price">{{ number_format($room->price, 0, ',', ' ') }} FCFA</div>
                            <span class="room-badge {{ $room->status === 'disponible' ? 'dispo' : 'autre' }}">
                                {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            @php
                if ($hotel->latitude && $hotel->longitude) {
                    $mapSrc   = 'https://maps.google.com/maps?q=' . $hotel->latitude . ',' . $hotel->longitude . '&output=embed&hl=fr&z=16';
                    $mapsLink = 'https://www.google.com/maps?q=' . $hotel->latitude . ',' . $hotel->longitude;
                } else {
                    $mapQuery = $hotel->name . ' ' . $hotel->quartier . ' ' . $hotel->city . ' Côte d\'Ivoire';
                    $mapSrc   = 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed&hl=fr&z=15';
                    $mapsLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapQuery);
                }
            @endphp
            <div class="card">
                <h2><i class="fa-solid fa-location-dot" style="color:var(--coral)"></i> {{ __('Localisation') }}</h2>
                <div style="border-radius:12px;overflow:hidden;border:1px solid var(--border);margin-bottom:10px;">
                    <iframe src="{{ $mapSrc }}" width="100%" height="280" frameborder="0" style="border:0;display:block"
                            allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            title="Localisation {{ $hotel->name }}"></iframe>
                </div>
                <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer" style="font-size:13px;color:var(--coral);font-weight:600;">
                    <i class="fa-solid fa-up-right-from-square"></i> {{ __('Ouvrir dans Google Maps') }}
                </a>
            </div>
        </div>

        <div>
            <div class="card">
                <h2><i class="fa-solid fa-calendar-check" style="color:var(--coral)"></i> {{ __('Réserver') }}</h2>

                @if(session('error'))
                    <div class="alert alert-err"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-ok"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                @endif

                @auth
                <form method="POST" action="{{ auth()->user()->role === 'client' ? route('client.reservations.store') : route('reservations.store') }}">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                    <input type="hidden" name="channel" value="Site web">

                    <div class="grid2">
                        <div class="form-group">
                            <label>{{ __("Date d'arrivée") }}</label>
                            <input type="date" name="checkin" id="checkin" value="{{ old('checkin', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Date de départ') }}</label>
                            <input type="date" name="checkout" id="checkout" value="{{ old('checkout', now()->addDays(1)->toDateString()) }}" min="{{ now()->addDay()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <label style="margin:0;">{{ __('Chambre') }}</label>
                            <span id="avail-badge"></span>
                        </div>
                        <select name="room_id" id="room_id" required>
                            <option value="">{{ __('Sélectionnez une chambre') }}</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-price="{{ $room->price }}"
                                        {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->number }} — {{ $room->type }} — {{ number_format($room->price,0,',',' ') }} FCFA
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Méthode de paiement') }}</label>
                        <input type="hidden" name="payment_method" id="pm_value" value="{{ old('payment_method', 'Orange Money') }}" required>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            @php
                                $detailPayMethods = [
                                    ['value'=>'Orange Money',    'img'=>'images/payment-methods/orange-money.svg'],
                                    ['value'=>'MTN Mobile Money','img'=>'images/payment-methods/mtn-momo.png'],
                                    ['value'=>'Wave',            'img'=>'images/payment-methods/wave.png'],
                                    ['value'=>'Moov Money',      'img'=>'images/payment-methods/moov.png'],
                                    ['value'=>'DJAMO',           'badge'=>'DJAMO',  'bg'=>'#6C2BD9', 'fg'=>'#fff'],
                                    ['value'=>'Espèces',         'fa'=>'fa-money-bill-wave',  'bg'=>'#15803d', 'fg'=>'#fff'],
                                    ['value'=>'Virement',        'fa'=>'fa-building-columns', 'bg'=>'#4f46e5', 'fg'=>'#fff'],
                                ];
                            @endphp
                            @foreach($detailPayMethods as $pm)
                            <div class="pm-card {{ old('payment_method','Orange Money') === $pm['value'] ? 'pm-active' : '' }}" data-value="{{ $pm['value'] }}" onclick="selectPm(this)">
                                @if(isset($pm['img']))
                                    <span style="width:30px;height:26px;border-radius:7px;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;padding:2px;">
                                        <img src="{{ asset($pm['img']) }}" alt="{{ $pm['value'] }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                                    </span>
                                @elseif(isset($pm['fa']))
                                    <span style="width:26px;height:26px;border-radius:7px;background:{{ $pm['bg'] }};color:{{ $pm['fg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.82rem;">
                                        <i class="fa-solid {{ $pm['fa'] }}"></i>
                                    </span>
                                @else
                                    <span style="width:26px;height:26px;border-radius:7px;background:{{ $pm['bg'] }};color:{{ $pm['fg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.56rem;font-weight:800;line-height:1;">
                                        {{ $pm['badge'] }}
                                    </span>
                                @endif
                                {{ $pm['value'] }}
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Nationalité') }}</label>
                        <input type="text" name="nationality" value="{{ old('nationality', 'Côte d\'Ivoire') }}" required>
                    </div>

                    @php
                        $bookedRoomsJs = $bookedRooms->map(fn($r) => [
                            'room_id'  => $r->room_id,
                            'checkin'  => $r->checkin->format('Y-m-d'),
                            'checkout' => $r->checkout->format('Y-m-d'),
                        ]);
                    @endphp
                    <script>const BOOKED_ROOMS = @json($bookedRoomsJs);</script>

                    <div id="price-preview" style="padding:12px;background:rgba(0,154,68,.08);border-radius:10px;margin-bottom:14px;display:none">
                        <div style="font-size:13px;color:var(--text-3)">{{ __('Estimation du coût') }}</div>
                        <div id="price-total" style="font-size:1.3rem;font-weight:700;color:var(--ci-green)"></div>
                    </div>

                    <button type="submit" class="btn-primary"><i class="fa-solid fa-calendar-check"></i> {{ __('Réserver maintenant') }}</button>
                </form>
                @else
                <div class="cta-login">
                    <p>{{ __('Connectez-vous pour réserver une chambre dans cet hôtel.') }}</p>
                    <div class="cta-login-actions">
                        <a href="{{ route('login') }}" class="l1"><i class="fa-solid fa-sign-in-alt"></i> {{ __('Se connecter') }}</a>
                        <a href="{{ route('register') }}" class="l2"><i class="fa-solid fa-user-plus"></i> {{ __('Créer un compte') }}</a>
                    </div>
                </div>
                @endauth
            </div>

            <div class="card">
                <h2><i class="fa-solid fa-star" style="color:#f4a423"></i> {{ __('Avis') }} ({{ $reviews->count() }})</h2>

                @auth
                    @if(auth()->user()->role === 'client')
                    <form method="POST" action="{{ route('reviews.store', $hotel) }}" style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border);">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Note') }}</label>
                            <div style="display:flex;gap:6px" id="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                <label style="cursor:pointer;font-size:1.4rem;color:{{ $userReview && $userReview->rating >= $i ? '#f4a423' : '#cbd5e1' }}">
                                    <input type="radio" name="rating" value="{{ $i }}" style="display:none" {{ $userReview && $userReview->rating == $i ? 'checked' : '' }}>
                                    ★
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea name="comment" rows="2" placeholder="{{ __('Partagez votre expérience...') }}" required minlength="10" maxlength="2000">{{ old('comment', $userReview?->comment) }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> {{ $userReview ? __('Mettre à jour') : __('Publier') }}</button>
                    </form>
                    @endif
                @endauth

                @forelse($reviews as $review)
                <div class="review-row">
                    <div style="display:flex;gap:10px;">
                        <div class="review-ava">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                        <div style="flex:1;min-width:0">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;flex-wrap:wrap">
                                <strong style="font-size:13.5px">{{ $review->user->name ?? 'Utilisateur' }}</strong>
                                <div>
                                    @for($i=1;$i<=5;$i++)
                                        <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star" style="font-size:10px;color:#f4a423"></i>
                                    @endfor
                                </div>
                            </div>
                            <p style="font-size:13.5px;color:var(--text-3);line-height:1.6;margin:0">{{ $review->comment }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p style="color:var(--text-3);font-size:13.5px">{{ __('Aucun avis pour cet hôtel.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="galleryModal">
    <button class="close-btn" onclick="closeGallery()">&times;</button>
    <img id="galleryMainImg" src="" alt="">
    <div id="galleryLabel"></div>
    <div id="galleryThumbs"></div>
</div>

@include('partials.public-footer')

<script>
// ── Galerie hôtel + chambres ────────────────────────────────
const GALLERY_ITEMS = @json($galleryItems);
let galleryIndex = 0;

function renderGallery() {
    if (!GALLERY_ITEMS.length) return;
    const item = GALLERY_ITEMS[galleryIndex];
    document.getElementById('galleryMainImg').src = item.src;
    document.getElementById('galleryMainImg').alt = item.label;
    document.getElementById('galleryLabel').textContent = item.label;

    const thumbs = document.getElementById('galleryThumbs');
    thumbs.innerHTML = '';
    GALLERY_ITEMS.forEach((it, i) => {
        const t = document.createElement('img');
        t.src = it.src;
        t.title = it.label;
        t.style.opacity = (i === galleryIndex ? '1' : '.5');
        t.style.border = '2px solid ' + (i === galleryIndex ? 'var(--coral)' : 'transparent');
        t.onclick = () => { galleryIndex = i; renderGallery(); };
        thumbs.appendChild(t);
    });
}

function openGallery(startIndex) {
    if (!GALLERY_ITEMS.length) return;
    galleryIndex = startIndex || 0;
    renderGallery();
    document.getElementById('galleryModal').style.display = 'flex';
}

function closeGallery() {
    document.getElementById('galleryModal').style.display = 'none';
}

// ── Sélection méthode de paiement ───────────────────────────
function selectPm(el) {
    document.querySelectorAll('.pm-card').forEach(c => c.classList.remove('pm-active'));
    el.classList.add('pm-active');
    document.getElementById('pm_value').value = el.dataset.value;
}

// ── Étoiles d'avis ───────────────────────────────────────────
document.querySelectorAll('#star-rating label').forEach((star, idx, stars) => {
    star.addEventListener('mouseover', () => stars.forEach((s, i) => s.style.color = i <= idx ? '#f4a423' : '#cbd5e1'));
    star.addEventListener('mouseout', () => {
        const checked = document.querySelector('#star-rating input:checked');
        const checkedVal = checked ? parseInt(checked.value) : 0;
        stars.forEach((s, i) => s.style.color = i < checkedVal ? '#f4a423' : '#cbd5e1');
    });
    star.addEventListener('click', () => stars.forEach((s, i) => s.style.color = i <= idx ? '#f4a423' : '#cbd5e1'));
});

// ── Disponibilité & estimation de prix ──────────────────────
const checkinInput  = document.getElementById('checkin');
const checkoutInput = document.getElementById('checkout');
const roomSelect    = document.getElementById('room_id');

function checkAvailability() {
    if (!checkinInput || !checkoutInput || !roomSelect || typeof BOOKED_ROOMS === 'undefined') return;
    const checkin  = checkinInput.value;
    const checkout = checkoutInput.value;
    if (!checkin || !checkout || checkin >= checkout) return;

    const dIn  = new Date(checkin);
    const dOut = new Date(checkout);
    const occupiedIds = BOOKED_ROOMS
        .filter(r => new Date(r.checkin) < dOut && new Date(r.checkout) > dIn)
        .map(r => r.room_id);

    let availCount = 0;
    roomSelect.querySelectorAll('option').forEach(opt => {
        if (!opt.value) return;
        const id = parseInt(opt.value);
        if (occupiedIds.includes(id)) {
            opt.disabled = true;
            if (opt.selected) { opt.selected = false; roomSelect.value = ''; }
        } else {
            opt.disabled = false;
            availCount++;
        }
    });

    const badge = document.getElementById('avail-badge');
    if (badge) {
        badge.style.display = 'inline-flex';
        if (availCount === 0) {
            badge.textContent = '{{ __("Aucune chambre disponible") }}';
            badge.style.background = 'rgba(230,57,70,.15)'; badge.style.color = '#b91c1c';
        } else {
            badge.textContent = availCount + ' {{ __("chambre(s) disponible(s)") }}';
            badge.style.background = 'rgba(0,154,68,.12)'; badge.style.color = '#0a7a37';
        }
    }
    updatePrice();
}

function updatePrice() {
    if (!checkinInput || !checkoutInput || !roomSelect) return;
    const checkin  = new Date(checkinInput.value);
    const checkout = new Date(checkoutInput.value);
    const opt = roomSelect.options[roomSelect.selectedIndex];
    if (!opt || !opt.value || opt.disabled) {
        document.getElementById('price-preview').style.display = 'none';
        return;
    }
    const nights = Math.max(1, Math.round((checkout - checkin) / (1000 * 60 * 60 * 24)));
    const price  = parseInt(opt.dataset.price || 0);
    if (price > 0 && nights > 0 && checkout > checkin) {
        document.getElementById('price-preview').style.display = 'block';
        document.getElementById('price-total').textContent =
            (price * nights).toLocaleString('fr-FR') + ' FCFA — ' + nights + ' {{ __("nuit(s)") }}';
    }
}

if (checkinInput)  checkinInput.addEventListener('change',  checkAvailability);
if (checkoutInput) checkoutInput.addEventListener('change', checkAvailability);
if (roomSelect)    roomSelect.addEventListener('change',    updatePrice);
if (checkinInput?.value && checkoutInput?.value) checkAvailability();
</script>
</body>
</html>
