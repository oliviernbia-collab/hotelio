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
            <div style="width:48px;height:48px;border-radius:14px;background:rgba(59,130,246,.14);border:1px solid rgba(59,130,246,.28);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#60a5fa;">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Gestion des tickets') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Suivi des demandes, incidents et réclamations par hôtel.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="tickets-stats-grid" style="margin-bottom:1.5rem;">
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(148,163,184,.1);border:1px solid rgba(148,163,184,.2);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#94a3b8;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $statTotal }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Total') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#60a5fa;">
            <i class="fa-solid fa-circle-dot"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $statOuvert }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Ouverts') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#fbbf24;">
            <i class="fa-solid fa-spinner"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $statEnCours }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('En cours') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-green-dim);border:1px solid var(--ci-green-border);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:var(--ci-green);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $statFerme }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Fermés') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(230,57,70,.1);border:1px solid rgba(230,57,70,.22);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#fca5a5;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ $statUrgent }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Urgents') }}</div>
    </div>
</div>

{{-- LAYOUT FORMULAIRE + LÉGENDE --}}
<div class="tickets-form-layout" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;align-items:start;">

    {{-- FORMULAIRE --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-plus"></i> {{ __('Nouveau ticket') }}</span>
        </div>
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf

            <div class="form-group">
                <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
                <select name="hotel_id" required>
                    <option value="">{{ __('— Sélectionner un hôtel —') }}</option>
                    @foreach($hotels as $h)
                        <option value="{{ $h->id }}" {{ old('hotel_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('Titre') }} <span style="color:#e63946;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="{{ __('Ex : Climatisation en panne chambre 204') }}" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Type') }} <span style="color:#e63946;">*</span></label>
                    <select name="ticket_type" required>
                        @foreach(['support','technique','commercial','réclamation','maintenance','autre'] as $t)
                            <option value="{{ $t }}" {{ old('ticket_type','support') === $t ? 'selected' : '' }}>{{ ucfirst(__($t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Priorité') }} <span style="color:#e63946;">*</span></label>
                    <select name="priority" required>
                        @foreach(['faible','moyenne','haute','urgente'] as $p)
                            <option value="{{ $p }}" {{ old('priority','moyenne') === $p ? 'selected' : '' }}>{{ ucfirst(__($p)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Statut') }} <span style="color:#e63946;">*</span></label>
                    <select name="status" required>
                        @foreach(['ouvert','en cours','fermé','annulé'] as $s)
                            <option value="{{ $s }}" {{ old('status','ouvert') === $s ? 'selected' : '' }}>{{ ucfirst(__($s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Assigné à') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                    <input type="text" name="assigned_to" value="{{ old('assigned_to') }}" placeholder="{{ __('Nom du responsable') }}">
                </div>
            </div>

            <div class="form-group" style="margin-top:1rem;">
                <label>{{ __('Client') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                <select name="client_id">
                    <option value="">{{ __('— Aucun client associé —') }}</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>
                            {{ $cl->first_name }} {{ $cl->last_name }}@if($cl->phone) — {{ $cl->phone }}@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('Description') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                <textarea name="description" rows="3" placeholder="{{ __('Détails du problème, étapes à reproduire…') }}" style="resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-paper-plane"></i> {{ __('Créer le ticket') }}
            </button>
        </form>
    </div>

    {{-- LÉGENDE + RAPPEL --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        <div class="card">
            <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.9rem;">
                <i class="fa-solid fa-signal"></i> {{ __('Priorités') }}
            </div>
            <div style="display:flex;flex-direction:column;gap:.55rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge" style="background:rgba(148,163,184,.15);color:#94a3b8;">● {{ __('Faible') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Aucune urgence') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-warning">● {{ __('Moyenne') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('À traiter cette semaine') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge" style="background:rgba(247,127,0,.2);color:#fb923c;">● {{ __('Haute') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('À traiter sous 24h') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-danger">● {{ __('Urgente') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Action immédiate requise') }}</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.9rem;">
                <i class="fa-solid fa-circle-half-stroke"></i> {{ __('Statuts') }}
            </div>
            <div style="display:flex;flex-direction:column;gap:.55rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge" style="background:rgba(59,130,246,.15);color:#60a5fa;"><i class="fa-solid fa-circle-dot"></i> {{ __('Ouvert') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Nouveau ticket, non traité') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-warning"><i class="fa-solid fa-spinner"></i> {{ __('En cours') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Pris en charge') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> {{ __('Fermé') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Résolu et clôturé') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> {{ __('Annulé') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Sans suite') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- LISTE DES TICKETS --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-list-ul"></i> {{ __('Liste des tickets') }}</span>
        <span class="badge badge-secondary">{{ $tickets->total() }}</span>
    </div>

    @forelse($tickets as $t)
    @php
        $priorityMap = [
            'faible'   => ['rgba(148,163,184,.15)',  '#94a3b8',  __('Faible')],
            'moyenne'  => ['var(--badge-warning-bg)', 'var(--badge-warning-fg)', __('Moyenne')],
            'haute'    => ['rgba(247,127,0,.18)',     '#fb923c',  __('Haute')],
            'urgente'  => ['var(--badge-danger-bg)',  'var(--badge-danger-fg)',  __('Urgente')],
        ];
        $statusMap = [
            'ouvert'   => ['rgba(59,130,246,.15)',   '#60a5fa',  'fa-circle-dot',   __('Ouvert')],
            'en cours' => ['var(--badge-warning-bg)','var(--badge-warning-fg)','fa-spinner',__('En cours')],
            'fermé'    => ['var(--badge-success-bg)','var(--badge-success-fg)','fa-circle-check',__('Fermé')],
            'annulé'   => ['var(--badge-danger-bg)', 'var(--badge-danger-fg)', 'fa-xmark',__('Annulé')],
        ];
        [$pbg, $pfg, $plbl] = $priorityMap[$t->priority] ?? ['rgba(148,163,184,.1)','#94a3b8', ucfirst($t->priority)];
        [$sbg, $sfg, $sico, $slbl] = $statusMap[$t->status] ?? ['rgba(148,163,184,.1)','#94a3b8','fa-circle',ucfirst($t->status)];
    @endphp
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;transition:border-color .2s;"
         onmouseover="this.style.borderColor='rgba(59,130,246,.35)'"
         onmouseout="this.style.borderColor='var(--border)'">

        <div style="display:flex;align-items:flex-start;gap:.9rem;flex:1;min-width:0;">
            <div style="width:42px;height:42px;border-radius:12px;background:{{ $pbg }};border:1px solid {{ $pfg }}40;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;color:{{ $pfg }};flex-shrink:0;letter-spacing:.03em;">
                {{ strtoupper(substr($t->priority, 0, 1)) }}
            </div>
            <div style="min-width:0;flex:1;">
                <div style="font-weight:700;color:var(--text-primary);font-size:.95rem;margin-bottom:.2rem;">
                    {{ $t->title }}
                </div>
                <div style="font-size:.78rem;color:var(--text-muted);display:flex;flex-wrap:wrap;gap:.6rem;align-items:center;margin-bottom:.35rem;">
                    <span><i class="fa-solid fa-hotel" style="opacity:.6;"></i> {{ optional($t->hotel)->name ?? '—' }}</span>
                    @if($t->client)<span>· <i class="fa-solid fa-user" style="opacity:.6;"></i> {{ $t->client->first_name }} {{ $t->client->last_name }}</span>@endif
                    @if($t->assigned_to)<span>· <i class="fa-solid fa-user-tie" style="opacity:.6;"></i> {{ $t->assigned_to }}</span>@endif
                    <span>· {{ $t->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                </div>
                @if($t->description)
                <div style="font-size:.78rem;color:var(--text-secondary);font-style:italic;">"{{ Str::limit($t->description, 80) }}"</div>
                @endif
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;background:{{ $pbg }};color:{{ $pfg }};">
                {{ $plbl }}
            </span>
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;background:{{ $sbg }};color:{{ $sfg }};">
                <i class="fa-solid {{ $sico }}" style="font-size:.65rem;"></i> {{ $slbl }}
            </span>
            <span class="badge badge-secondary" style="font-size:.7rem;">{{ ucfirst(__(str_replace('_',' ',$t->ticket_type))) }}</span>
            {{-- Modifier --}}
            <button type="button" class="btn-secondary" style="padding:.4rem .7rem;font-size:.78rem;"
                onclick="openTicketEdit({{ $t->id }}, {{ $t->hotel_id }}, '{{ addslashes($t->title) }}', '{{ $t->ticket_type }}', '{{ $t->priority }}', '{{ $t->status }}', {{ $t->client_id ?? 'null' }}, '{{ addslashes($t->assigned_to ?? '') }}', '{{ addslashes($t->description ?? '') }}')">
                <i class="fa-solid fa-pen"></i>
            </button>
            {{-- Supprimer --}}
            <form method="POST" action="{{ route('tickets.destroy', $t->id) }}"
                  onsubmit="return confirm('{{ __('Supprimer ce ticket ?') }}')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="display:inline-flex;align-items:center;padding:.4rem .7rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;font-size:.78rem;font-family:inherit;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='rgba(230,57,70,.25)'"
                        onmouseout="this.style.background='rgba(230,57,70,.1)'">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3.5rem;color:var(--text-muted);">
        <i class="fa-solid fa-ticket" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
        <p style="font-weight:500;">{{ __('Aucun ticket enregistré.') }}</p>
        <p style="font-size:.82rem;margin-top:.25rem;">{{ __('Utilisez le formulaire ci-dessus pour créer le premier ticket.') }}</p>
    </div>
    @endforelse

    @if($tickets->hasPages())
    <div style="margin-top:1.25rem;display:flex;justify-content:center;">{{ $tickets->links() }}</div>
    @endif
</div>

{{-- MODAL ÉDITION --}}
<div id="ticket-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-weight:700;font-size:1.1rem;color:#60a5fa;">
                <i class="fa-solid fa-pen"></i> {{ __('Modifier le ticket') }}
            </div>
            <button onclick="closeTicketEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="ticket-edit-form" method="POST" action="">
            @csrf @method('PUT')

            <div class="form-group">
                <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
                <select name="hotel_id" id="te-hotel" required>
                    @foreach($hotels as $h)
                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('Titre') }} <span style="color:#e63946;">*</span></label>
                <input type="text" name="title" id="te-title" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Type') }} <span style="color:#e63946;">*</span></label>
                    <select name="ticket_type" id="te-type" required>
                        @foreach(['support','technique','commercial','réclamation','maintenance','autre'] as $tt)
                            <option value="{{ $tt }}">{{ ucfirst(__($tt)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Priorité') }} <span style="color:#e63946;">*</span></label>
                    <select name="priority" id="te-priority" required>
                        @foreach(['faible','moyenne','haute','urgente'] as $tp)
                            <option value="{{ $tp }}">{{ ucfirst(__($tp)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Statut') }} <span style="color:#e63946;">*</span></label>
                    <select name="status" id="te-status" required>
                        @foreach(['ouvert','en cours','fermé','annulé'] as $ts)
                            <option value="{{ $ts }}">{{ ucfirst(__($ts)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Assigné à') }}</label>
                    <input type="text" name="assigned_to" id="te-assigned">
                </div>
            </div>

            <div class="form-group" style="margin-top:1rem;">
                <label>{{ __('Client') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                <select name="client_id" id="te-client">
                    <option value="">{{ __('— Aucun —') }}</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->first_name }} {{ $cl->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('Description') }}</label>
                <textarea name="description" id="te-desc" rows="3" style="resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="button" onclick="closeTicketEdit()" class="btn-secondary" style="flex:1;justify-content:center;">{{ __('Annuler') }}</button>
                <button type="submit" class="btn" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.tickets-stats-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 1rem;
}
@media (max-width: 767px) {
    .tickets-stats-grid   { grid-template-columns: repeat(2, 1fr); }
    .tickets-form-layout  { grid-template-columns: 1fr !important; }
}
</style>

<script>
(function () {
    const modal = document.getElementById('ticket-modal');
    const form  = document.getElementById('ticket-edit-form');

    window.openTicketEdit = function(id, hotelId, title, type, priority, status, clientId, assigned, desc) {
        form.action = '/tickets/' + id;
        document.getElementById('te-hotel').value    = hotelId;
        document.getElementById('te-title').value    = title;
        document.getElementById('te-type').value     = type;
        document.getElementById('te-priority').value = priority;
        document.getElementById('te-status').value   = status;
        document.getElementById('te-client').value   = clientId || '';
        document.getElementById('te-assigned').value = assigned;
        document.getElementById('te-desc').value     = desc;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeTicketEdit = function() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeTicketEdit();
    });
})();
</script>

@endsection
