@extends('layouts.app')

@section('content')
{{-- DOIT être en premier : utilisé par les inline scripts du foreach --}}
<script>var STAFF_DATA = {};</script>
<script>var UPDATE_ROUTE = "{{ route('staff.update', '__ID__') }}";</script>

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Gestion du personnel') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">
                    {{ $staff->total() }} {{ __('membre(s) enregistré(s)') }}
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width:720px) {
    .staff-grid { grid-template-columns: 1fr !important; }
    .staff-list { order: 2; }
    .staff-form { order: 1; }
}
</style>
<div class="grid staff-grid" style="grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">

    {{-- LISTE DU PERSONNEL --}}
    <div class="card staff-list">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-list"></i> {{ __('Liste du personnel') }}</span>
            <span class="badge badge-secondary">{{ $staff->total() }}</span>
        </div>

        <div style="display:flex;flex-direction:column;gap:.75rem;">
            @forelse($staff as $member)
            <div style="display:flex;align-items:center;gap:1rem;padding:1rem;background:var(--bg-input);border-radius:14px;border:1px solid var(--border);transition:border-color .2s;"
                 onmouseover="this.style.borderColor='var(--ci-orange-border)'"
                 onmouseout="this.style.borderColor='var(--border)'">

                {{-- PHOTO --}}
                <div style="flex-shrink:0;">
                    @if($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}"
                             alt="{{ $member->first_name }}"
                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--ci-orange-border);">
                    @else
                        <div style="width:52px;height:52px;border-radius:50%;background:var(--ci-orange-dim);border:2px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:var(--ci-orange);">
                            {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- INFOS --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;color:var(--text-primary);">
                        {{ $member->first_name }} {{ $member->last_name }}
                    </div>
                    <div style="font-size:.82rem;color:var(--ci-orange);font-weight:600;">{{ $member->role }}</div>
                    <div style="font-size:.78rem;color:var(--text-muted);margin-top:.2rem;">
                        {{ $member->hotel->name ?? '—' }}
                    </div>
                </div>

                {{-- CONTACTS --}}
                <div style="display:flex;flex-direction:column;gap:.25rem;text-align:right;flex-shrink:0;">
                    @if($member->email)
                    <div style="font-size:.78rem;color:var(--text-secondary);">
                        <i class="fa-solid fa-envelope" style="color:var(--text-muted)"></i>
                        {{ $member->email }}
                    </div>
                    @endif
                    @if($member->phone)
                    <div style="font-size:.78rem;color:var(--text-secondary);">
                        <i class="fa-solid fa-phone" style="color:var(--text-muted)"></i>
                        {{ $member->phone }}
                    </div>
                    @endif
                    @if($member->hired_at)
                    <div style="font-size:.72rem;color:var(--text-muted);">
                        <i class="fa-solid fa-calendar"></i>
                        {{ __('Depuis') }} {{ $member->hired_at->format('m/Y') }}
                    </div>
                    @endif
                </div>

                {{-- STATUT + SALAIRE --}}
                <div style="flex-shrink:0;text-align:center;min-width:90px;">
                    <span class="badge {{ $member->status === 'actif' ? 'badge-success' : ($member->status === 'inactif' ? 'badge-danger' : 'badge-warning') }}" style="display:block;margin-bottom:.4rem;">
                        {{ ucfirst(__($member->status)) }}
                    </span>
                    <div style="font-size:.78rem;font-weight:700;color:var(--ci-green);">
                        {{ number_format($member->salary, 0, ',', ' ') }}
                        <span style="font-size:.65rem;opacity:.7;">FCFA</span>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div style="display:flex;flex-direction:column;gap:.4rem;flex-shrink:0;">
                    <button onclick="openEditModal({{ $member->id }})"
                            class="btn-secondary"
                            style="padding:.4rem .75rem;font-size:.78rem;display:flex;align-items:center;gap:.3rem;">
                        <i class="fa-solid fa-pen"></i> {{ __('Modifier') }}
                    </button>
                    <form method="POST" action="{{ route('staff.destroy', $member->id) }}"
                          onsubmit="return confirm('{{ __('Supprimer :name ?', ['name' => $member->first_name.' '.$member->last_name]) }}')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="width:100%;padding:.4rem .75rem;font-size:.78rem;display:flex;align-items:center;gap:.3rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;cursor:pointer;transition:background .2s;"
                                onmouseover="this.style.background='rgba(230,57,70,.25)'"
                                onmouseout="this.style.background='rgba(230,57,70,.1)'">
                            <i class="fa-solid fa-trash"></i> {{ __('Supprimer') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- DONNÉES POUR LA MODALE (JSON hidden) --}}
            <script>
            STAFF_DATA[{{ $member->id }}] = {
                hotel_id:    {{ $member->hotel_id }},
                first_name:  "{{ addslashes($member->first_name) }}",
                last_name:   "{{ addslashes($member->last_name) }}",
                role:        "{{ addslashes($member->role) }}",
                phone:       "{{ addslashes($member->phone ?? '') }}",
                email:       "{{ addslashes($member->email ?? '') }}",
                hired_at:    "{{ $member->hired_at ? $member->hired_at->format('Y-m-d') : '' }}",
                status:      "{{ $member->status }}",
                salary:      {{ $member->salary }},
                photo:       "{{ $member->photo ? asset('storage/'.$member->photo) : '' }}",
                user_id:     {{ $member->user_id ?? 'null' }},
                permissions: @json($member->user?->permissions ?? []),
            };
            </script>
            @empty
            <div style="text-align:center;padding:2.5rem;color:var(--text-muted);">
                <i class="fa-solid fa-users-slash" style="font-size:2rem;opacity:.4;margin-bottom:.75rem;display:block;"></i>
                <p>{{ __('Aucun membre du personnel enregistré.') }}</p>
            </div>
            @endforelse
        </div>

        @if($staff->hasPages())
        <div style="margin-top:1.25rem;">{{ $staff->links() }}</div>
        @endif
    </div>

    {{-- FORMULAIRE AJOUT --}}
    <div class="card staff-form" style="align-self:start;">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-user-plus"></i> {{ __('Ajouter un membre') }}</span>
        </div>

        <form method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- PHOTO --}}
            <div class="form-group">
                <label><i class="fa-solid fa-camera" style="color:var(--ci-orange)"></i> {{ __('Photo') }}</label>
                <div style="display:flex;align-items:center;gap:1rem;padding:1rem;background:var(--bg-input);border:2px dashed var(--border);border-radius:14px;cursor:pointer;transition:border-color .2s;"
                     onclick="document.getElementById('photoInput').click()"
                     id="photoDropZone"
                     onmouseover="this.style.borderColor='var(--ci-orange-border)'"
                     onmouseout="this.style.borderColor='var(--border)'">
                    <div id="photoPreview" style="width:52px;height:52px;border-radius:50%;background:var(--ci-orange-dim);border:2px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);flex-shrink:0;overflow:hidden;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <div style="font-size:.85rem;font-weight:600;color:var(--text-primary);">{{ __('Cliquer pour choisir') }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);">{{ __('JPG, PNG — max 2 Mo') }}</div>
                    </div>
                </div>
                <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;"
                       onchange="previewPhoto(this)">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-hotel" style="color:var(--ci-orange)"></i> {{ __('Hôtel') }}</label>
                <select name="hotel_id" required class="form-select">
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label><i class="fa-solid fa-user" style="color:var(--ci-orange)"></i> {{ __('Prénom') }}</label>
                    <input type="text" name="first_name" required placeholder="Jean">
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label><i class="fa-solid fa-user" style="color:var(--ci-orange)"></i> {{ __('Nom') }}</label>
                    <input type="text" name="last_name" required placeholder="Kouassi">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-briefcase" style="color:var(--ci-orange)"></i> {{ __('Rôle / Poste') }}</label>
                <input type="text" name="role" required placeholder="{{ __('Ex : Réceptionniste') }}">
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label><i class="fa-solid fa-phone" style="color:var(--ci-orange)"></i> {{ __('Téléphone') }}</label>
                    <input type="text" name="phone" placeholder="+225 07 00 00 00 00">
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label><i class="fa-solid fa-envelope" style="color:var(--ci-orange)"></i> {{ __('Email') }}</label>
                    <input type="email" name="email" placeholder="nom@hotel.ci">
                </div>
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label><i class="fa-solid fa-calendar" style="color:var(--ci-orange)"></i> {{ __("Date d'embauche") }}</label>
                    <input type="date" name="hired_at">
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label><i class="fa-solid fa-toggle-on" style="color:var(--ci-orange)"></i> {{ __('Statut') }}</label>
                    <select name="status" required class="form-select">
                        <option value="actif">{{ __('Actif') }}</option>
                        <option value="inactif">{{ __('Inactif') }}</option>
                        <option value="congé">{{ __('En congé') }}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-money-bill-wave" style="color:var(--ci-orange)"></i> {{ __('Salaire (FCFA)') }}</label>
                <input type="number" name="salary" value="0" min="0" required>
            </div>

            {{-- COMPTE & PERMISSIONS --}}
            <div style="padding:1rem;background:var(--bg-input);border-radius:14px;border:1px solid var(--border);margin-bottom:1rem;">
                <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin-bottom:.4rem;">
                    <input type="checkbox" name="create_account" id="createAccountToggle" value="1"
                           onchange="document.getElementById('accountFields').style.display = this.checked ? 'block' : 'none'"
                           style="accent-color:var(--ci-orange);width:16px;height:16px;flex-shrink:0;">
                    <span style="font-weight:700;font-size:.88rem;"><i class="fa-solid fa-key" style="color:var(--ci-orange)"></i> {{ __('Créer un compte de connexion') }}</span>
                </label>
                <p style="font-size:.74rem;color:var(--text-muted);margin-left:1.6rem;">{{ __("L'employé pourra se connecter avec les droits que vous lui assignez.") }}</p>
            </div>

            <div id="accountFields" style="display:none;margin-bottom:1rem;">
                <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                    <div class="form-group" style="padding-right:.5rem;">
                        <label><i class="fa-solid fa-envelope" style="color:var(--ci-orange)"></i> {{ __('Email de connexion') }}</label>
                        <input type="email" name="account_email" placeholder="employe@hotel.ci">
                    </div>
                    <div class="form-group" style="padding-left:.5rem;">
                        <label><i class="fa-solid fa-lock" style="color:var(--ci-orange)"></i> {{ __('Mot de passe') }} <span style="color:var(--text-muted);font-size:.7rem;">{{ __('(défaut: password123)') }}</span></label>
                        <input type="password" name="account_pass" placeholder="{{ __('Min. 6 caractères') }}">
                    </div>
                </div>

                {{-- PERMISSIONS --}}
                <div>
                    <div style="font-size:.78rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.65rem;display:flex;align-items:center;gap:.5rem;">
                        <i class="fa-solid fa-shield-halved" style="color:var(--ci-orange)"></i>
                        {{ __("Droits d'accès de l'employé") }}
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem;">
                        @foreach(\App\Models\User::availablePermissions() as $key => $perm)
                        <label class="perm-label" style="display:flex;align-items:center;gap:.5rem;padding:.55rem .75rem;background:var(--bg-input);border:1px solid var(--border);border-radius:10px;cursor:pointer;transition:all .2s;">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                   style="accent-color:{{ $perm['color'] }};width:14px;height:14px;flex-shrink:0;">
                            <span style="color:{{ $perm['color'] }};font-size:.8rem;flex-shrink:0;width:16px;text-align:center;"><i class="fa-solid {{ $perm['icon'] }}"></i></span>
                            <span style="font-size:.78rem;font-weight:600;color:var(--text-primary);line-height:1.2;">{{ $perm['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div style="display:flex;gap:.5rem;margin-top:.6rem;">
                        <button type="button" onclick="toggleAllPerms(true)" class="btn-secondary" style="font-size:.74rem;padding:.3rem .65rem;">
                            <i class="fa-solid fa-check-double"></i> {{ __('Tout cocher') }}
                        </button>
                        <button type="button" onclick="toggleAllPerms(false)" class="btn-secondary" style="font-size:.74rem;padding:.3rem .65rem;">
                            <i class="fa-solid fa-xmark"></i> {{ __('Tout décocher') }}
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-user-plus"></i> {{ __('Ajouter le membre') }}
            </button>
        </form>
    </div>
</div>

<script>
function toggleAllPerms(check) {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = check);
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('editPhotoPreview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openEditModal(id) {
    const d = STAFF_DATA[id];
    if (!d) return;

    document.getElementById('editForm').action = UPDATE_ROUTE.replace('__ID__', id);
    document.getElementById('edit_hotel_id').value   = d.hotel_id;
    document.getElementById('edit_first_name').value = d.first_name;
    document.getElementById('edit_last_name').value  = d.last_name;
    document.getElementById('edit_role').value       = d.role;
    document.getElementById('edit_phone').value      = d.phone;
    document.getElementById('edit_email').value      = d.email;
    document.getElementById('edit_hired_at').value   = d.hired_at;
    document.getElementById('edit_status').value     = d.status;
    document.getElementById('edit_salary').value     = d.salary;

    const preview = document.getElementById('editPhotoPreview');
    if (d.photo) {
        preview.innerHTML = `<img src="${d.photo}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
    } else {
        preview.innerHTML = `<i class="fa-solid fa-user"></i>`;
    }

    // Compte & permissions
    const createSection = document.getElementById('editCreateAccountSection');
    const permsNote     = document.getElementById('editPermsNote');
    const cbs           = document.querySelectorAll('.edit-perm-cb');

    if (d.user_id) {
        // Employé a déjà un compte → juste les permissions
        createSection.style.display = 'none';
        permsNote.textContent = @json(__('(compte actif — modifications appliquées à la connexion suivante)'));
        cbs.forEach(cb => { cb.checked = Array.isArray(d.permissions) && d.permissions.includes(cb.value); });
    } else {
        // Pas de compte → proposer la création + cocher les droits souhaités
        createSection.style.display = 'block';
        permsNote.textContent = @json(__('(sera appliqué si un compte est créé)'));
        // Réinitialiser le toggle compte
        const toggle = document.getElementById('editCreateAccountToggle');
        if (toggle) { toggle.checked = false; }
        document.getElementById('editNewAccountFields').style.display = 'none';
        cbs.forEach(cb => cb.checked = false);
    }

    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function toggleEditPerms(check) {
    document.querySelectorAll('.edit-perm-cb').forEach(cb => cb.checked = check);
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });
</script>

{{-- MODALE MODIFICATION --}}
<div id="editModal" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(2,6,23,.7);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.5rem;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative;">

        {{-- TITRE --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">
                <i class="fa-solid fa-pen-to-square" style="color:var(--ci-orange)"></i> {{ __('Modifier le membre') }}
            </div>
            <button onclick="closeEditModal()" style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg-input);cursor:pointer;color:var(--text-secondary);display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- PHOTO --}}
            <div class="form-group">
                <label><i class="fa-solid fa-camera" style="color:var(--ci-orange)"></i> {{ __('Photo') }}</label>
                <div style="display:flex;align-items:center;gap:1rem;padding:.85rem 1rem;background:var(--bg-input);border:2px dashed var(--border);border-radius:14px;cursor:pointer;"
                     onclick="document.getElementById('editPhotoInput').click()">
                    <div id="editPhotoPreview" style="width:48px;height:48px;border-radius:50%;background:var(--ci-orange-dim);border:2px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--ci-orange);flex-shrink:0;overflow:hidden;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div style="font-size:.82rem;color:var(--text-secondary);">{{ __('Cliquer pour changer la photo') }}</div>
                </div>
                <input type="file" id="editPhotoInput" name="photo" accept="image/*" style="display:none;" onchange="previewEditPhoto(this)">
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label>{{ __('Prénom') }}</label>
                    <input type="text" id="edit_first_name" name="first_name" required>
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label>{{ __('Nom') }}</label>
                    <input type="text" id="edit_last_name" name="last_name" required>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('Hôtel') }}</label>
                <select id="edit_hotel_id" name="hotel_id" required class="form-select">
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('Rôle / Poste') }}</label>
                <input type="text" id="edit_role" name="role" required>
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label>{{ __('Téléphone') }}</label>
                    <input type="text" id="edit_phone" name="phone">
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label>{{ __('Email') }}</label>
                    <input type="email" id="edit_email" name="email">
                </div>
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.5rem;">
                    <label>{{ __("Date d'embauche") }}</label>
                    <input type="date" id="edit_hired_at" name="hired_at">
                </div>
                <div class="form-group" style="padding-left:.5rem;">
                    <label>{{ __('Statut') }}</label>
                    <select id="edit_status" name="status" required class="form-select">
                        <option value="actif">{{ __('Actif') }}</option>
                        <option value="inactif">{{ __('Inactif') }}</option>
                        <option value="congé">{{ __('En congé') }}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('Salaire (FCFA)') }}</label>
                <input type="number" id="edit_salary" name="salary" min="0" required>
            </div>

            {{-- SECTION COMPTE & DROITS --}}
            <div style="margin-top:.75rem;">
                <div style="height:1px;background:var(--border);margin-bottom:1rem;"></div>

                {{-- Option création de compte si pas encore de compte --}}
                <div id="editCreateAccountSection" style="display:none;margin-bottom:.85rem;">
                    <div style="padding:.85rem 1rem;background:rgba(247,127,0,.07);border:1px solid var(--ci-orange-border);border-radius:12px;">
                        <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;">
                            <input type="checkbox" name="create_account" id="editCreateAccountToggle" value="1"
                                   onchange="document.getElementById('editNewAccountFields').style.display = this.checked ? 'block' : 'none'"
                                   style="accent-color:var(--ci-orange);width:16px;height:16px;flex-shrink:0;">
                            <span style="font-weight:700;font-size:.88rem;">
                                <i class="fa-solid fa-key" style="color:var(--ci-orange)"></i>
                                {{ __('Créer un compte de connexion pour cet employé') }}
                            </span>
                        </label>
                        <div id="editNewAccountFields" style="display:none;margin-top:.75rem;">
                            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                                <div class="form-group" style="padding-right:.5rem;margin-bottom:.5rem;">
                                    <label style="font-size:.8rem;"><i class="fa-solid fa-envelope" style="color:var(--ci-orange)"></i> {{ __('Email de connexion') }}</label>
                                    <input type="email" name="account_email" id="edit_account_email" placeholder="employe@hotel.ci">
                                </div>
                                <div class="form-group" style="padding-left:.5rem;margin-bottom:.5rem;">
                                    <label style="font-size:.8rem;"><i class="fa-solid fa-lock" style="color:var(--ci-orange)"></i> {{ __('Mot de passe') }} <span style="color:var(--text-muted);font-size:.7rem;">{{ __('(défaut: password123)') }}</span></label>
                                    <input type="password" name="account_pass" placeholder="{{ __('Min. 6 caractères') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Droits d'accès (toujours affiché) --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.7rem;">
                    <div style="font-size:.8rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em;display:flex;align-items:center;gap:.5rem;">
                        <i class="fa-solid fa-shield-halved" style="color:var(--ci-orange)"></i>
                        {{ __("Droits d'accès") }}
                    </div>
                    <span id="editPermsNote" style="font-size:.72rem;color:var(--text-muted);font-style:italic;"></span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem;">
                    @foreach(\App\Models\User::availablePermissions() as $key => $perm)
                    <label style="display:flex;align-items:center;gap:.5rem;padding:.55rem .75rem;background:var(--bg-input);border:1px solid var(--border);border-radius:10px;cursor:pointer;transition:all .2s;"
                           onmouseover="this.style.borderColor='var(--ci-orange-border)'"
                           onmouseout="this.style.borderColor='var(--border)'">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}"
                               class="edit-perm-cb"
                               style="accent-color:{{ $perm['color'] }};width:14px;height:14px;flex-shrink:0;">
                        <span style="color:{{ $perm['color'] }};font-size:.8rem;flex-shrink:0;width:16px;text-align:center;"><i class="fa-solid {{ $perm['icon'] }}"></i></span>
                        <span style="font-size:.78rem;font-weight:600;color:var(--text-primary);line-height:1.2;">{{ $perm['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                <div style="display:flex;gap:.5rem;margin-top:.6rem;">
                    <button type="button" onclick="toggleEditPerms(true)" class="btn-secondary" style="font-size:.74rem;padding:.3rem .65rem;">
                        <i class="fa-solid fa-check-double"></i> {{ __('Tout cocher') }}
                    </button>
                    <button type="button" onclick="toggleEditPerms(false)" class="btn-secondary" style="font-size:.74rem;padding:.3rem .65rem;">
                        <i class="fa-solid fa-xmark"></i> {{ __('Tout décocher') }}
                    </button>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:1rem;">
                <button type="button" onclick="closeEditModal()" class="btn-secondary">{{ __('Annuler') }}</button>
                <button type="submit" class="btn">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
