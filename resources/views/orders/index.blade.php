@extends('layouts.app')

@section('content')

{{-- ALERTS --}}
@if(session('success'))
<div class="alert-success" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
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
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-bowl-food"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Gestion des commandes') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Suivi des commandes restaurant par hôtel.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- STATS (6 cadres) --}}
<div class="orders-stats-grid" style="margin-bottom:1.5rem;">

    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:var(--ci-orange);">
            <i class="fa-solid fa-coins"></i>
        </div>
        <div style="font-size:1.35rem;font-weight:800;color:var(--text-primary);">{{ number_format($statCA, 0, ',', ' ') }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">FCFA — {{ __('CA') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-green-dim);border:1px solid var(--ci-green-border);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:var(--ci-green);">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div style="font-size:1.35rem;font-weight:800;color:var(--text-primary);">{{ $statTotal }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Total') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.28);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#fbbf24;">
            <i class="fa-solid fa-fire-burner"></i>
        </div>
        <div style="font-size:1.35rem;font-weight:800;color:var(--text-primary);">{{ $statPrep }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('En préparation') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:var(--ci-orange);">
            <i class="fa-solid fa-bell-concierge"></i>
        </div>
        <div style="font-size:1.35rem;font-weight:800;color:var(--text-primary);">{{ $statPret }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Prêt') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-green-dim);border:1px solid var(--ci-green-border);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:var(--ci-green);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div style="font-size:1.35rem;font-weight:800;color:var(--text-primary);">{{ $statLivre }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Livrées') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.25rem .75rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(230,57,70,.1);border:1px solid rgba(230,57,70,.22);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;font-size:1.1rem;color:#fca5a5;">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <div style="font-size:1.35rem;font-weight:800;color:var(--text-primary);">{{ $statAnnul }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-top:.2rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Annulées') }}</div>
    </div>

</div>

{{-- DEUX COLONNES : FORMULAIRE + APERÇU --}}
<div class="orders-form-layout" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;align-items:start;">

    {{-- FORMULAIRE --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-plus"></i> {{ __('Nouvelle commande') }}</span>
        </div>
        <form method="POST" action="{{ route('orders.store') }}" id="order-form">
            @csrf
            <div class="form-group">
                <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
                <select name="hotel_id" id="sel-hotel" required>
                    <option value="">{{ __('— Sélectionner un hôtel —') }}</option>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('Article du menu') }} <span style="color:#e63946;">*</span></label>
                <select name="menu_item_id" id="sel-item" required>
                    <option value="">{{ __('— Sélectionner un article —') }}</option>
                    @foreach($menuItems as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->price }}" data-hotel="{{ $item->hotel_id }}"
                            {{ old('menu_item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->emoji ? $item->emoji . ' ' : '' }}{{ $item->name }} ({{ number_format($item->price, 0, ',', ' ') }} FCFA)
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Quantité') }} <span style="color:#e63946;">*</span></label>
                    <input type="number" name="quantity" id="inp-qty" value="{{ old('quantity', 1) }}" min="1" max="99" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Statut') }} <span style="color:#e63946;">*</span></label>
                    <select name="status" required>
                        @foreach(['en préparation','prêt','livré','annulé'] as $s)
                            <option value="{{ $s }}" {{ old('status','en préparation') === $s ? 'selected' : '' }}>{{ ucfirst(__($s)) }}</option>
                        @endforeach
                    </select>
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
                <label>{{ __('Notes') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                <textarea name="notes" rows="2" placeholder="{{ __('Instructions spéciales, allergies…') }}" style="resize:vertical;">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-paper-plane"></i> {{ __('Enregistrer la commande') }}
            </button>
        </form>
    </div>

    {{-- APERÇU + LÉGENDE --}}
    <div>
        <div class="card" style="border:1px solid var(--ci-orange-border);background:var(--ci-orange-dim);">
            <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--ci-orange);margin-bottom:1rem;">
                <i class="fa-solid fa-calculator"></i> {{ __('Aperçu du total') }}
            </div>
            <div id="preview-item" style="font-size:.95rem;color:var(--text-secondary);margin-bottom:.5rem;">{{ __('— Sélectionnez un article —') }}</div>
            <div style="display:flex;align-items:baseline;gap:.5rem;margin-top:1rem;">
                <span id="preview-total" style="font-size:2.2rem;font-weight:800;color:var(--ci-orange);">0</span>
                <span style="font-size:1rem;color:var(--text-muted);font-weight:600;">FCFA</span>
            </div>
            <div id="preview-calc" style="font-size:.78rem;color:var(--text-muted);margin-top:.25rem;"></div>
        </div>
        <div class="card" style="margin-top:1.25rem;">
            <div style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:.85rem;">
                <i class="fa-solid fa-tag"></i> {{ __('Légende des statuts') }}
            </div>
            <div style="display:flex;flex-direction:column;gap:.55rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-warning"><i class="fa-solid fa-fire-burner"></i> {{ __('En préparation') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Commande reçue, en cours') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-primary"><i class="fa-solid fa-bell-concierge"></i> {{ __('Prêt') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Prêt à être servi') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> {{ __('Livré') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Remis au client') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> {{ __('Annulé') }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">{{ __('Commande annulée') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- LISTE DES COMMANDES --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-list-ul"></i> {{ __('Liste des commandes') }}</span>
        <span class="badge badge-secondary">{{ $orders->total() }}</span>
    </div>

    @forelse($orders as $o)
    @php
        $statusMap = [
            'en préparation' => ['badge-warning', 'fa-fire-burner',    __('En préparation')],
            'prêt'           => ['badge-primary', 'fa-bell-concierge', __('Prêt')],
            'livré'          => ['badge-success', 'fa-circle-check',   __('Livré')],
            'annulé'         => ['badge-danger',  'fa-xmark',          __('Annulé')],
        ];
        [$cls, $ico, $lbl] = $statusMap[$o->status] ?? ['badge-secondary', 'fa-circle', ucfirst($o->status)];
    @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;transition:border-color .2s;"
         onmouseover="this.style.borderColor='var(--ci-orange-border)'"
         onmouseout="this.style.borderColor='var(--border)'">

        <div style="display:flex;align-items:center;gap:.9rem;flex:1;min-width:0;">
            <div style="width:44px;height:44px;border-radius:12px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                {{ optional($o->menuItem)->emoji ?: '🍽️' }}
            </div>
            <div style="min-width:0;">
                <div style="font-weight:700;color:var(--text-primary);font-size:.95rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ optional($o->menuItem)->name ?? '—' }}
                    <span style="font-weight:400;color:var(--text-muted);font-size:.82rem;">× {{ $o->quantity }}</span>
                </div>
                <div style="font-size:.78rem;color:var(--text-muted);margin-top:2px;">
                    <i class="fa-solid fa-hotel" style="opacity:.6;"></i> {{ optional($o->hotel)->name ?? '—' }}
                    @if($o->client)&nbsp;·&nbsp;<i class="fa-solid fa-user" style="opacity:.6;"></i> {{ $o->client->first_name }} {{ $o->client->last_name }}@endif
                    &nbsp;·&nbsp;{{ $o->created_at?->format('d/m H:i') ?? '—' }}
                </div>
                @if($o->notes)
                <div style="font-size:.75rem;color:var(--text-secondary);margin-top:3px;font-style:italic;">"{{ Str::limit($o->notes, 55) }}"</div>
                @endif
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:.65rem;flex-shrink:0;">
            <div style="font-weight:800;font-size:.95rem;color:var(--ci-green);white-space:nowrap;">
                {{ number_format($o->total, 0, ',', ' ') }} FCFA
            </div>
            <span class="badge {{ $cls }}">
                <i class="fa-solid {{ $ico }}"></i>&nbsp;{{ $lbl }}
            </span>
            {{-- Bouton Modifier --}}
            <button type="button" class="btn-secondary" style="padding:.4rem .75rem;font-size:.78rem;"
                onclick="openEdit({{ $o->id }}, {{ $o->hotel_id }}, {{ $o->menu_item_id }}, {{ $o->quantity }}, '{{ addslashes($o->status) }}', {{ $o->client_id ?? 'null' }}, '{{ addslashes($o->notes ?? '') }}')">
                <i class="fa-solid fa-pen"></i>
            </button>
            {{-- Bouton Supprimer --}}
            <form method="POST" action="{{ route('orders.destroy', $o->id) }}"
                  onsubmit="return confirm('{{ __('Supprimer cette commande ?') }}')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="display:inline-flex;align-items:center;padding:.4rem .75rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;font-size:.78rem;font-family:inherit;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='rgba(230,57,70,.25)'"
                        onmouseout="this.style.background='rgba(230,57,70,.1)'">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3.5rem;color:var(--text-muted);">
        <i class="fa-solid fa-bowl-food" style="font-size:2.5rem;opacity:.35;margin-bottom:.75rem;display:block;"></i>
        <p style="font-weight:500;">{{ __('Aucune commande enregistrée.') }}</p>
        <p style="font-size:.82rem;margin-top:.25rem;">{{ __('Utilisez le formulaire ci-dessus pour créer la première commande.') }}</p>
    </div>
    @endforelse

    @if($orders->hasPages())
    <div style="margin-top:1.25rem;display:flex;justify-content:center;">{{ $orders->links() }}</div>
    @endif
</div>

{{-- MODAL ÉDITION --}}
<div id="edit-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.7);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.4);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div style="font-weight:700;font-size:1.1rem;color:var(--ci-orange);">
                <i class="fa-solid fa-pen"></i> {{ __('Modifier la commande') }}
            </div>
            <button onclick="closeEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="edit-form" method="POST" action="">
            @csrf @method('PUT')
            <div class="form-group">
                <label>{{ __('Hôtel') }} <span style="color:#e63946;">*</span></label>
                <select name="hotel_id" id="edit-hotel" required>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('Article du menu') }} <span style="color:#e63946;">*</span></label>
                <select name="menu_item_id" id="edit-item" required>
                    @foreach($menuItems as $item)
                        <option value="{{ $item->id }}" data-hotel="{{ $item->hotel_id }}">
                            {{ $item->emoji ? $item->emoji . ' ' : '' }}{{ $item->name }} ({{ number_format($item->price, 0, ',', ' ') }} FCFA)
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Quantité') }} <span style="color:#e63946;">*</span></label>
                    <input type="number" name="quantity" id="edit-qty" min="1" max="99" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>{{ __('Statut') }} <span style="color:#e63946;">*</span></label>
                    <select name="status" id="edit-status" required>
                        @foreach(['en préparation','prêt','livré','annulé'] as $s)
                            <option value="{{ $s }}">{{ ucfirst(__($s)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top:1rem;">
                <label>{{ __('Client') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                <select name="client_id" id="edit-client">
                    <option value="">{{ __('— Aucun client associé —') }}</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->first_name }} {{ $cl->last_name }}@if($cl->phone) — {{ $cl->phone }}@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('Notes') }} <span style="font-weight:400;color:var(--text-muted);font-size:.8rem;">{{ __('(optionnel)') }}</span></label>
                <textarea name="notes" id="edit-notes" rows="2" style="resize:vertical;"></textarea>
            </div>
            <div style="display:flex;gap:.75rem;margin-top:.5rem;">
                <button type="button" onclick="closeEdit()" class="btn-secondary" style="flex:1;justify-content:center;">
                    {{ __('Annuler') }}
                </button>
                <button type="submit" class="btn" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Stats : 6 colonnes sur desktop, 2 sur mobile */
.orders-stats-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 1rem;
}
@media (max-width: 767px) {
    .orders-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .orders-form-layout { grid-template-columns: 1fr !important; }
}
</style>

<script>
(function () {
    /* ── Formulaire création ── */
    const selHotel = document.getElementById('sel-hotel');
    const selItem  = document.getElementById('sel-item');
    const inpQty   = document.getElementById('inp-qty');
    const prevItem = document.getElementById('preview-item');
    const prevTot  = document.getElementById('preview-total');
    const prevCalc = document.getElementById('preview-calc');

    function fmt(n) { return n.toLocaleString('fr-FR'); }

    function updatePreview() {
        const opt   = selItem.options[selItem.selectedIndex];
        const price = parseInt(opt?.dataset?.price || 0);
        const qty   = parseInt(inpQty.value || 1);
        const total = price * qty;
        if (price > 0) {
            prevItem.textContent = opt.text.replace(/\s*\(.*\)$/, '');
            prevTot.textContent  = fmt(total);
            prevCalc.textContent = fmt(price) + ' FCFA × ' + qty;
        } else {
            prevItem.textContent = @json(__('— Sélectionnez un article —'));
            prevTot.textContent  = '0';
            prevCalc.textContent = '';
        }
    }

    function filterItems() {
        const hotelId = selHotel.value;
        Array.from(selItem.options).forEach(opt => {
            if (!opt.value) return;
            opt.hidden = hotelId && opt.dataset.hotel !== hotelId;
        });
        const cur = selItem.options[selItem.selectedIndex];
        if (cur && cur.hidden) selItem.value = '';
        updatePreview();
    }

    selHotel.addEventListener('change', filterItems);
    selItem.addEventListener('change', updatePreview);
    inpQty.addEventListener('input', updatePreview);
    filterItems();
    updatePreview();

    /* ── Modal édition ── */
    const modal     = document.getElementById('edit-modal');
    const editForm  = document.getElementById('edit-form');
    const editHotel = document.getElementById('edit-hotel');
    const editItem  = document.getElementById('edit-item');

    function filterEditItems() {
        const hotelId = editHotel.value;
        Array.from(editItem.options).forEach(opt => {
            opt.hidden = hotelId && opt.dataset.hotel !== hotelId;
        });
    }
    editHotel.addEventListener('change', filterEditItems);

    window.openEdit = function(id, hotelId, menuItemId, qty, status, clientId, notes) {
        editForm.action = '/orders/' + id;
        editHotel.value = hotelId;
        filterEditItems();
        editItem.value   = menuItemId;
        document.getElementById('edit-qty').value    = qty;
        document.getElementById('edit-status').value = status;
        document.getElementById('edit-client').value = clientId || '';
        document.getElementById('edit-notes').value  = notes;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeEdit = function() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeEdit();
    });
})();
</script>

@endsection
