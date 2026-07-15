@extends('layouts.app')

@section('content')
    <div class="card card-title" style="margin-bottom:1.5rem;">
        <div class="card-title">
            <div>
                <div style="font-size:1.4rem;font-weight:700;">{{ $hotel->name }}</div>
                <p style="margin:.5rem 0 0;color:#94a3b8;">{{ $hotel->city }} — {{ $hotel->quartier }} | {{ $hotel->stars }} étoiles</p>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                @if(!auth()->check() || auth()->user()->role !== 'client')
                <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-primary">Modifier</a>
                <form method="POST" action="{{ route('hotels.destroy', $hotel) }}" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet hôtel ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
                @endif
                <a href="{{ route('hotels.index') }}" class="btn">Retour à la liste</a>
            </div>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 2fr 1fr; gap:1.25rem; margin-bottom:1.5rem;">
        <div class="card">
            <div class="card-title"><span>Détails de l'hôtel</span></div>
            <p>{{ $hotel->description }}</p>
            <div class="meta-list" style="margin-top:1rem;">
                <div class="meta-line"><span>Statut</span><strong>{{ $hotel->status }}</strong></div>
                <div class="meta-line"><span>Prix moyen</span><strong>{{ number_format($hotel->price,0,',',' ') }} FCFA</strong></div>
                <div class="meta-line"><span>Taux d'occupation</span><strong>{{ $hotel->occupancy }} %</strong></div>
                <div class="meta-line"><span>Ouvert depuis</span><strong>{{ $hotel->opened_at?->format('d/m/Y') ?? 'Non renseigné' }}</strong></div>
            </div>
        </div>
        <div class="card">
            <div class="card-title"><span>Directeur</span></div>
            <div class="meta-list">
                <div class="meta-line"><span>Nom</span><strong>{{ $hotel->manager }}</strong></div>
                <div class="meta-line"><span>Téléphone</span><strong>{{ $hotel->phone }}</strong></div>
                <div class="meta-line"><span>Email</span><strong>{{ $hotel->email }}</strong></div>
            </div>
            <div style="margin-top:1.25rem;">
                <div class="badge badge-warning">{{ $hotel->category }}</div>
            </div>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 1fr 1fr; gap:1.25rem; margin-bottom:1.5rem;">
        <div class="card">
            <div class="card-title"><span>Services</span></div>
            @if($hotel->services && count($hotel->services) > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($hotel->services as $service)
                        <div class="badge badge-primary">{{ $service }}</div>
                    @endforeach
                </div>
            @else
                <p style="color:#94a3b8;">Aucun service spécifié.</p>
            @endif
        </div>
        <div class="card">
            <div class="card-title"><span>Image</span></div>
            @if($hotel->image)
                <img src="{{ str_starts_with($hotel->image, 'http') ? $hotel->image : asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 0.5rem;">
            @else
                <div style="width: 100%; height: 200px; background: rgba(148, 163, 184, .08); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                    Aucune image
                </div>
            @endif
        </div>
    </div>

    {{-- Localisation Google Maps --}}
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
    <div class="card" style="margin-bottom:1.5rem">
        <div class="card-title" style="margin-bottom:1rem">
            <span><i class="fa-solid fa-location-dot" style="color:var(--ci-orange)"></i> Localisation</span>
            <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer"
               class="btn-secondary" style="font-size:.8rem;padding:.4rem .8rem">
                <i class="fa-solid fa-up-right-from-square"></i> Ouvrir dans Maps
            </a>
        </div>
        <div style="border-radius:10px;overflow:hidden;border:1px solid var(--border)">
            <iframe src="{{ $mapSrc }}"
                width="100%" height="300" frameborder="0" style="border:0;display:block"
                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                title="Localisation {{ $hotel->name }}">
            </iframe>
        </div>
        <div style="margin-top:.75rem;display:flex;align-items:center;gap:.5rem;color:#94a3b8;font-size:.85rem">
            <i class="fa-solid fa-location-dot" style="color:var(--ci-orange)"></i>
            {{ $hotel->quartier }}, {{ $hotel->city }}
            @if($hotel->latitude && $hotel->longitude)
                <span style="margin-left:.5rem;font-family:monospace;font-size:.75rem;opacity:.6">
                    {{ $hotel->latitude }}, {{ $hotel->longitude }}
                </span>
            @endif
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 1fr 1fr; gap:1.25rem; margin-bottom:1.5rem;">
        <div class="card">
            <div class="card-title"><span>Chambres</span></div>
            <div class="grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); gap:1rem;">
                @foreach($rooms->take(6) as $room)
                    <div class="card-sm" style="background: rgba(148, 163, 184, .08);">
                        <div style="font-weight:700;">{{ $room->number }}</div>
                        <div style="color:#94a3b8; margin:.5rem 0;">{{ $room->type }}</div>
                        <div class="badge badge-secondary">{{ $room->status }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-title"><span>Réservations à venir</span></div>
            <div class="meta-list">
                @forelse($reservations as $reservation)
                    <div class="meta-line"><span>{{ $reservation->code }}</span><strong>{{ $reservation->checkin->format('d/m') }} ➜ {{ $reservation->checkout->format('d/m') }}</strong></div>
                @empty
                    <p>Aucune réservation à venir.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-title"><span>{{ __('Réserver une chambre') }}</span></div>

        @if(session('error'))
            <div style="background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.3);border-radius:10px;padding:1rem;margin-bottom:1rem;color:#fca5a5">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.3);border-radius:10px;padding:1rem;margin-bottom:1rem;color:#a7f3d0">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @auth
        @php
        $bookedRooms = \App\Models\Reservation::whereIn('room_id', $rooms->pluck('id'))
            ->whereNotIn('status', ['annulée', 'cancelled', 'refusée'])
            ->whereDate('checkout', '>=', now())
            ->select('room_id', 'checkin', 'checkout')
            ->get();
        @endphp

        <form method="POST" action="{{ auth()->user()->role === 'client' ? route('client.reservations.store') : route('reservations.store') }}">
            @csrf
            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:1rem">

                {{-- 1. DATES EN PREMIER --}}
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-day" style="color:var(--ci-orange)"></i> {{ __("Date d'arrivée") }}</label>
                    <input type="date" name="checkin" id="checkin"
                           value="{{ old('checkin', now()->toDateString()) }}"
                           min="{{ now()->toDateString() }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-check" style="color:var(--ci-green)"></i> {{ __('Date de départ') }}</label>
                    <input type="date" name="checkout" id="checkout"
                           value="{{ old('checkout', now()->addDays(1)->toDateString()) }}"
                           min="{{ now()->addDay()->toDateString() }}" required>
                </div>

                {{-- 2. CHAMBRE avec indicateur de disponibilité --}}
                <div class="form-group" style="grid-column:1/-1;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                        <label style="margin:0;"><i class="fa-solid fa-bed" style="color:var(--ci-orange)"></i> {{ __('Chambre') }}</label>
                        <span id="avail-badge" style="font-size:.78rem;font-weight:700;display:none;padding:.25rem .75rem;border-radius:999px;"></span>
                    </div>
                    <select name="room_id" id="room_id" required class="form-select">
                        <option value="">{{ __('Sélectionnez une chambre') }}</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}"
                                    data-price="{{ $room->price }}"
                                    data-label="{{ $room->number }} — {{ $room->type }} — {{ number_format($room->price,0,',',' ') }} FCFA"
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->number }} — {{ $room->type }} — {{ number_format($room->price,0,',',' ') }} FCFA ({{ $room->status }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. CLIENT (admin/prestataire uniquement) --}}
                @if(auth()->user()->role !== 'client')
                <div class="form-group" style="grid-column:1/-1;">
                    <label><i class="fa-solid fa-user" style="color:var(--ci-orange)"></i> {{ __('Client') }}</label>
                    <select name="client_id" required class="form-select">
                        <option value="">{{ __('Sélectionnez un client') }}</option>
                        @foreach(\App\Models\Client::orderBy('first_name')->get() as $c)
                            <option value="{{ $c->id }}">{{ $c->first_name }} {{ $c->last_name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- 4. MÉTHODE DE PAIEMENT --}}
                <div class="form-group" style="grid-column:1/-1;">
                    <label style="margin-bottom:.75rem;display:block;"><i class="fa-solid fa-wallet" style="color:var(--ci-orange)"></i> {{ __('Méthode de paiement') }}</label>
                    <input type="hidden" name="payment_method" id="pm_value" value="{{ old('payment_method', 'Orange Money') }}" required>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.6rem;">
                        @php
                        $payMethods = [
                            ['value'=>'Orange Money',    'label'=>'Orange Money',     'sub'=>'Paiement mobile sécurisé',   'icon'=>'🟠','bg'=>'#ff6600'],
                            ['value'=>'MTN Mobile Money','label'=>'MTN Mobile Money', 'sub'=>'Paiement mobile instantané', 'icon'=>'🟡','bg'=>'#ffcc00'],
                            ['value'=>'Wave',            'label'=>'Wave',             'sub'=>'Transfert rapide 0 frais',   'icon'=>'🔵','bg'=>'#1a9fff'],
                            ['value'=>'Carte bancaire',  'label'=>'Carte bancaire',   'sub'=>'Visa, Mastercard, AMEX',     'icon'=>'💳','bg'=>'#1e3a5f'],
                            ['value'=>'Espèces',         'label'=>'Espèces',          'sub'=>'Payer à la réception',       'icon'=>'💵','bg'=>'#15803d'],
                            ['value'=>'Virement',        'label'=>'Virement bancaire','sub'=>'IBAN, SWIFT',                'icon'=>'🏦','bg'=>'#4f46e5'],
                        ];
                        $selectedPm = old('payment_method', 'Orange Money');
                        @endphp
                        @foreach($payMethods as $pm)
                        <div class="pm-card {{ $selectedPm === $pm['value'] ? 'pm-active' : '' }}"
                             data-value="{{ $pm['value'] }}"
                             onclick="selectPm(this)"
                             style="border:2px solid {{ $selectedPm === $pm['value'] ? 'var(--ci-orange)' : 'var(--border)' }};
                                    background:{{ $selectedPm === $pm['value'] ? 'var(--ci-orange-dim)' : 'var(--bg-input)' }};
                                    border-radius:12px;padding:.7rem .9rem;cursor:pointer;
                                    display:flex;align-items:center;gap:.6rem;transition:all .2s;">
                            <span style="font-size:1.4rem;line-height:1;">{{ $pm['icon'] }}</span>
                            <div style="min-width:0;">
                                <div style="font-weight:700;font-size:.82rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pm['label'] }}</div>
                                <div style="font-size:.72rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pm['sub'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 5. NATIONALITÉ --}}
                <div class="form-group">
                    <label><i class="fa-solid fa-flag" style="color:var(--ci-orange)"></i> {{ __('Nationalité') }}</label>
                    <input type="text" name="nationality" value="{{ old('nationality', 'Côte d\'Ivoire') }}" required>
                </div>
            </div>

            {{-- Données réservations pour vérif JS --}}
            @php
            $bookedRoomsJs = $bookedRooms->map(function($r) {
                return [
                    'room_id'  => $r->room_id,
                    'checkin'  => $r->checkin->format('Y-m-d'),
                    'checkout' => $r->checkout->format('Y-m-d'),
                ];
            });
            @endphp
            <script>
            const BOOKED_ROOMS = @json($bookedRoomsJs);
            </script>

            <input type="hidden" name="channel" value="Site web">

            <div id="price-preview" style="padding:1rem;background:rgba(0,154,68,.08);border-radius:10px;margin-bottom:1rem;display:none">
                <div style="font-size:.85rem;color:#94a3b8">Estimation du coût</div>
                <div id="price-total" style="font-size:1.5rem;font-weight:700;color:var(--ci-green)"></div>
            </div>

            <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;margin-top:1rem">
                <button type="submit" class="btn" style="min-width:160px">
                    <i class="fa-solid fa-calendar-check"></i> Réserver maintenant
                </button>
                <a href="{{ route('hotels.index') }}" class="btn-secondary">Retour</a>
            </div>
        </form>
        @else
        <div style="text-align:center;padding:2rem">
            <p style="color:#94a3b8;margin-bottom:1rem">Connectez-vous pour effectuer une réservation.</p>
            <a href="{{ route('login') }}" class="btn"><i class="fa-solid fa-sign-in-alt"></i> Se connecter</a>
            <a href="{{ route('register') }}" class="btn-secondary" style="margin-left:1rem">Créer un compte</a>
        </div>
        @endauth
    </div>

    <!-- AVIS ET ÉVALUATIONS -->
    <div class="card">
        <div class="card-title" style="margin-bottom:1.25rem">
            <span><i class="fa-solid fa-star" style="color:#f4a423"></i> Avis des clients ({{ $reviews->count() }})</span>
            <div style="display:flex;align-items:center;gap:6px;font-size:1.1rem;font-weight:700">
                <i class="fa-solid fa-star" style="color:#f4a423"></i>
                {{ number_format($hotel->rating, 1) }} / 5
            </div>
        </div>

        @if(session('success') && str_contains(session('success'), 'avis'))
            <div style="background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.3);border-radius:10px;padding:1rem;margin-bottom:1rem;color:#a7f3d0">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Formulaire d'avis (clients uniquement) -->
        @auth
            @if(auth()->user()->role === 'client')
            <div class="card" style="background:rgba(247,127,0,.05);border-color:var(--ci-orange-border);margin-bottom:1.5rem">
                <div style="font-size:.9rem;font-weight:700;margin-bottom:1rem;color:var(--ci-orange)">
                    <i class="fa-solid fa-pen-to-square"></i>
                    {{ $userReview ? 'Modifier votre avis' : 'Laisser un avis' }}
                </div>
                <form method="POST" action="{{ route('reviews.store', $hotel) }}">
                    @csrf
                    <div class="form-group">
                        <label>Note</label>
                        <div style="display:flex;gap:8px;margin-top:4px" id="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                            <label style="cursor:pointer;font-size:1.6rem;color:{{ $userReview && $userReview->rating >= $i ? '#f4a423' : '#94a3b8' }}">
                                <input type="radio" name="rating" value="{{ $i }}" style="display:none" {{ $userReview && $userReview->rating == $i ? 'checked' : '' }}>
                                ★
                            </label>
                            @endfor
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Titre (optionnel)</label>
                        <input type="text" name="title" placeholder="Ex: Séjour excellent" maxlength="200"
                               value="{{ old('title', $userReview?->title) }}">
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea name="comment" rows="3" placeholder="Partagez votre expérience..." required minlength="10" maxlength="2000">{{ old('comment', $userReview?->comment) }}</textarea>
                    </div>
                    <button type="submit" class="btn">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ $userReview ? 'Mettre à jour' : 'Publier mon avis' }}
                    </button>
                </form>
            </div>
            @endif
        @endauth

        <!-- Liste des avis -->
        @forelse($reviews as $review)
        <div style="padding:1rem 0;border-bottom:1px solid rgba(148,163,184,.1)">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem">
                <div style="display:flex;align-items:center;gap:.75rem;flex:1">
                    <div style="width:38px;height:38px;border-radius:50%;background:var(--ci-orange);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;flex-shrink:0">
                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="flex:1">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                            <strong style="font-size:.9rem">{{ $review->user->name ?? 'Utilisateur' }}</strong>
                            <div style="display:flex;gap:2px">
                                @for($i=1;$i<=5;$i++)
                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star" style="font-size:11px;color:#f4a423"></i>
                                @endfor
                            </div>
                            <span style="font-size:.75rem;color:#94a3b8">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        @if($review->title)
                            <div style="font-weight:600;font-size:.88rem;margin-bottom:4px">{{ $review->title }}</div>
                        @endif
                        <p style="font-size:.85rem;color:#94a3b8;line-height:1.6;margin:0">{{ $review->comment }}</p>
                    </div>
                </div>
                @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->role === 'admin'))
                <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer cet avis ?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#e63946;font-size:.8rem" title="Supprimer">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:#94a3b8">
            <i class="fa-solid fa-comment-slash" style="font-size:2rem;opacity:.4;margin-bottom:.5rem"></i>
            <p>Aucun avis pour cet hôtel. Soyez le premier à donner votre avis !</p>
        </div>
        @endforelse
    </div>

