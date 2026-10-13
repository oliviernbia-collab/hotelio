@extends('layouts.app')

@section('content')

@php
    $tierOf = function (int $pts) {
        return match(true) {
            $pts >= 5000 => ['💎', __('Platine'), '#6366f1'],
            $pts >= 2000 => ['🥇', __('Or'), '#f59e0b'],
            $pts >= 500  => ['🥈', __('Argent'), '#94a3b8'],
            default      => ['🥉', __('Bronze'), '#cd7f32'],
        };
    };
@endphp

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:0;">
        <div>
            <div style="font-size:1.4rem;font-weight:700;">{{ __('Clients') }}</div>
            <p style="margin:.5rem 0 0;color:#94a3b8;">{{ __('Suivi des profils des clients, de leurs visites et de leur fidélité.') }}</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('clients.index') }}" class="{{ $vue === 'tous' ? 'btn' : 'btn-secondary' }}" style="padding:.5rem 1rem;font-size:.82rem;">
                <i class="fa-solid fa-list"></i> {{ __('Tous') }}
            </a>
            <a href="{{ route('clients.index', ['vue' => 'top']) }}" class="{{ $vue === 'top' ? 'btn' : 'btn-secondary' }}" style="padding:.5rem 1rem;font-size:.82rem;">
                <i class="fa-solid fa-trophy"></i> {{ __('Meilleurs clients') }}
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:1.5rem;">
    <i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span>
</div>
@endif

