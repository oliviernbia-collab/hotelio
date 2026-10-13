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

@php
    $poolStatusMap = [
        'ouverte'     => ['rgba(16,185,129,.15)', '#10b981', __('Ouverte')],
        'fermée'      => ['rgba(107,114,128,.15)','#9ca3af', __('Fermée')],
        'maintenance' => ['rgba(245,158,11,.15)', '#f59e0b', __('Maintenance')],
    ];
@endphp

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:0;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(14,165,233,.14);border:1px solid rgba(14,165,233,.28);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#38bdf8;">
                <i class="fa-solid fa-water-ladder"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Gestion des piscines') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Piscines disponibles par hôtel, leur statut et leurs caractéristiques.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- FORMULAIRE AJOUT --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-plus"></i> {{ __('Nouvelle piscine') }}</span>
    </div>

    @if($hotels->isEmpty())
    <p style="color:var(--text-muted);font-size:.88rem;">{{ __("Ajoutez d'abord un hôtel pour pouvoir y rattacher une piscine.") }}</p>
    @else
    <form method="POST" action="{{ route('pools.store') }}">
        @csrf
        <div class="form-group">
            <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
            <select name="hotel_id" required>
                @foreach($hotels as $hotel)
                    <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Nom') }} <span style="color:#e63946;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Ex : Piscine panoramique') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Statut') }} <span style="color:#e63946;">*</span></label>
                <select name="status" required>
                    @foreach(\App\Http\Controllers\DashboardController::POOL_STATUSES as $st)
                        <option value="{{ $st }}" {{ old('status', 'ouverte') === $st ? 'selected' : '' }}>{{ $poolStatusMap[$st][2] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Surface (m²)') }}</label>
                <input type="number" name="area" min="0" value="{{ old('area') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Profondeur (m)') }}</label>
                <input type="number" step="0.1" name="depth" min="0" value="{{ old('depth') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Température (°C)') }}</label>
                <input type="number" step="0.1" name="temperature" value="{{ old('temperature') }}">
            </div>
        </div>

        <div class="form-group" style="margin-top:1rem;">
            <label>{{ __('Description') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
            <textarea name="description" rows="2" style="resize:vertical;">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn" style="width:100%;justify-content:center;">
            <i class="fa-solid fa-plus"></i> {{ __('Ajouter la piscine') }}
        </button>
    </form>
    @endif
</div>

{{-- LISTE DES PISCINES --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-list-ul"></i> {{ __('Liste des piscines') }}</span>
        <span class="badge badge-secondary">{{ $pools->total() }}</span>
    </div>

    @forelse($pools as $pool)
    @php [$pbg, $pfg, $plbl] = $poolStatusMap[$pool->status] ?? ['rgba(148,163,184,.1)', '#94a3b8', ucfirst($pool->status)]; @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;">

        <div style="display:flex;align-items:center;gap:.9rem;flex:1;min-width:0;">
            <div style="width:42px;height:42px;border-radius:12px;background:{{ $pbg }};border:1px solid {{ $pfg }}40;display:flex;align-items:center;justify-content:center;font-size:1rem;color:{{ $pfg }};flex-shrink:0;">
                <i class="fa-solid fa-water"></i>
            </div>
            <div style="min-width:0;flex:1;">
                <div style="font-weight:700;color:var(--text-primary);font-size:.95rem;">{{ $pool->name }}</div>
                <div style="font-size:.78rem;color:var(--text-muted);display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-top:.2rem;">
                    <span><i class="fa-solid fa-hotel" style="opacity:.6;"></i> {{ optional($pool->hotel)->name ?? '—' }}</span>
                    @if($pool->area)<span>· {{ $pool->area }} m²</span>@endif
                    @if($pool->depth)<span>· {{ $pool->depth }} m</span>@endif
                    @if($pool->temperature)<span>· {{ $pool->temperature }}°C</span>@endif
                </div>
                @if($pool->description)
                <div style="font-size:.78rem;color:var(--text-secondary);font-style:italic;margin-top:.2rem;">"{{ Str::limit($pool->description, 90) }}"</div>
                @endif
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;background:{{ $pbg }};color:{{ $pfg }};">
                {{ $plbl }}
            </span>

            <button type="button" class="btn-secondary" style="padding:.4rem .7rem;font-size:.78rem;"
                onclick="openPoolEdit({{ $pool->id }}, {{ $pool->hotel_id }}, '{{ addslashes($pool->name) }}', '{{ $pool->status }}', {{ $pool->area ?? 'null' }}, {{ $pool->depth ?? 'null' }}, {{ $pool->temperature ?? 'null' }}, '{{ addslashes($pool->description ?? '') }}')">
                <i class="fa-solid fa-pen"></i>
            </button>

            <form method="POST" action="{{ route('pools.destroy', $pool->id) }}"
                  onsubmit="return confirm('{{ __('Supprimer cette piscine ?') }}')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="display:inline-flex;align-items:center;padding:.4rem .7rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;font-size:.78rem;font-family:inherit;cursor:pointer;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3.5rem;color:var(--text-muted);">
        <i class="fa-solid fa-water-ladder" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
        <p style="font-weight:500;">{{ __('Aucune piscine enregistrée.') }}</p>
        <p style="font-size:.82rem;margin-top:.25rem;">{{ __('Utilisez le formulaire ci-dessus pour créer la première piscine.') }}</p>
    </div>
    @endforelse

    @if($pools->hasPages())
    <div style="margin-top:1.25rem;display:flex;justify-content:center;">{{ $pools->links() }}</div>
    @endif
</div>

{{-- MODAL ÉDITION --}}
<div id="pool-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-weight:700;font-size:1.1rem;color:#38bdf8;">
                <i class="fa-solid fa-pen"></i> {{ __('Modifier la piscine') }}
            </div>
            <button type="button" onclick="closePoolEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="pool-edit-form" method="POST" action="">
            @csrf @method('PUT')

            <div class="form-group">
                <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
                <select name="hotel_id" id="pe-hotel" required>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Nom') }} <span style="color:#e63946;">*</span></label>
                    <input type="text" name="name" id="pe-name" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Statut') }} <span style="color:#e63946;">*</span></label>
                    <select name="status" id="pe-status" required>
                        @foreach(\App\Http\Controllers\DashboardController::POOL_STATUSES as $st)
                            <option value="{{ $st }}">{{ $poolStatusMap[$st][2] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Surface (m²)') }}</label>
                    <input type="number" name="area" id="pe-area" min="0">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Profondeur (m)') }}</label>
                    <input type="number" step="0.1" name="depth" id="pe-depth" min="0">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Température (°C)') }}</label>
                    <input type="number" step="0.1" name="temperature" id="pe-temp">
                </div>
            </div>

            <div class="form-group" style="margin-top:1rem;">
                <label>{{ __('Description') }}</label>
                <textarea name="description" id="pe-desc" rows="2" style="resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="button" onclick="closePoolEdit()" class="btn-secondary" style="flex:1;justify-content:center;">{{ __('Annuler') }}</button>
                <button type="submit" class="btn" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('pool-modal');
    const form  = document.getElementById('pool-edit-form');

    window.openPoolEdit = function (id, hotelId, name, status, area, depth, temperature, desc) {
        form.action = '/pools/' + id;
        document.getElementById('pe-hotel').value  = hotelId;
        document.getElementById('pe-name').value   = name;
        document.getElementById('pe-status').value = status;
        document.getElementById('pe-area').value   = area ?? '';
        document.getElementById('pe-depth').value  = depth ?? '';
        document.getElementById('pe-temp').value   = temperature ?? '';
        document.getElementById('pe-desc').value   = desc;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closePoolEdit = function () {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closePoolEdit();
    });
})();
</script>

@endsection
