@extends('layouts.app')
@section('title', $hotel ? __('Modifier').' '.$hotel->name : __('Ajouter un hôtel'))

@section('content')
<div style="max-width:900px;margin:0 auto;padding:40px 28px;">

    <a href="{{ route('hotels.index') }}" style="display:inline-flex;align-items:center;gap:8px;color:var(--text-muted);font-size:14px;margin-bottom:24px;">
        ← {{ __('Retour à mes hôtels') }}
    </a>

    <div style="margin-bottom:36px;">
        <h1 style="font-size:32px;color:var(--dark);">{{ $hotel ? __('Modifier').' '.$hotel->name : __('Ajouter un hôtel') }}</h1>
        <p style="color:var(--text-muted);font-size:15px;margin-top:6px;">
            {{ $hotel ? __('Mettez à jour les informations de votre établissement.') : __('Remplissez les informations pour soumettre votre établissement à notre équipe de validation.') }}
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-error mb-3">
            <strong>{{ __('Veuillez corriger les erreurs suivantes :') }}</strong>
            <ul style="margin-top:8px;padding-left:18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $hotel ? route('owner.hotels.update', $hotel) : route('owner.hotels.store') }}" enctype="multipart/form-data">
        @csrf
        @if($hotel) @method('PUT') @endif

        {{-- ══ INFOS GÉNÉRALES ══ --}}
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">📋 {{ __('Informations générales') }}</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">{{ __("Nom de l'hôtel *") }}</label>
                    <input type="text" name="name" class="form-control" placeholder="{{ __('ex: Hôtel Palm Beach Abidjan') }}" value="{{ old('name', $hotel?->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Description *') }}</label>
                    <textarea name="description" class="form-control" placeholder="{{ __('Décrivez votre hôtel, son ambiance, ses points forts...') }}" required style="min-height:130px;">{{ old('description', $hotel?->description) }}</textarea>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('Ville *') }}</label>
                        <select name="city" class="form-control" required>
                            <option value="">{{ __('Sélectionner une ville') }}</option>
                            @foreach($cities as $c)
                                <option value="{{ $c }}" {{ old('city', $hotel?->city) == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Classement (étoiles) *') }}</label>
                        <div style="display:flex;gap:8px;" id="star-container">
                            @for($i=1;$i<=5;$i++)
                            <button type="button" onclick="setStar({{ $i }})" id="star-{{ $i }}"
                                style="width:40px;height:40px;border-radius:8px;border:1.5px solid var(--border);background:{{ old('stars',$hotel?->stars ?? 0) >= $i ? 'var(--gold)' : 'white' }};cursor:pointer;font-size:18px;transition:all .2s;">★</button>
                            @endfor
                        </div>
                        <input type="hidden" name="stars" id="stars-input" value="{{ old('stars', $hotel?->stars ?? 3) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Adresse complète *') }}</label>
                    <input type="text" name="address" class="form-control" placeholder="{{ __('ex: Rue du Commerce, Plateau, Abidjan') }}" value="{{ old('address', $hotel?->address) }}" required>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('Latitude (GPS)') }}</label>
                        <input type="number" name="latitude" step="0.000001" class="form-control" placeholder="5.3600" value="{{ old('latitude', $hotel?->latitude) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Longitude (GPS)') }}</label>
                        <input type="number" name="longitude" step="0.000001" class="form-control" placeholder="-4.0083" value="{{ old('longitude', $hotel?->longitude) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Photo de couverture') }}</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                    @if($hotel?->cover_image)
                        <img src="{{ $hotel->cover_url }}" alt="{{ __('Couverture actuelle') }}" style="height:80px;border-radius:8px;margin-top:10px;object-fit:cover;">
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __("Galerie d'images (plusieurs fichiers)") }}</label>
                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    @if($hotel && $hotel->images->isNotEmpty())
                        <div style="display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;">
                            @foreach($hotel->images as $img)
                                <div style="position:relative;width:90px;height:70px;border-radius:8px;overflow:hidden;border:1px solid var(--border);">
                                    <img src="{{ $img->url }}" alt="{{ __('Image') }} {{ $loop->iteration }}" style="width:100%;height:100%;object-fit:cover;">
                                    <form method="POST" action="{{ route('owner.hotels.images.destroy', ['hotel' => $hotel, 'image' => $img]) }}" style="position:absolute;top:4px;right:4px;margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:rgba(0,0,0,.55);border:none;border-radius:50%;width:24px;height:24px;color:white;font-size:12px;line-height:1;cursor:pointer;">✕</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══ CONTACT ══ --}}
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">📞 {{ __('Contact & Informations pratiques') }}</h3></div>
            <div class="card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('Téléphone *') }}</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+225 07 XX XX XX XX" value="{{ old('phone', $hotel?->phone) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="contact@monhotel.ci" value="{{ old('email', $hotel?->email) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Site web') }}</label>
                        <input type="url" name="website" class="form-control" placeholder="https://www.monhotel.ci" value="{{ old('website', $hotel?->website) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="tel" name="whatsapp" class="form-control" placeholder="+225 07 XX XX XX XX" value="{{ old('whatsapp', $hotel?->whatsapp) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __("Check-in (heure d'arrivée) *") }}</label>
                        <input type="time" name="check_in_time" class="form-control" value="{{ old('check_in_time', $hotel?->check_in_time ?? '14:00') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Check-out (heure de départ) *') }}</label>
                        <input type="time" name="check_out_time" class="form-control" value="{{ old('check_out_time', $hotel?->check_out_time ?? '12:00') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ ÉQUIPEMENTS ══ --}}
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">✅ {{ __('Équipements & Services') }}</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-wrap:wrap;gap:12px;">
                    @foreach($amenities as $key => $label)
                    <label class="form-check">
                        <input type="checkbox" name="amenities[]" value="{{ $key }}"
                            {{ in_array($key, old('amenities', $hotel?->amenities ?? [])) ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ CHAMBRES ══ --}}
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">🛏️ {{ __('Chambres') }}</h3>
                <button type="button" onclick="addRoom()" class="btn btn-outline btn-sm">+ {{ __('Ajouter') }}</button>
            </div>
            <div class="card-body" id="rooms-container">
                @if($hotel && $hotel->rooms->count())
                    @foreach($hotel->rooms as $i => $room)
                    <div class="room-block" style="background:var(--sand);border-radius:14px;padding:20px;margin-bottom:16px;">
                        <input type="hidden" name="rooms[{{ $i }}][id]" value="{{ $room->id }}">
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nom de la chambre *') }}</label>
                                <input type="text" name="rooms[{{ $i }}][name]" class="form-control" value="{{ $room->name }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Type *') }}</label>
                                <select name="rooms[{{ $i }}][type]" class="form-control">
                                    @foreach($roomTypes as $k=>$v)
                                        <option value="{{ $k }}" {{ $room->type === $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Prix / nuit (FCFA) *') }}</label>
                                <input type="number" name="rooms[{{ $i }}][price_per_night]" class="form-control" value="{{ $room->price_per_night }}" min="1000" step="500" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Capacité (personnes)') }}</label>
                                <input type="number" name="rooms[{{ $i }}][capacity]" class="form-control" value="{{ $room->capacity }}" min="1" max="10">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Nombre de lits') }}</label>
                                <input type="number" name="rooms[{{ $i }}][beds]" class="form-control" value="{{ $room->beds }}" min="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Superficie (m²)') }}</label>
                                <input type="number" name="rooms[{{ $i }}][area_m2]" class="form-control" value="{{ $room->area_m2 }}" step="0.5">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Nombre de chambres') }}</label>
                                <input type="number" name="rooms[{{ $i }}][quantity]" class="form-control" value="{{ $room->quantity }}" min="1">
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div id="no-rooms" style="text-align:center;padding:24px;color:var(--text-muted);">
                        {{ __('Aucune chambre. Cliquez sur "+ Ajouter" pour en créer une.') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ POLITIQUE ══ --}}
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">📜 {{ __('Politiques') }}</h3></div>
            <div class="card-body">
                <div class="grid-2">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">{{ __("Politique d'annulation *") }}</label>
                        <select name="cancellation_policy" class="form-control" required>
                            @foreach(['Annulation gratuite jusqu\'à 24h avant l\'arrivée','Annulation gratuite jusqu\'à 48h avant l\'arrivée','Annulation gratuite jusqu\'à 7 jours avant l\'arrivée','Non remboursable'] as $policy)
                                <option {{ old('cancellation_policy',$hotel?->cancellation_policy) === $policy ? 'selected' : '' }}>{{ __($policy) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Animaux acceptés') }}</label>
                        <select name="pets_allowed" class="form-control">
                            <option value="0" {{ !old('pets_allowed',$hotel?->pets_allowed) ? 'selected' : '' }}>{{ __('Non') }}</option>
                            <option value="1" {{ old('pets_allowed',$hotel?->pets_allowed) ? 'selected' : '' }}>{{ __('Oui') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Fumeurs acceptés') }}</label>
                        <select name="smoking_allowed" class="form-control">
                            <option value="0" {{ !old('smoking_allowed',$hotel?->smoking_allowed) ? 'selected' : '' }}>{{ __('Non') }}</option>
                            <option value="1" {{ old('smoking_allowed',$hotel?->smoking_allowed) ? 'selected' : '' }}>{{ __('Zone fumeurs') }}</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">{{ __('Informations supplémentaires') }}</label>
                        <textarea name="extra_info" class="form-control" placeholder="{{ __('Règlements, services spéciaux...') }}">{{ old('extra_info', $hotel?->extra_info) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg">
                {{ $hotel ? '💾 '.__('Enregistrer les modifications') : '📤 '.__('Soumettre pour validation') }}
            </button>
            <a href="{{ route('owner.hotels.index') }}" class="btn btn-outline btn-lg">{{ __('Annuler') }}</a>
        </div>
        @unless($hotel)
        <p style="font-size:13px;color:var(--text-muted);margin-top:16px;">
            {{ __('Notre équipe examinera votre hôtel sous 24–48h avant de le publier sur la plateforme.') }}
        </p>
        @endunless
    </form>
</div>

@push('scripts')
<script>
let roomIndex = {{ $hotel ? $hotel->rooms->count() : 0 }};

function setStar(n) {
    document.getElementById('stars-input').value = n;
    for (let i = 1; i <= 5; i++) {
        const btn = document.getElementById('star-' + i);
        btn.style.background = i <= n ? 'var(--gold)' : 'white';
        btn.style.borderColor = i <= n ? 'var(--gold)' : 'var(--border)';
    }
}

function addRoom() {
    document.getElementById('no-rooms')?.remove();
    const types = @json($roomTypes);
    const options = Object.entries(types).map(([k,v]) => `<option value="${k}">${v}</option>`).join('');
    const i = roomIndex++;
    const html = `
    <div class="room-block" style="background:var(--sand);border-radius:14px;padding:20px;margin-bottom:16px;position:relative;">
        <button type="button" onclick="this.closest('.room-block').remove()" style="position:absolute;top:12px;right:12px;background:none;border:none;cursor:pointer;font-size:18px;color:var(--text-muted);">✕</button>
        <div class="grid-2">
            <div class="form-group"><label class="form-label">{{ __('Nom *') }}</label>
                <input type="text" name="rooms[${i}][name]" class="form-control" placeholder="{{ __('ex: Chambre Deluxe') }}" required></div>
            <div class="form-group"><label class="form-label">{{ __('Type *') }}</label>
                <select name="rooms[${i}][type]" class="form-control">${options}</select></div>
            <div class="form-group"><label class="form-label">{{ __('Prix / nuit (FCFA) *') }}</label>
                <input type="number" name="rooms[${i}][price_per_night]" class="form-control" placeholder="75000" min="1000" step="500" required></div>
            <div class="form-group"><label class="form-label">{{ __('Capacité') }}</label>
                <input type="number" name="rooms[${i}][capacity]" class="form-control" value="2" min="1" max="10"></div>
            <div class="form-group"><label class="form-label">{{ __('Nombre de lits') }}</label>
                <input type="number" name="rooms[${i}][beds]" class="form-control" value="1" min="1"></div>
            <div class="form-group"><label class="form-label">{{ __('Superficie (m²)') }}</label>
                <input type="number" name="rooms[${i}][area_m2]" class="form-control" placeholder="28" step="0.5"></div>
            <div class="form-group"><label class="form-label">{{ __('Nombre de chambres') }}</label>
                <input type="number" name="rooms[${i}][quantity]" class="form-control" value="1" min="1"></div>
        </div>
    </div>`;
    document.getElementById('rooms-container').insertAdjacentHTML('beforeend', html);
}

// Init stars
setStar(parseInt(document.getElementById('stars-input').value) || 3);
</script>
@endpush
@endsection
