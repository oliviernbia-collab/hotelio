@extends('layouts.app')

@section('content')

<script>var USERS_DATA = {}; var UPDATE_USER_ROUTE = "{{ route('users.update', '__ID__') }}";</script>

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-title">
        <div>
            <div style="font-size:1.2rem;font-weight:700"><i class="fa-solid fa-users" style="color:var(--ci-orange)"></i> {{ __('Gestion des Utilisateurs') }}</div>
            <p style="margin:.3rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ $users->total() }} {{ __('utilisateur(s) enregistré(s) sur la plateforme.') }}</p>
        </div>
        <button onclick="document.getElementById('addUserForm').classList.toggle('hidden')" class="btn">
            <i class="fa-solid fa-user-plus"></i> {{ __('Ajouter un utilisateur') }}
        </button>
    </div>
</div>

{{-- FORMULAIRE AJOUT --}}
<div id="addUserForm" class="card hidden" style="margin-bottom:1.5rem;border-color:var(--ci-orange-border)">
    <div class="card-title" style="margin-bottom:1.25rem;"><span><i class="fa-solid fa-user-plus" style="color:var(--ci-orange)"></i> {{ __('Nouvel utilisateur') }}</span></div>
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="grid" style="grid-template-columns:1fr 1fr 1fr;gap:1rem">
            <div class="form-group">
                <label>{{ __('Nom complet') }}</label>
                <input type="text" name="name" required placeholder="Jean Kouassi">
            </div>
            <div class="form-group">
                <label>{{ __('Email') }}</label>
                <input type="email" name="email" required placeholder="jean@example.com">
            </div>
            <div class="form-group">
                <label>{{ __('Rôle') }}</label>
                <select name="role" required class="form-select">
                    <option value="client">{{ __('Client') }}</option>
                    <option value="prestataire">{{ __('Prestataire') }}</option>
                    <option value="admin">{{ __('Administrateur') }}</option>
                </select>
            </div>
        </div>
        <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:1rem">
            <i class="fa-solid fa-info-circle"></i> {{ __('Mot de passe par défaut :') }} <code>password123</code>
        </p>
        <button type="submit" class="btn"><i class="fa-solid fa-check"></i> {{ __("Créer l'utilisateur") }}</button>
    </form>
</div>

{{-- TABLEAU --}}
<div class="card">
    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Utilisateur') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Rôle') }}</th>
                <th>{{ __('Statut') }}</th>
                <th>{{ __('Hôtels') }}</th>
                <th>{{ __('Inscrit le') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="color:var(--text-muted);font-size:.8rem">#{{ $user->id }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <div style="width:34px;height:34px;border-radius:50%;background:var(--ci-orange);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:.85rem;flex-shrink:0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div style="font-weight:600;font-size:.9rem">{{ $user->name }}</div>
                    </div>
                </td>
                <td style="color:var(--text-secondary);font-size:.85rem">{{ $user->email }}</td>
                <td>
                    <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'prestataire' ? 'warning' : 'success') }}">
                        @if($user->role === 'admin') <i class="fa-solid fa-crown"></i>
                        @elseif($user->role === 'prestataire') <i class="fa-solid fa-building"></i>
                        @else <i class="fa-solid fa-user"></i>
                        @endif
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ match($user->status ?? 'actif') { 'actif' => 'success', 'en_attente' => 'warning', default => 'secondary' } }}">
                        {{ $user->status === 'en_attente' ? __('En attente') : ucfirst($user->status ?? 'actif') }}
                    </span>
                    @if($user->status === 'en_attente' && ($user->permissions['proof_document'] ?? null))
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($user->permissions['proof_document']) }}" target="_blank"
                           style="display:block;margin-top:.3rem;font-size:.72rem;color:var(--ci-orange);">
                            <i class="fa-solid fa-file-arrow-down"></i> {{ __('Voir le justificatif') }}
                        </a>
                    @endif
                </td>
                <td style="color:var(--text-muted);font-size:.8rem">
                    @if($user->role === 'prestataire')
                        {{ \App\Models\Hotel::where('user_id', $user->id)->count() }} {{ __('hôtel(s)') }}
                    @else —
                    @endif
                </td>
                <td style="color:var(--text-muted);font-size:.8rem">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    <div style="display:flex;gap:.4rem;">
                        <button onclick="openEditUser({{ $user->id }})"
                                class="btn-secondary"
                                style="padding:.35rem .7rem;font-size:.78rem;display:flex;align-items:center;gap:.25rem;">
                            <i class="fa-solid fa-pen"></i> {{ __('Modifier') }}
                        </button>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                              onsubmit="return confirm('{{ __('Supprimer :name ?', ['name' => addslashes($user->name)]) }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="padding:.35rem .7rem;font-size:.78rem;display:flex;align-items:center;gap:.25rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;cursor:pointer;transition:background .2s;"
                                    onmouseover="this.style.background='rgba(230,57,70,.22)'"
                                    onmouseout="this.style.background='rgba(230,57,70,.1)'">
                                <i class="fa-solid fa-trash"></i> {{ __('Supprimer') }}
                            </button>
                        </form>
                        @else
                        <span style="font-size:.72rem;color:var(--text-muted);align-self:center;">{{ __('(vous)') }}</span>
                        @endif
                    </div>
                </td>
            </tr>

            {{-- Données pour la modale --}}
            <script>
            USERS_DATA[{{ $user->id }}] = {
                name:   "{{ addslashes($user->name) }}",
                email:  "{{ addslashes($user->email) }}",
                role:   "{{ $user->role }}",
                status: "{{ $user->status ?? 'actif' }}",
            };
            </script>

            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem">{{ __('Aucun utilisateur.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div style="margin-top:1.25rem;display:flex;justify-content:center">
        {{ $users->links() }}
    </div>