@if($vue === 'top')
{{-- ── MEILLEURS CLIENTS (fidélité) ── --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-trophy" style="color:#f59e0b"></i> {{ __('Classement par dépenses') }}</span>
        <span class="badge badge-secondary">{{ $topClients->count() }}</span>
    </div>

    @forelse($topClients as $i => $client)
    @php [$emoji, $label, $color] = $tierOf((int) $client->points_fidelite); @endphp
    <div style="display:flex;align-items:center;gap:1rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;flex-wrap:wrap;">
        <div style="width:36px;text-align:center;font-weight:800;color:var(--text-muted);">
            {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}
        </div>
        <div style="flex:1;min-width:200px;">
            <div style="font-weight:700;color:var(--text-primary);">
                {{ $client->first_name }} {{ $client->last_name }}
                <span style="display:inline-flex;align-items:center;gap:.2rem;padding:.15rem .5rem;border-radius:999px;font-size:.7rem;font-weight:700;background:{{ $color }}22;color:{{ $color }};margin-left:.4rem;">
                    {{ $emoji }} {{ $label }}
                </span>
            </div>
            <div style="font-size:.78rem;color:var(--text-muted);margin-top:.2rem;">
                <i class="fa-solid fa-wallet" style="opacity:.6;"></i> {{ number_format($client->total_spend, 0, ',', ' ') }} FCFA
                &nbsp;·&nbsp;
                <i class="fa-solid fa-bed" style="opacity:.6;"></i> {{ $client->total_stays }} {{ __('séjour(s)') }}
                &nbsp;·&nbsp;
                <i class="fa-solid fa-star" style="opacity:.6;"></i> {{ $client->points_fidelite }} pts
            </div>
        </div>
        <button type="button" class="btn-secondary" style="padding:.45rem .8rem;font-size:.78rem;"
            onclick="openPoints({{ $client->id }}, '{{ addslashes($client->first_name.' '.$client->last_name) }}', {{ $client->points_fidelite }})">
            <i class="fa-solid fa-star"></i> {{ __('Points') }}
        </button>
    </div>
    @empty
    <div style="text-align:center;padding:3.5rem;color:var(--text-muted);">
        <i class="fa-solid fa-trophy" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
        <p>{{ __('Aucun client avec des séjours payés pour le moment.') }}</p>
    </div>
    @endforelse
</div>

@else
{{-- ── FORMULAIRE AJOUT ── --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-plus"></i> {{ __('Nouveau client') }}</span>
    </div>
    <form method="POST" action="{{ route('clients.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Prénom') }} <span style="color:#e63946;">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Nom') }} <span style="color:#e63946;">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Téléphone') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-top:1rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Ville') }}</label>
                <input type="text" name="city" value="{{ old('city') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Quartier') }}</label>
                <input type="text" name="quartier" value="{{ old('quartier') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>{{ __('Nationalité') }}</label>
                <input type="text" name="nationality" value="{{ old('nationality') }}">
            </div>
        </div>
        <button type="submit" class="btn" style="width:100%;justify-content:center;margin-top:1rem;">
            <i class="fa-solid fa-plus"></i> {{ __('Ajouter le client') }}
        </button>
    </form>
</div>

{{-- LISTE --}}
<div class="card">
    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Nom') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Statut') }}</th>
                <th>{{ __('Dernière visite') }}</th>
                <th>{{ __('Dépenses') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client->code }}</td>
                    <td>{{ $client->first_name }} {{ $client->last_name }}</td>
                    <td>{{ $client->email }}</td>
                    <td><span class="tag">{{ $client->status }}</span></td>
                    <td>{{ $client->last_visit?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ number_format($client->total_spend,0,',',' ') }} FCFA</td>
                    <td>
                        <button type="button" class="action-link" title="{{ __('Modifier') }}"
                            onclick="openClientEdit({{ $client->id }}, '{{ addslashes($client->first_name) }}', '{{ addslashes($client->last_name) }}', '{{ addslashes($client->email ?? '') }}', '{{ addslashes($client->phone ?? '') }}', '{{ addslashes($client->city ?? '') }}', '{{ addslashes($client->quartier ?? '') }}', '{{ addslashes($client->nationality ?? '') }}', '{{ $client->status }}', '{{ addslashes($client->note ?? '') }}')">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">{{ __('Aucun client pour le moment.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem;">{{ $clients->links() }}</div>
</div>
@endif

{{-- MODAL ÉDITION CLIENT --}}
<div id="client-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-weight:700;font-size:1.1rem;">
                <i class="fa-solid fa-pen"></i> {{ __('Modifier le client') }}
            </div>
            <button type="button" onclick="closeClientEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="client-edit-form" method="POST" action="">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Prénom') }} <span style="color:#e63946;">*</span></label>
                    <input type="text" name="first_name" id="ce-first" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Nom') }} <span style="color:#e63946;">*</span></label>
                    <input type="text" name="last_name" id="ce-last" required>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Email') }}</label>
                    <input type="email" name="email" id="ce-email">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Téléphone') }}</label>
                    <input type="text" name="phone" id="ce-phone">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Ville') }}</label>
                    <input type="text" name="city" id="ce-city">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Quartier') }}</label>
                    <input type="text" name="quartier" id="ce-quartier">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Nationalité') }}</label>
                    <input type="text" name="nationality" id="ce-nationality">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Statut') }}</label>
                    <select name="status" id="ce-status">
                        <option value="actif">{{ __('Actif') }}</option>
                        <option value="inactif">{{ __('Inactif') }}</option>
                        <option value="vip">{{ __('VIP') }}</option>
                        <option value="bloqué">{{ __('Bloqué') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top:1rem;">
                <label>{{ __('Note interne') }}</label>
                <textarea name="note" id="ce-note" rows="2" style="resize:vertical;"></textarea>
            </div>
            <div style="display:flex;gap:.75rem;margin-top:.5rem;">
                <button type="button" onclick="closeClientEdit()" class="btn-secondary" style="flex:1;justify-content:center;">{{ __('Annuler') }}</button>
                <button type="submit" class="btn" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL POINTS DE FIDÉLITÉ --}}
<div id="points-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:400px;box-shadow:0 24px 64px rgba(0,0,0,.45);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-weight:700;font-size:1.1rem;color:#f59e0b;">
                <i class="fa-solid fa-star"></i> {{ __('Points de fidélité') }}
            </div>
            <button type="button" onclick="closePoints()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <p style="font-size:.85rem;color:var(--text-secondary);margin-bottom:1rem;" id="points-client-label"></p>
        <form id="points-form" method="POST" action="">
            @csrf
            <div class="form-group">
                <label>{{ __('Points à ajouter (négatif pour retirer)') }}</label>
                <input type="number" name="points" id="pt-points" required placeholder="{{ __('Ex : 100 ou -50') }}">
            </div>
            <div style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap;">
                @foreach([50, 100, 250, 500] as $v)
                <button type="button" class="btn-secondary" style="padding:.35rem .7rem;font-size:.78rem;" onclick="document.getElementById('pt-points').value={{ $v }}">+{{ $v }}</button>
                @endforeach
            </div>
            <button type="submit" class="btn" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-check"></i> {{ __('Valider') }}
            </button>
        </form>
    </div>
</div>

<script>
(function () {
    const cModal = document.getElementById('client-modal');
    const cForm  = document.getElementById('client-edit-form');
    window.openClientEdit = function (id, first, last, email, phone, city, quartier, nat, status, note) {
        cForm.action = '/clients/' + id;
        document.getElementById('ce-first').value       = first;
        document.getElementById('ce-last').value         = last;
        document.getElementById('ce-email').value        = email;
        document.getElementById('ce-phone').value        = phone;
        document.getElementById('ce-city').value         = city;
        document.getElementById('ce-quartier').value     = quartier;
        document.getElementById('ce-nationality').value  = nat;
        document.getElementById('ce-status').value       = status;
        document.getElementById('ce-note').value         = note;
        cModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    window.closeClientEdit = function () { cModal.style.display = 'none'; document.body.style.overflow = ''; };
    cModal.addEventListener('click', e => { if (e.target === cModal) closeClientEdit(); });

    const pModal = document.getElementById('points-modal');
    const pForm  = document.getElementById('points-form');
    window.openPoints = function (id, name, current) {
        pForm.action = '/clients/' + id + '/points';
        document.getElementById('points-client-label').textContent = name + ' — {{ __("solde actuel :") }} ' + current + ' pts';
        document.getElementById('pt-points').value = '';
        pModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    window.closePoints = function () { pModal.style.display = 'none'; document.body.style.overflow = ''; };
    pModal.addEventListener('click', e => { if (e.target === pModal) closePoints(); });
})();
</script>

@endsection
