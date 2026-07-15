@extends('layouts.app')

@section('content')
    <style>
        select option {
            color: #0b6ff2;
            background: rgba(71, 85, 105, .14);
        }
    </style>

    <div class="card card-title">
        <div class="card-title">
            <div>
                @if(($scope ?? 'mine') === 'tous')
                    <div style="font-size:1.25rem;font-weight:700;">
                        @if(($vue ?? '') === 'mes') {{ __('Mes Hôtels') }}
                        @elseif(($vue ?? '') === 'autres') {{ __('Autres Hôtels') }}
                        @else {{ __('Tous les Hôtels') }}
                        @endif
                    </div>
                    <p style="margin: .5rem 0 0; color:#94a3b8;">{{ __('Explorez tous les hôtels de la plateforme.') }}</p>
                @else
                    <div style="font-size:1.25rem;font-weight:700;">{{ __('Mes Hôtels') }}</div>
                    <p style="margin: .5rem 0 0; color:#94a3b8;">{{ __('Gérez vos propriétés, catégories et indicateurs clés.') }}</p>
                @endif
            </div>
            <a href="{{ route('dashboard') }}" class="btn">{{ __('Retour') }}</a>
        </div>
    </div>

    @if(($scope ?? 'mine') === 'tous')
        {{-- Tabs : Tous | Mes Hôtels | Autres --}}
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
            <div class="grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));flex:1;margin-bottom:0;">
                <a href="{{ route('hotels.index', ['scope' => 'tous']) }}"
                   class="tag {{ !($vue ?? '') ? 'active' : '' }}">{{ __('Tous') }}</a>
                <a href="{{ route('hotels.index', ['scope' => 'tous', 'vue' => 'mes']) }}"
                   class="tag {{ ($vue ?? '') === 'mes' ? 'active' : '' }}">{{ __('Mes Hôtels') }}</a>
                <a href="{{ route('hotels.index', ['scope' => 'tous', 'vue' => 'autres']) }}"
                   class="tag {{ ($vue ?? '') === 'autres' ? 'active' : '' }}">{{ __('Autres Hôtels') }}</a>
            </div>
            @if(auth()->user()->role === 'prestataire')
            <button onclick="document.getElementById('addHotelForm').classList.toggle('hidden')"
                    class="btn" style="white-space:nowrap;flex-shrink:0;">
                <i class="fa-solid fa-plus"></i> {{ __('Ajouter un hôtel') }}
            </button>
            @endif
        </div>

        @if(auth()->user()->role === 'prestataire')
        <div id="addHotelForm" class="card hidden" style="margin-bottom:1.5rem;">
            <div class="card-title"><span>{{ __('Ajouter un hôtel') }}</span></div>
            <form method="POST" action="{{ route('hotels.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid">
                    <div class="form-group"><label>{{ __('Nom complet') }}</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>{{ __('Nom court') }}</label><input type="text" name="short_name" required></div>
                    <div class="form-group"><label>{{ __('Ville') }}</label><input type="text" name="city" required></div>
                    <div class="form-group"><label>{{ __('Quartier') }}</label><input type="text" name="quartier" required></div>
                    <div class="form-group"><label>{{ __('Étoiles') }}</label><select name="stars" required class="form-select"><option value="3">{{ __('3 étoiles') }}</option><option value="4">{{ __('4 étoiles') }}</option><option value="5">{{ __('5 étoiles') }}</option></select></div>
                    <div class="form-group"><label>{{ __('Nombre de chambres') }}</label><input type="number" name="rooms" value="12" required></div>
                    <div class="form-group"><label>{{ __('Catégorie') }}</label><select name="category" required class="form-select"><option value="Luxe">{{ __('Luxe') }}</option><option value="Plage">{{ __('Plage') }}</option><option value="Affaires">{{ __('Affaires') }}</option><option value="Standard">{{ __('Standard') }}</option></select></div>
                    <div class="form-group"><label>{{ __('Statut') }}</label><select name="status" required class="form-select"><option value="Ouvert">{{ __('Ouvert') }}</option><option value="Fermé">{{ __('Fermé') }}</option><option value="En travaux">{{ __('En travaux') }}</option></select></div>
                    <div class="form-group"><label>{{ __('Note (0-5)') }}</label><input type="number" step="0.1" name="rating" value="4.5" min="0" max="5" required></div>
                    <div class="form-group"><label>{{ __('Prix par nuit (FCFA)') }}</label><input type="number" name="price" value="210000" required></div>
                    <div class="form-group"><label>{{ __('Description') }}</label><textarea name="description" rows="3"></textarea></div>
                    <div class="form-group"><label>{{ __('Services (séparés par des virgules)') }}</label><input type="text" name="services" placeholder="WiFi, Parking, Restaurant, Piscine"></div>
                    <div class="form-group"><label>{{ __("Image de l'hôtel") }}</label><input type="file" name="image" accept="image/*"></div>
                    <div class="form-group"><button class="btn" type="submit">{{ __("Créer l'hôtel") }}</button></div>
                </div>
            </form>
        </div>
        @endif
    @else
        {{-- Filtres par catégorie (mode "mes hôtels") --}}
        <div class="grid" style="grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom:1.5rem;">
            <a href="{{ route('hotels.index') }}" class="tag {{ $filter ? '' : 'active' }}">{{ __('Tous') }}</a>
            <a href="{{ route('hotels.index', ['filter' => 'Luxe']) }}" class="tag {{ $filter === 'Luxe' ? 'active' : '' }}">{{ __('Luxe') }}</a>
            <a href="{{ route('hotels.index', ['filter' => 'Plage']) }}" class="tag {{ $filter === 'Plage' ? 'active' : '' }}">{{ __('Plage') }}</a>
            <a href="{{ route('hotels.index', ['filter' => 'Affaires']) }}" class="tag {{ $filter === 'Affaires' ? 'active' : '' }}">{{ __('Affaires') }}</a>
        </div>

        {{-- Formulaire d'ajout (uniquement en mode "mes hôtels") --}}
        @if(auth()->user()->role === 'prestataire')
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-title"><span>{{ __('Ajouter un hôtel') }}</span></div>
            <form method="POST" action="{{ route('hotels.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid">
                    <div class="form-group"><label>{{ __('Nom complet') }}</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>{{ __('Nom court') }}</label><input type="text" name="short_name" required></div>
                    <div class="form-group"><label>{{ __('Ville') }}</label><input type="text" name="city" required></div>
                    <div class="form-group"><label>{{ __('Quartier') }}</label><input type="text" name="quartier" required></div>
                    <div class="form-group"><label>{{ __('Étoiles') }}</label><select name="stars" required class="form-select"><option value="3">{{ __('3 étoiles') }}</option><option value="4">{{ __('4 étoiles') }}</option><option value="5">{{ __('5 étoiles') }}</option></select></div>
                    <div class="form-group"><label>{{ __('Nombre de chambres') }}</label><input type="number" name="rooms" value="12" required></div>
                    <div class="form-group"><label>{{ __('Catégorie') }}</label><select name="category" required class="form-select"><option value="Luxe">{{ __('Luxe') }}</option><option value="Plage">{{ __('Plage') }}</option><option value="Affaires">{{ __('Affaires') }}</option><option value="Standard">{{ __('Standard') }}</option></select></div>
                    <div class="form-group"><label>{{ __('Statut') }}</label><select name="status" required class="form-select"><option value="Ouvert">{{ __('Ouvert') }}</option><option value="Fermé">{{ __('Fermé') }}</option><option value="En travaux">{{ __('En travaux') }}</option></select></div>
                    <div class="form-group"><label>{{ __('Note (0-5)') }}</label><input type="number" step="0.1" name="rating" value="4.5" min="0" max="5" required></div>
                    <div class="form-group"><label>{{ __('Prix par nuit (FCFA)') }}</label><input type="number" name="price" value="210000" required></div>
                    <div class="form-group"><label>{{ __('Description') }}</label><textarea name="description" rows="3"></textarea></div>
                    <div class="form-group"><label>{{ __('Services (séparés par des virgules)') }}</label><input type="text" name="services" placeholder="WiFi, Parking, Restaurant, Piscine"></div>
                    <div class="form-group"><label>{{ __("Image de l'hôtel") }}</label><input type="file" name="image" accept="image/*"></div>
                    <div class="form-group"><button class="btn" type="submit">{{ __("Créer l'hôtel") }}</button></div>
                </div>
            </form>
        </div>
        @endif
    @endif

    <div class="grid hotels-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem;">

    @forelse($hotels as $hotel)
        <div class="card">

            @if($hotel->image)
                <img src="{{ asset('storage/' . $hotel->image) }}"
                     alt="{{ __('Image de') }} {{ $hotel->name }}"
                     style="width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:1rem;">
            @else
                <img src="{{ asset('images/default-hotel.jpg') }}"
                     alt="{{ __('Image par défaut') }}"
                     style="width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:1rem;">
            @endif

            <div class="card-title">
                <span>{{ $hotel->emoji }}</span>
                <span>{{ $hotel->name }}</span>
                @if(($scope ?? 'mine') === 'tous' && $hotel->user_id !== auth()->id())
                    <span style="margin-left:auto;padding:.2rem .55rem;border-radius:999px;font-size:.7rem;font-weight:600;background:rgba(148,163,184,.12);color:#94a3b8;border:1px solid rgba(148,163,184,.2);">
                        <i class="fa-solid fa-building"></i> {{ __('Autre prestataire') }}
                    </span>
                @endif
            </div>

            <div style="font-size: .9rem; color: #94a3b8; margin-bottom:1rem;">
                {{ $hotel->city }} — {{ $hotel->quartier }}
            </div>

            <div class="meta-line"><span>{{ __('Catégorie') }}</span><strong>{{ $hotel->category }}</strong></div>
            <div class="meta-line"><span>{{ __('Chambres') }}</span><strong>{{ $hotel->rooms }}</strong></div>
            <div class="meta-line">
                <span>{{ __('Prix / nuit') }}</span>
                <strong>{{ number_format($hotel->price ?? 0, 0, ',', ' ') }} FCFA</strong>
            </div>

            <div style="margin-top:1rem;">
                <span class="badge badge-success">{{ $hotel->status }}</span>
            </div>

            <div style="margin-top:1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-secondary">{{ __('Voir') }}</a>

                @if($hotel->user_id === auth()->id())
                    <a href="{{ route('hotels.edit', $hotel) }}" class="btn btn-primary">{{ __('Modifier') }}</a>
                    <form method="POST" action="{{ route('hotels.destroy', $hotel) }}"
                          style="display: inline;"
                          onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cet hôtel ?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Supprimer') }}</button>
                    </form>
                @endif
            </div>

        </div>
    @empty
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:#94a3b8;">
            <i class="fa-solid fa-hotel" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
            <p>{{ __('Aucun hôtel trouvé.') }}</p>
        </div>
    @endforelse

</div>
@push('styles')
<style>
.hidden { display: none !important; }
@media (max-width: 720px) {
    .hotels-grid { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@endsection