</div>

{{-- MODALE MODIFICATION --}}
<div id="editUserModal" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(2,6,23,.7);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.5rem;width:100%;max-width:520px;position:relative;">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">
                <i class="fa-solid fa-user-pen" style="color:var(--ci-orange)"></i> {{ __("Modifier l'utilisateur") }}
            </div>
            <button onclick="closeEditUser()" style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg-input);cursor:pointer;color:var(--text-secondary);display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label><i class="fa-solid fa-user" style="color:var(--ci-orange)"></i> {{ __('Nom complet') }}</label>
                <input type="text" id="eu_name" name="name" required>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-envelope" style="color:var(--ci-orange)"></i> {{ __('Email') }}</label>
                <input type="email" id="eu_email" name="email" required>
            </div>
            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label><i class="fa-solid fa-shield-halved" style="color:var(--ci-orange)"></i> {{ __('Rôle') }}</label>
                    <select id="eu_role" name="role" required class="form-select">
                        <option value="client">{{ __('Client') }}</option>
                        <option value="prestataire">{{ __('Prestataire') }}</option>
                        <option value="admin">{{ __('Administrateur') }}</option>
                    </select>
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label><i class="fa-solid fa-toggle-on" style="color:var(--ci-orange)"></i> {{ __('Statut') }}</label>
                    <select id="eu_status" name="status" required class="form-select">
                        <option value="actif">{{ __('Actif') }}</option>
                        <option value="en_attente">{{ __('En attente') }}</option>
                        <option value="inactif">{{ __('Inactif') }}</option>
                        <option value="suspendu">{{ __('Suspendu') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-lock" style="color:var(--ci-orange)"></i> {{ __('Nouveau mot de passe') }} <span style="color:var(--text-muted);font-size:.78rem;">{{ __('(laisser vide pour ne pas changer)') }}</span></label>
                <input type="password" id="eu_password" name="password" placeholder="{{ __('Min. 6 caractères') }}">
            </div>

            <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:.5rem;">
                <button type="button" onclick="closeEditUser()" class="btn-secondary">{{ __('Annuler') }}</button>
                <button type="submit" class="btn">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditUser(id) {
    const d = USERS_DATA[id];
    if (!d) return;
    document.getElementById('editUserForm').action = UPDATE_USER_ROUTE.replace('__ID__', id);
    document.getElementById('eu_name').value   = d.name;
    document.getElementById('eu_email').value  = d.email;
    document.getElementById('eu_role').value   = d.role;
    document.getElementById('eu_status').value = d.status;
    document.getElementById('eu_password').value = '';
    document.getElementById('editUserModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeEditUser() {
    document.getElementById('editUserModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditUser(); });
</script>

<style>.hidden { display: none !important; }</style>

@endsection
