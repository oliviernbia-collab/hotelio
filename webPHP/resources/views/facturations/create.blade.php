@extends('layouts.app')

@section('content')
<div class="content">
    <div class="card" style="max-width:780px;">
        <div class="card-title">
            <h2 style="margin:0;font-size:1.2rem;">{{ __('Nouvelle facture') }}</h2>
            <a href="{{ route('facturations.index') }}" class="btn-secondary" style="font-size:.85rem;padding:.5rem 1rem;border-radius:999px;display:inline-flex;align-items:center;gap:.4rem;">
                <i class="fa-solid fa-arrow-left"></i> {{ __('Retour') }}
            </a>
        </div>

        @if($errors->any())
        <div style="background:rgba(248,113,113,.13);border:1px solid rgba(248,113,113,.3);color:#fecaca;border-radius:12px;padding:1rem;margin-bottom:1rem;">
            <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('facturations.store') }}" id="factureForm">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                <div class="form-group">
                    <label>{{ __('Client *') }}</label>
                    <input type="text" name="client" placeholder="{{ __('Nom du client') }}" required value="{{ old('client') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('Hôtel') }}</label>
                    <input type="text" name="hotel" placeholder="{{ __("Nom de l'hôtel") }}" value="{{ old('hotel') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('Mode de paiement') }}</label>
                    <select name="mode_paiement">
                        <option value="">{{ __('— Sélectionner —') }}</option>
                        <option value="Espèces">{{ __('Espèces') }}</option>
                        <option value="Carte bancaire">{{ __('Carte bancaire') }}</option>
                        <option value="Mobile Money (Orange)">Mobile Money (Orange)</option>
                        <option value="Mobile Money (MTN)">Mobile Money (MTN)</option>
                        <option value="Mobile Money (Moov)">Mobile Money (Moov)</option>
                        <option value="Virement bancaire">{{ __('Virement bancaire') }}</option>
                        <option value="Chèque">{{ __('Chèque') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('Notes / Conditions') }}</label>
                    <input type="text" name="notes" placeholder="{{ __('ex: Acompte 50% reçu…') }}" value="{{ old('notes') }}">
                </div>
            </div>

            {{-- Lignes de prestations --}}
            <div style="background:rgba(247,127,0,.05);border:1px dashed rgba(247,127,0,.3);border-radius:16px;padding:1.25rem;margin-bottom:1rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                    <h4 style="margin:0;color:#cbd5e1;font-size:.95rem;">{{ __('Détail des prestations *') }}</h4>
                    <button type="button" class="btn" style="font-size:.8rem;padding:.5rem 1rem;" onclick="addItem()">
                        <i class="fa-solid fa-plus"></i> {{ __('Ajouter ligne') }}
                    </button>
                </div>

                <div style="display:grid;grid-template-columns:3fr 1fr 1.5fr;gap:.5rem;margin-bottom:.4rem;font-size:.78rem;color:#64748b;text-transform:uppercase;letter-spacing:.06em;padding:0 .4rem;">
                    <span>{{ __('Désignation') }}</span><span>{{ __('Qté') }}</span><span>{{ __('Prix unitaire (FCFA)') }}</span>
                </div>

                <div id="items">
                    <div class="item-row" style="display:grid;grid-template-columns:3fr 1fr 1.5fr;gap:.5rem;margin-bottom:.5rem;">
                        <input name="items[0][designation]" placeholder="{{ __('Chambre, service, repas…') }}" required>
                        <input name="items[0][quantite]" type="number" min="1" value="1" required>
                        <input name="items[0][prix]" type="number" min="0" placeholder="0" required>
                    </div>
                </div>
            </div>

            {{-- Aperçu totaux --}}
            <div style="display:flex;justify-content:flex-end;margin-bottom:1.5rem;">
                <div style="width:280px;background:rgba(10,18,36,.9);border:1px solid rgba(247,127,0,.2);border-radius:16px;padding:1rem;">
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.85rem;color:#94a3b8;border-bottom:1px solid rgba(255,255,255,.06);">
                        <span>{{ __('Sous-total HT') }}</span><span id="previewHT">0 FCFA</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.85rem;color:#94a3b8;border-bottom:1px solid rgba(255,255,255,.06);">
                        <span>{{ __('TVA (18%)') }}</span><span id="previewTVA">0 FCFA</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.85rem;color:#cbd5e1;border-bottom:1px solid rgba(255,255,255,.06);">
                        <span>{{ __('Taxe Hôtelière — TH (2%)') }}</span><span id="previewTH">0 FCFA</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:.6rem 0 0;font-size:1rem;font-weight:700;color:#F77F00;">
                        <span>{{ __('Total TTC') }}</span><span id="previewTTC">0 FCFA</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn" style="width:100%;">
                <i class="fa-solid fa-file-invoice"></i> {{ __('Générer la facture') }}
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let idx = 1;

function fmt(n) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
}

function recalc() {
    let ht = 0;
    document.querySelectorAll('#items .item-row').forEach(row => {
        const q = parseFloat(row.querySelector('[name$="[quantite]"]')?.value) || 0;
        const p = parseFloat(row.querySelector('[name$="[prix]"]')?.value) || 0;
        ht += q * p;
    });
    const tva = ht * 0.18;
    const th  = ht * 0.02;
    document.getElementById('previewHT').textContent  = fmt(ht);
    document.getElementById('previewTVA').textContent = fmt(tva);
    document.getElementById('previewTH').textContent  = fmt(th);
    document.getElementById('previewTTC').textContent = fmt(ht + tva + th);
}

function addItem() {
    const row = document.createElement('div');
    row.className = 'item-row';
    row.style.cssText = 'display:grid;grid-template-columns:3fr 1fr 1.5fr;gap:.5rem;margin-bottom:.5rem;';
    row.innerHTML = `
        <input name="items[${idx}][designation]" placeholder="{{ __('Désignation') }}" required style="background:rgba(148,163,184,.07);border:1px solid rgba(247,127,0,.18);color:#e2e8f0;border-radius:18px;padding:.9rem 1rem;outline:none;">
        <input name="items[${idx}][quantite]" type="number" min="1" value="1" required oninput="recalc()" style="background:rgba(148,163,184,.07);border:1px solid rgba(247,127,0,.18);color:#e2e8f0;border-radius:18px;padding:.9rem 1rem;outline:none;">
        <input name="items[${idx}][prix]" type="number" min="0" placeholder="0" required oninput="recalc()" style="background:rgba(148,163,184,.07);border:1px solid rgba(247,127,0,.18);color:#e2e8f0;border-radius:18px;padding:.9rem 1rem;outline:none;">
    `;
    document.getElementById('items').appendChild(row);
    idx++;
}

document.getElementById('items').addEventListener('input', recalc);
</script>
@endpush
@endsection
