@extends('layouts.app')

@section('content')
    <style>
        select option {
            color: #e2e8f0;
            background: rgba(71, 85, 105, .14);
        }
    </style>
    <div class="card card-title">
        <div class="card-title">
            <div>
                <div style="font-size:1.25rem;font-weight:700;">{{ __("Modifier l'hôtel") }}</div>
                <p style="margin: .5rem 0 0; color:#94a3b8;">{{ __("Modifiez les informations de l'hôtel.") }}</p>
            </div>
            <a href="{{ route('hotels.show', $hotel) }}" class="btn">{{ __("Voir l'hôtel") }}</a>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-title"><span>{{ __("Modifier l'hôtel") }}</span></div>
        <form method="POST" action="{{ route('hotels.update', $hotel) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid">
                <div class="form-group"><label>{{ __('Nom complet') }}</label><input type="text" name="name" value="{{ old('name', $hotel->name) }}" required></div>
                <div class="form-group"><label>{{ __('Nom court') }}</label><input type="text" name="short_name" value="{{ old('short_name', $hotel->short_name) }}" required></div>
                <div class="form-group"><label>{{ __('Ville') }}</label><input type="text" name="city" value="{{ old('city', $hotel->city) }}" required></div>
                <div class="form-group"><label>{{ __('Quartier') }}</label><input type="text" name="quartier" value="{{ old('quartier', $hotel->quartier) }}" required></div>
                {{-- Sélecteur de localisation interactif --}}
                <div class="form-group" style="grid-column:1/-1">
                    <label style="display:flex;align-items:center;gap:.5rem">
                        <i class="fa-solid fa-location-dot" style="color:var(--ci-orange)"></i>
                        {{ __('Localisation sur la carte') }}
                        <span style="font-size:.75rem;color:#94a3b8;font-weight:400">{{ __('(cliquez sur la carte pour placer le marqueur)') }}</span>
                    </label>

                    {{-- Barre de recherche --}}
                    <div style="display:flex;gap:.5rem;margin-bottom:.6rem">
                        <input type="text" id="map-search" placeholder="{{ __('Rechercher une adresse... ex: Hôtel Ivoire, Abidjan') }}"
                               style="flex:1" autocomplete="off">
                        <button type="button" onclick="searchAddress()"
                                style="white-space:nowrap;padding:.5rem 1rem;background:var(--ci-orange);color:white;border:none;border-radius:8px;cursor:pointer;font-size:.85rem">
                            <i class="fa-solid fa-magnifying-glass"></i> {{ __('Chercher') }}
                        </button>
                    </div>

                    {{-- Carte Leaflet --}}
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                    <div id="location-map" style="width:100%;height:320px;border-radius:10px;border:2px solid var(--border);overflow:hidden;cursor:crosshair"></div>

                    {{-- Coordonnées affichées --}}
                    <div style="display:flex;gap:1rem;margin-top:.6rem;align-items:center;flex-wrap:wrap">
                        <div style="flex:1;min-width:160px">
                            <label style="font-size:.75rem;color:#94a3b8;margin-bottom:.25rem;display:block">{{ __('Latitude') }}</label>
                            <input type="number" step="0.0000001" name="latitude" id="lat-input"
                                   value="{{ old('latitude', $hotel->latitude) }}"
                                   placeholder="{{ __('cliquez sur la carte') }}"
                                   style="font-family:monospace;font-size:.85rem;width:100%">
                        </div>
                        <div style="flex:1;min-width:160px">
                            <label style="font-size:.75rem;color:#94a3b8;margin-bottom:.25rem;display:block">{{ __('Longitude') }}</label>
                            <input type="number" step="0.0000001" name="longitude" id="lng-input"
                                   value="{{ old('longitude', $hotel->longitude) }}"
                                   placeholder="{{ __('cliquez sur la carte') }}"
                                   style="font-family:monospace;font-size:.85rem;width:100%">
                        </div>
                        <button type="button" onclick="clearLocation()"
                                style="padding:.5rem .9rem;background:rgba(230,57,70,.12);color:#fca5a5;border:1px solid rgba(230,57,70,.3);border-radius:8px;cursor:pointer;font-size:.8rem;white-space:nowrap;margin-top:1.1rem">
                            <i class="fa-solid fa-trash"></i> {{ __('Effacer') }}
                        </button>
                    </div>
                    <p style="font-size:.76rem;color:#64748b;margin-top:.4rem">
                        <i class="fa-solid fa-circle-info"></i>
                        {{ __('Faites glisser le marqueur ou cliquez ailleurs pour le repositionner. Laissez vide pour utiliser la recherche par nom de ville.') }}
                    </p>
                </div>
                <div class="form-group"><label>{{ __('Étoiles') }}</label><select name="stars" required class="form-select"><option value="3" {{ old('stars', $hotel->stars) == 3 ? 'selected' : '' }}>{{ __('3 étoiles') }}</option><option value="4" {{ old('stars', $hotel->stars) == 4 ? 'selected' : '' }}>{{ __('4 étoiles') }}</option><option value="5" {{ old('stars', $hotel->stars) == 5 ? 'selected' : '' }}>{{ __('5 étoiles') }}</option></select></div>
                <div class="form-group"><label>{{ __('Nombre de chambres') }}</label><input type="number" name="rooms" value="{{ old('rooms', $hotel->rooms) }}" required></div>
                <div class="form-group"><label>{{ __('Catégorie') }}</label><select name="category" required class="form-select"><option value="Luxe" {{ old('category', $hotel->category) == 'Luxe' ? 'selected' : '' }}>{{ __('Luxe') }}</option><option value="Plage" {{ old('category', $hotel->category) == 'Plage' ? 'selected' : '' }}>{{ __('Plage') }}</option><option value="Affaires" {{ old('category', $hotel->category) == 'Affaires' ? 'selected' : '' }}>{{ __('Affaires') }}</option><option value="Standard" {{ old('category', $hotel->category) == 'Standard' ? 'selected' : '' }}>{{ __('Standard') }}</option></select></div>
                <div class="form-group"><label>{{ __('Statut') }}</label><select name="status" required class="form-select"><option value="Ouvert" {{ old('status', $hotel->status) == 'Ouvert' ? 'selected' : '' }}>{{ __('Ouvert') }}</option><option value="Fermé" {{ old('status', $hotel->status) == 'Fermé' ? 'selected' : '' }}>{{ __('Fermé') }}</option><option value="En travaux" {{ old('status', $hotel->status) == 'En travaux' ? 'selected' : '' }}>{{ __('En travaux') }}</option></select></div>
                <div class="form-group"><label>{{ __('Note (0-5)') }}</label><input type="number" step="0.1" name="rating" value="{{ old('rating', $hotel->rating) }}" min="0" max="5" required></div>
                <div class="form-group"><label>{{ __('Prix par nuit (FCFA)') }}</label><input type="number" name="price" value="{{ old('price', $hotel->price) }}" required></div>
                <div class="form-group"><label>{{ __('Description') }}</label><textarea name="description" rows="3">{{ old('description', $hotel->description) }}</textarea></div>
                <div class="form-group"><label>{{ __('Services (séparés par des virgules)') }}</label><input type="text" name="services" value="{{ old('services', is_array($hotel->services) ? implode(', ', $hotel->services) : $hotel->services) }}" placeholder="WiFi, Parking, Restaurant, Piscine"></div>
                <div class="form-group"><label>{{ __("Image de l'hôtel") }}</label><input type="file" name="image" accept="image/*">
                @if($hotel->image)
                    <p>{{ __('Image actuelle:') }} <img src="{{ str_starts_with($hotel->image, 'http') ? $hotel->image : asset('storage/' . $hotel->image) }}" alt="{{ __('Image actuelle') }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;"></p>
                @endif
                </div>
                <div class="form-group"><button class="btn" type="submit">{{ __("Mettre à jour l'hôtel") }}</button></div>
            </div>
        </form>
    </div>
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const initLat = parseFloat(document.getElementById('lat-input').value) || 5.3600;
    const initLng = parseFloat(document.getElementById('lng-input').value) || -4.0083;
    const hasCoords = document.getElementById('lat-input').value !== '';

    const map = L.map('location-map', { zoomControl: true }).setView(
        [initLat, initLng],
        hasCoords ? 16 : 7
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    const markerIcon = L.divIcon({
        html: '<div style="font-size:2rem;line-height:1;filter:drop-shadow(0 2px 4px rgba(0,0,0,.4))">📍</div>',
        className: '',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
    });

    let marker = null;

    function placeMarker(lat, lng) {
        lat = parseFloat(lat.toFixed(7));
        lng = parseFloat(lng.toFixed(7));
        document.getElementById('lat-input').value = lat;
        document.getElementById('lng-input').value = lng;
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { icon: markerIcon, draggable: true }).addTo(map);
            marker.on('dragend', function (e) {
                placeMarker(e.target.getLatLng().lat, e.target.getLatLng().lng);
            });
        }
        map.setView([lat, lng], Math.max(map.getZoom(), 15));
    }

    if (hasCoords) {
        placeMarker(initLat, initLng);
    }

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng);
    });

    window.clearLocation = function () {
        document.getElementById('lat-input').value = '';
        document.getElementById('lng-input').value = '';
        if (marker) { map.removeLayer(marker); marker = null; }
    };

    window.searchAddress = function () {
        const q = document.getElementById('map-search').value.trim();
        if (!q) return;
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1&countrycodes=ci', {
            headers: { 'Accept-Language': 'fr' }
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.length > 0) {
                placeMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } else {
                // Fallback sans restriction de pays
                return fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1', {
                    headers: { 'Accept-Language': 'fr' }
                }).then(r => r.json()).then(d => {
                    if (d && d.length > 0) placeMarker(parseFloat(d[0].lat), parseFloat(d[0].lon));
                    else alert('{{ __('Adresse introuvable. Essayez avec plus de détails.') }}');
                });
            }
        })
        .catch(() => alert('{{ __('Erreur de recherche. Vérifiez votre connexion.') }}'));
    };

    document.getElementById('map-search').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); window.searchAddress(); }
    });

    // Sync manuel des champs vers la carte
    ['lat-input', 'lng-input'].forEach(id => {
        document.getElementById(id).addEventListener('change', function () {
            const lat = parseFloat(document.getElementById('lat-input').value);
            const lng = parseFloat(document.getElementById('lng-input').value);
            if (!isNaN(lat) && !isNaN(lng)) placeMarker(lat, lng);
        });
    });
})();
</script>
@endpush
@endsection