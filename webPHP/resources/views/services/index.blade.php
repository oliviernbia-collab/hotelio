@extends('layouts.app')

@section('content')

{{-- ALERTS --}}
@if(session('success'))
<div class="alert-success" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div class="alert-error" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-circle-exclamation"></i>
    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
</div>
@endif

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:0;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(20,184,166,.14);border:1px solid rgba(20,184,166,.28);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#2dd4bf;">
                <i class="fa-solid fa-concierge-bell"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Services additionnels') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Bien-être, transport, restauration… les prestations proposées en plus du séjour.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- FORMULAIRE AJOUT --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-plus"></i> {{ __('Nouveau service') }}</span>
    </div>

    @if($hotels->isEmpty())
    <p style="color:var(--text-muted);font-size:.88rem;">{{ __("Ajoutez d'abord un hôtel pour pouvoir y rattacher des services.") }}</p>
    @else
    <form method="POST" action="{{ route('services.store') }}">
        @csrf
        <div class="form-group">
            <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
            <select name="hotel_id" required>
                @foreach($hotels as $h)
                    <option value="{{ $h->id }}" {{ old('hotel_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:70px 1fr;gap:1rem;">
            <div class="form-group">
                <label>{{ __('Emoji') }}</label>
                <input type="text" name="emoji" value="{{ old('emoji', '✨') }}" maxlength="10" style="text-align:center;font-size:1.2rem;">
            </div>
            <div class="form-group">
                <label>{{ __('Nom du service') }} <span style="color:#e63946;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Ex : Massage relaxant') }}" required>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Catégorie') }} <span style="color:#e63946;">*</span></label>
                <select name="category" required>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ old('category', 'Général') === $c ? 'selected' : '' }}>{{ __($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Prix (FCFA)') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(0 = inclus gratuitement)') }}</span></label>
                <input type="number" name="price" min="0" value="{{ old('price', 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('Description') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
            <textarea name="description" rows="2" style="resize:vertical;">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn" style="width:100%;justify-content:center;">
            <i class="fa-solid fa-plus"></i> {{ __('Ajouter le service') }}
        </button>
    </form>
    @endif
</div>

{{-- LISTE DES SERVICES --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-list-ul"></i> {{ __('Liste des services') }}</span>
        <span class="badge badge-secondary">{{ $services->count() }}</span>
    </div>

    @forelse($services as $s)
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;{{ ! $s->available ? 'opacity:.6;' : '' }}">

        <div style="display:flex;align-items:center;gap:.9rem;flex:1;min-width:0;">
            <div style="width:42px;height:42px;border-radius:12px;background:rgba(20,184,166,.12);border:1px solid rgba(20,184,166,.25);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                {{ $s->emoji }}
            </div>
            <div style="min-width:0;flex:1;">
                <div style="font-weight:700;color:var(--text-primary);font-size:.95rem;">{{ $s->name }}</div>
                <div style="font-size:.78rem;color:var(--text-muted);display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-top:.2rem;">
                    <span><i class="fa-solid fa-hotel" style="opacity:.6;"></i> {{ optional($s->hotel)->name ?? '—' }}</span>
                    <span>· {{ __($s->category) }}</span>
                    <span>· {{ $s->price > 0 ? number_format($s->price, 0, ',', ' ') . ' FCFA' : __('Inclus') }}</span>
                </div>
                @if($s->description)
                <div style="font-size:.78rem;color:var(--text-secondary);font-style:italic;margin-top:.2rem;">"{{ Str::limit($s->description, 90) }}"</div>
                @endif
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;flex-wrap:wrap;">
            <form method="POST" action="{{ route('services.toggle', $s->id) }}" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="badge {{ $s->available ? 'badge-success' : 'badge-secondary' }}" style="border:none;cursor:pointer;font-family:inherit;">
                    <i class="fa-solid {{ $s->available ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                    {{ $s->available ? __('Disponible') : __('Désactivé') }}
                </button>
            </form>

            <button type="button" class="btn-secondary" style="padding:.4rem .7rem;font-size:.78rem;"
                onclick="openServiceEdit({{ $s->id }}, {{ $s->hotel_id }}, '{{ addslashes($s->name) }}', '{{ $s->category }}', {{ $s->price }}, '{{ addslashes($s->emoji) }}', '{{ addslashes($s->description ?? '') }}')">
                <i class="fa-solid fa-pen"></i>
            </button>

            <form method="POST" action="{{ route('services.destroy', $s->id) }}"
                  onsubmit="return confirm('{{ __('Supprimer ce service ?') }}')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="display:inline-flex;align-items:center;padding:.4rem .7rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;font-size:.78rem;font-family:inherit;cursor:pointer;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3.5rem;color:var(--text-muted);">
        <i class="fa-solid fa-concierge-bell" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
        <p style="font-weight:500;">{{ __('Aucun service additionnel enregistré.') }}</p>
        <p style="font-size:.82rem;margin-top:.25rem;">{{ __('Utilisez le formulaire ci-dessus pour créer le premier service.') }}</p>
    </div>
    @endforelse
</div>

{{-- MODAL ÉDITION --}}
<div id="service-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-weight:700;font-size:1.1rem;color:#2dd4bf;">
                <i class="fa-solid fa-pen"></i> {{ __('Modifier le service') }}
            </div>
            <button type="button" onclick="closeServiceEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="service-edit-form" method="POST" action="">
            @csrf @method('PUT')

            <div class="form-group">
                <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
                <select name="hotel_id" id="se-hotel" required>
                    @foreach($hotels as $h)
                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:70px 1fr;gap:1rem;">
                <div class="form-group">
                    <label>{{ __('Emoji') }}</label>
                    <input type="text" name="emoji" id="se-emoji" maxlength="10" style="text-align:center;font-size:1.2rem;">
                </div>
                <div class="form-group">
                    <label>{{ __('Nom') }} <span style="color:#e63946;">*</span></label>
                    <input type="text" name="name" id="se-name" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Catégorie') }} <span style="color:#e63946;">*</span></label>
                    <select name="category" id="se-category" required>
                        @foreach($categories as $c)
                            <option value="{{ $c }}">{{ __($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Prix (FCFA)') }}</label>
                    <input type="number" name="price" id="se-price" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('Description') }}</label>
                <textarea name="description" id="se-desc" rows="2" style="resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="button" onclick="closeServiceEdit()" class="btn-secondary" style="flex:1;justify-content:center;">{{ __('Annuler') }}</button>
                <button type="submit" class="btn" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('service-modal');
    const form  = document.getElementById('service-edit-form');

    window.openServiceEdit = function (id, hotelId, name, category, price, emoji, desc) {
        form.action = '/services/' + id;
        document.getElementById('se-hotel').value    = hotelId;
        document.getElementById('se-name').value     = name;
        document.getElementById('se-category').value = category;
        document.getElementById('se-price').value    = price;
        document.getElementById('se-emoji').value    = emoji;
        document.getElementById('se-desc').value     = desc;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeServiceEdit = function () {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeServiceEdit();
    });
})();
</script>

@endsection
