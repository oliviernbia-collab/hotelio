@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-bed"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">
                    {{ __('Chambres') }}
                    @if($hotel) — <span style="color:var(--ci-orange);">{{ $hotel->name }}</span>@endif
                </div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">
                    {{ __("Gérez l'inventaire des chambres et leurs statuts.") }}
                </p>
            </div>
        </div>
        {{-- Sélecteur d'hôtel --}}
        @php $hotels = \App\Models\Hotel::whereIn('id', auth()->user()->getHotelIds())->get(); @endphp
        @if($hotels->count() > 1)
        <form method="GET" action="{{ route('rooms.index') }}" style="display:flex;align-items:center;gap:.5rem;">
            <select name="hotel_id" class="form-select" style="padding:.5rem .9rem;font-size:.85rem;min-width:180px;" onchange="this.form.submit()">
                @foreach($hotels as $h)
                    <option value="{{ $h->id }}" {{ $hotelId == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
</div>

<div class="grid" style="grid-template-columns:2fr 1fr;gap:1.25rem;">

    {{-- LISTE DES CHAMBRES --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-list"></i> {{ __('Chambres') }}</span>
            <span class="badge badge-secondary">{{ $rooms->total() }}</span>
        </div>

        @if($rooms->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--text-muted);">
            <i class="fa-solid fa-bed" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
            <p style="margin-bottom:.5rem;">{{ __('Aucune chambre pour cet hôtel.') }}</p>
            <p style="font-size:.85rem;">{{ __('Utilisez le formulaire ci-contre pour ajouter des chambres.') }}</p>
        </div>
        @else
        <div class="grid" style="grid-template-columns:repeat(2,1fr);gap:.75rem;">
            @foreach($rooms as $room)
            <div style="padding:1rem;background:var(--bg-input);border-radius:14px;border:1px solid var(--border);transition:border-color .2s;"
                 onmouseover="this.style.borderColor='var(--ci-orange-border)'"
                 onmouseout="this.style.borderColor='var(--border)'">
                @if($room->image_url)
                    <img src="{{ $room->image_url }}" alt="{{ $room->number }}"
                         style="width:100%;height:110px;object-fit:cover;border-radius:10px;margin-bottom:.6rem;cursor:pointer;"
                         onclick="window.open('{{ $room->image_url }}','_blank')">
                @endif
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                    <div style="font-weight:800;font-size:1.05rem;color:var(--text-primary);">
                        <i class="fa-solid fa-door-open" style="color:var(--ci-orange);font-size:.9rem;"></i>
                        {{ $room->number }}
                    </div>
                    @php
                        $badgeClass = match($room->status) {
                            'disponible'   => 'success',
                            'réservée'     => 'warning',
                            'occupée'      => 'danger',
                            'en_nettoyage' => 'secondary',
                            default        => 'secondary',
                        };
                    @endphp
                    <span class="badge badge-{{ $badgeClass }}">
                        {{ ucfirst(str_replace('_',' ', $room->status)) }}
                    </span>
                </div>
                <div style="font-size:.82rem;color:var(--text-secondary);margin-bottom:.4rem;">
                    <i class="fa-solid fa-tag" style="opacity:.6;"></i> {{ $room->type }}
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-layer-group" style="opacity:.6;"></i> {{ __('Étage') }} {{ $room->floor }}
                    &nbsp;·&nbsp;
                    <i class="fa-solid fa-users" style="opacity:.6;"></i> {{ $room->capacity }} {{ __('pers.') }}
                </div>
                <div style="font-weight:700;color:var(--ci-green);font-size:.95rem;margin-bottom:.75rem;">
                    {{ number_format($room->price, 0, ',', ' ') }} FCFA / nuit
                </div>
                <div style="display:flex;gap:.4rem;">
                    {{-- Changer le statut --}}
                    <form method="POST" action="{{ route('rooms.status', $room->id) }}" style="flex:1;">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select" style="padding:.35rem .6rem;font-size:.75rem;width:100%;" onchange="this.form.submit()">
                            <option value="disponible"   {{ $room->status === 'disponible'   ? 'selected' : '' }}>{{ __('Disponible') }}</option>
                            <option value="réservée"     {{ $room->status === 'réservée'     ? 'selected' : '' }}>{{ __('Réservée') }}</option>
                            <option value="occupée"      {{ $room->status === 'occupée'      ? 'selected' : '' }}>{{ __('Occupée') }}</option>
                            <option value="en_nettoyage" {{ $room->status === 'en_nettoyage' ? 'selected' : '' }}>{{ __('En nettoyage') }}</option>
                            <option value="hors_service" {{ $room->status === 'hors_service' ? 'selected' : '' }}>{{ __('Hors service') }}</option>
                            <option value="maintenance"  {{ $room->status === 'maintenance'  ? 'selected' : '' }}>{{ __('Maintenance') }}</option>
                        </select>
                    </form>
                    {{-- Nettoyage terminé → Libre --}}
                    @if($room->status === 'en_nettoyage')
                    <form method="POST" action="{{ route('rooms.cleaned', $room->id) }}">
                        @csrf
                        <button type="submit"
                                style="padding:.35rem .65rem;border-radius:8px;border:1px solid rgba(0,154,68,.4);background:rgba(0,154,68,.15);color:#a7f3d0;cursor:pointer;font-size:.75rem;white-space:nowrap;"
                                title="{{ __('Marquer propre → Disponible') }}">
                            <i class="fa-solid fa-broom"></i> OK
                        </button>
                    </form>
                    @endif
                    {{-- Supprimer --}}
                    <form method="POST" action="{{ route('rooms.destroy', $room->id) }}"
                          onsubmit="return confirm('{{ __('Supprimer la chambre :number ?', ['number' => $room->number]) }}')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="padding:.35rem .6rem;border-radius:8px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;cursor:pointer;font-size:.78rem;transition:background .2s;"
                                onmouseover="this.style.background='rgba(230,57,70,.22)'"
                                onmouseout="this.style.background='rgba(230,57,70,.1)'">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
                {{-- Ajouter / remplacer l'image --}}
                <form method="POST" action="{{ route('rooms.image', $room->id) }}" enctype="multipart/form-data"
                      style="margin-top:.5rem;display:flex;gap:.4rem;">
                    @csrf
                    <input type="file" name="image" accept="image/*" required
                           style="flex:1;font-size:.72rem;padding:.3rem;border-radius:8px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);">
                    <button type="submit"
                            style="padding:.35rem .6rem;border-radius:8px;border:1px solid var(--ci-orange-border);background:var(--ci-orange-dim);color:var(--ci-orange);cursor:pointer;font-size:.78rem;white-space:nowrap;"
                            title="{{ __("Enregistrer l'image") }}">
                        <i class="fa-solid fa-upload"></i>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @if($rooms->hasPages())
        <div style="margin-top:1.25rem;">{{ $rooms->links() }}</div>
        @endif
        @endif
    </div>

    {{-- FORMULAIRE AJOUT --}}
    <div class="card staff-form" style="align-self:start;">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-plus-circle"></i> {{ __('Ajouter une chambre') }}</span>
        </div>

        <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="hotel_id" value="{{ $hotelId }}">

            <div class="form-group">
                <label><i class="fa-solid fa-hashtag" style="color:var(--ci-orange)"></i> {{ __('Numéro de chambre') }}</label>
                <input type="text" name="number" required placeholder="{{ __('Ex : 101, A12') }}">
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label><i class="fa-solid fa-stairs" style="color:var(--ci-orange)"></i> {{ __('Étage') }}</label>
                    <input type="number" name="floor" min="0" value="1" required>
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label><i class="fa-solid fa-users" style="color:var(--ci-orange)"></i> {{ __('Capacité') }}</label>
                    <input type="number" name="capacity" min="1" value="2" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-tag" style="color:var(--ci-orange)"></i> {{ __('Type') }}</label>
                <select name="type" required class="form-select">
                    <option value="Simple">{{ __('Simple') }}</option>
                    <option value="Double">{{ __('Double') }}</option>
                    <option value="Suite">{{ __('Suite') }}</option>
                    <option value="Deluxe">{{ __('Deluxe') }}</option>
                    <option value="Familiale">{{ __('Familiale') }}</option>
                    <option value="Présidentielle">{{ __('Présidentielle') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-toggle-on" style="color:var(--ci-orange)"></i> {{ __('Statut') }}</label>
                <select name="status" required class="form-select">
                    <option value="disponible">{{ __('Disponible') }}</option>
                    <option value="occupée">{{ __('Occupée') }}</option>
                    <option value="maintenance">{{ __('Maintenance') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-coins" style="color:var(--ci-orange)"></i> {{ __('Prix / nuit (FCFA)') }}</label>
                <input type="number" name="price" min="0" value="50000" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left" style="color:var(--ci-orange)"></i> {{ __('Description (optionnel)') }}</label>
                <textarea name="description" rows="2" placeholder="{{ __('Vue mer, balcon, climatisation...') }}"></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image" style="color:var(--ci-orange)"></i> {{ __('Image de la chambre (optionnel)') }}</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-plus"></i> {{ __('Ajouter la chambre') }}
            </button>
        </form>

        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);text-align:center;">
            <a href="{{ route('hotels.index') }}" class="btn-secondary" style="font-size:.82rem;">
                <i class="fa-solid fa-arrow-left"></i> {{ __('Retour aux hôtels') }}
            </a>
        </div>
    </div>
</div>

@endsection