<script>
// Sélection interactive des étoiles
document.querySelectorAll('#star-rating label').forEach((star, idx, stars) => {
    star.addEventListener('mouseover', () => {
        stars.forEach((s, i) => s.style.color = i <= idx ? '#f4a423' : '#94a3b8');
    });
    star.addEventListener('mouseout', () => {
        const checked = document.querySelector('#star-rating input:checked');
        const checkedVal = checked ? parseInt(checked.value) : 0;
        stars.forEach((s, i) => s.style.color = i < checkedVal ? '#f4a423' : '#94a3b8');
    });
    star.addEventListener('click', () => {
        stars.forEach((s, i) => s.style.color = i <= idx ? '#f4a423' : '#94a3b8');
    });
});

const checkinInput  = document.getElementById('checkin');
const checkoutInput = document.getElementById('checkout');
const roomSelect    = document.getElementById('room_id');

// ── Vérification de disponibilité ──────────────────────────
function checkAvailability() {
    if (!checkinInput || !checkoutInput || !roomSelect) return;
    const checkin  = checkinInput.value;
    const checkout = checkoutInput.value;
    if (!checkin || !checkout || checkin >= checkout) return;

    const dIn  = new Date(checkin);
    const dOut = new Date(checkout);

    // Rooms occupées pour ces dates
    const occupiedIds = BOOKED_ROOMS
        .filter(r => new Date(r.checkin) < dOut && new Date(r.checkout) > dIn)
        .map(r => r.room_id);

    let availCount = 0;
    roomSelect.querySelectorAll('option').forEach(opt => {
        if (!opt.value) return;
        const id = parseInt(opt.value);
        if (occupiedIds.includes(id)) {
            opt.disabled = true;
            // Ajouter "(Occupée)" si pas déjà là
            if (!opt.dataset.orig) opt.dataset.orig = opt.text;
            const baseLabel = opt.dataset.orig.replace(/\s*—?\s*\(Occupée\)$/i, '').replace(/\s*\(disponible\)$/i, '');
            opt.text = baseLabel + ' — ({{ __("Occupée") }})';
            // Désélectionner si occupée
            if (opt.selected) { opt.selected = false; roomSelect.value = ''; }
        } else {
            opt.disabled = false;
            if (opt.dataset.orig) opt.text = opt.dataset.orig;
            availCount++;
        }
    });

    // Badge disponibilité
    const badge = document.getElementById('avail-badge');
    if (badge) {
        badge.style.display = 'inline-flex';
        if (availCount === 0) {
            badge.textContent = '{{ __("Aucune chambre disponible") }}';
            badge.style.background = 'rgba(230,57,70,.15)';
            badge.style.color = '#fca5a5';
            badge.style.border = '1px solid rgba(230,57,70,.3)';
        } else {
            badge.textContent = availCount + ' {{ __("chambre(s) disponible(s)") }}';
            badge.style.background = 'rgba(0,154,68,.12)';
            badge.style.color = '#a7f3d0';
            badge.style.border = '1px solid rgba(0,154,68,.25)';
        }
    }

    updatePrice();
}

// ── Prévisualisation du prix ────────────────────────────────
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

// Lancer au chargement si les dates sont déjà renseignées
if (checkinInput?.value && checkoutInput?.value) checkAvailability();

function selectPm(el) {
    document.querySelectorAll('.pm-card').forEach(c => {
        c.classList.remove('pm-active');
        c.style.borderColor = 'var(--border)';
        c.style.background  = 'var(--bg-input)';
    });
    el.classList.add('pm-active');
    el.style.borderColor = 'var(--ci-orange)';
    el.style.background  = 'var(--ci-orange-dim)';
    document.getElementById('pm_value').value = el.dataset.value;
}
</script>
@push('styles')
<style>
/* Cartes méthode de paiement */
.pm-card:hover {
    border-color: var(--ci-orange) !important;
    background: var(--ci-orange-dim) !important;
}
.pm-card.pm-active {
    border-color: var(--ci-orange) !important;
    background: var(--ci-orange-dim) !important;
}

@media (max-width: 480px) {
    div[style*="grid-template-columns:repeat(2,1fr)"],
    div[style*="grid-template-columns: repeat(2, minmax(0, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush

@endsection
