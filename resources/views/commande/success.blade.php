@extends('layouts.app')

@section('content')

@php
$payMap = [
    'carte_bancaire' => ['Carte bancaire', '#818cf8', 'fa-credit-card'],
    'orange_money'   => ['Orange Money',   '#fb923c', 'fa-mobile-screen'],
    'mtn_momo'       => ['MTN MoMo',       '#fbbf24', 'fa-mobile-screen'],
    'moov_money'     => ['Moov Money',     '#60a5fa', 'fa-mobile-screen'],
    'wave'           => ['Wave',           '#38bdf8', 'fa-wave-square'],
    'mobile_money'   => ['Mobile Money',   '#F77F00', 'fa-mobile-screen'],
];
$pm    = $payMap[$commande->mode_paiement ?? ''] ?? [__('Non renseigné'), '#94a3b8', 'fa-circle-question'];
$pmLabel = $pm[0]; $pmColor = $pm[1]; $pmIcon = $pm[2];
@endphp

{{-- ÉTAPES (toutes validées) --}}
<div style="display:flex;align-items:center;justify-content:center;margin-bottom:2rem;max-width:520px;margin-left:auto;margin-right:auto;" class="no-print">
    @foreach([['fa-cart-shopping',__('Panier')],['fa-lock',__('Paiement')],['fa-circle-check',__('Confirmation')]] as $i=>[$ico,$lbl])
    <div style="display:flex;align-items:center;flex:1;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:.3rem;flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;background:#009A44;color:#fff;box-shadow:0 0 0 4px rgba(0,154,68,.2);">
                <i class="fa-solid {{ $ico }}"></i>
            </div>
            <span style="font-size:.72rem;font-weight:600;color:#4ade80;">{{ $lbl }}</span>
        </div>
        @if($i < 2)
        <div style="height:1px;width:32px;flex-shrink:0;margin-bottom:1.3rem;background:#009A44;"></div>
        @endif
    </div>
    @endforeach
</div>

{{-- ── HERO SUCCÈS ───────────────────────────────────────────────────────── --}}
<div class="card" style="text-align:center;padding:2.5rem 2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
    {{-- fond décoratif --}}
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 50% 0%,rgba(0,154,68,.12),transparent);pointer-events:none;"></div>

    <div style="position:relative;">
        {{-- icône animée --}}
        <div style="width:88px;height:88px;border-radius:50%;background:rgba(0,154,68,.12);border:2px solid rgba(0,154,68,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;animation:popIn .5s cubic-bezier(.34,1.56,.64,1) both;">
            <i class="fa-solid fa-circle-check" style="font-size:2.4rem;color:#4ade80;"></i>
        </div>

        <div style="font-size:1.7rem;font-weight:800;color:#f1f5f9;margin-bottom:.5rem;">{{ __('Paiement réussi !') }}</div>
        <div style="font-size:.9rem;color:#94a3b8;max-width:380px;margin:0 auto 1.25rem;">
            {{ __('Votre commande a bien été enregistrée et votre paiement confirmé.') }}
        </div>

        {{-- Montant --}}
        <div style="display:inline-flex;align-items:center;gap:.75rem;background:rgba(0,154,68,.1);border:1px solid rgba(0,154,68,.25);border-radius:999px;padding:.6rem 1.5rem;">
            <span style="font-size:.82rem;color:#86efac;">{{ __('Montant payé') }}</span>
            <span style="font-size:1.5rem;font-weight:800;color:#4ade80;">{{ number_format($commande->total, 0, ',', ' ') }}&nbsp;FCFA</span>
        </div>
    </div>
</div>

{{-- ── GRILLE INFOS ─────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">

    {{-- N° commande --}}
    <div class="card" style="padding:1.1rem 1.25rem;display:flex;align-items:center;gap:.85rem;">
        <span style="width:40px;height:40px;border-radius:10px;background:rgba(247,127,0,.12);border:1px solid rgba(247,127,0,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-receipt" style="color:#F77F00;"></i>
        </span>
        <div>
            <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Commande') }}</div>
            <div style="font-size:1.1rem;font-weight:800;color:#F77F00;">#{{ $commande->id }}</div>
        </div>
    </div>

    {{-- Méthode de paiement --}}
    <div class="card" style="padding:1.1rem 1.25rem;display:flex;align-items:center;gap:.85rem;">
        <span style="width:40px;height:40px;border-radius:10px;background:{{ $pmColor }}22;border:1px solid {{ $pmColor }}44;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid {{ $pmIcon }}" style="color:{{ $pmColor }};"></i>
        </span>
        <div>
            <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Méthode de paiement') }}</div>
            <div style="font-size:.92rem;font-weight:700;color:#e2e8f0;">{{ $pmLabel }}</div>
            @if($commande->payment_reference)
            <div style="font-size:.8rem;font-family:monospace;color:{{ $pmColor }};margin-top:.2rem;font-weight:600;">{{ $commande->payment_reference }}</div>
            @endif
        </div>
    </div>

    {{-- Date --}}
    <div class="card" style="padding:1.1rem 1.25rem;display:flex;align-items:center;gap:.85rem;">
        <span style="width:40px;height:40px;border-radius:10px;background:rgba(148,163,184,.08);border:1px solid rgba(148,163,184,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-calendar-check" style="color:#94a3b8;"></i>
        </span>
        <div>
            <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Date') }}</div>
            <div style="font-size:.88rem;font-weight:600;color:#e2e8f0;">{{ $commande->created_at?->format('d/m/Y') ?? '—' }}</div>
            <div style="font-size:.75rem;color:#64748b;">{{ $commande->created_at?->format('H:i') ?? '' }}</div>
        </div>
    </div>
</div>

{{-- ── RÉFÉRENCES TRANSACTION ───────────────────────────────────────────── --}}
<div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:1.1rem 1.5rem;margin-bottom:1.5rem;display:flex;flex-wrap:wrap;gap:1.25rem;align-items:center;justify-content:space-between;">

    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;flex:1;">
        @if($commande->transaction_id)
        <div style="display:flex;align-items:center;gap:.6rem;">
            <i class="fa-solid fa-fingerprint" style="color:#64748b;font-size:1rem;"></i>
            <div>
                <div style="font-size:.68rem;color:#475569;text-transform:uppercase;letter-spacing:.07em;font-weight:600;">{{ __('Réf. transaction') }}</div>
                <div style="font-family:monospace;font-size:.85rem;color:#94a3b8;margin-top:.1rem;">{{ $commande->transaction_id }}</div>
            </div>
        </div>
        @endif

        @if($commande->payment_reference)
        <div style="display:flex;align-items:center;gap:.6rem;">
            <i class="fa-solid fa-{{ $commande->mode_paiement === 'carte_bancaire' ? 'credit-card' : 'mobile-screen' }}" style="color:{{ $pmColor }};font-size:1rem;"></i>
            <div>
                <div style="font-size:.68rem;color:#475569;text-transform:uppercase;letter-spacing:.07em;font-weight:600;">{{ __('Compte débité') }}</div>
                <div style="font-size:.9rem;font-weight:700;color:#e2e8f0;margin-top:.1rem;font-family:monospace;">{{ $commande->payment_reference }}</div>
            </div>
        </div>
        @endif
    </div>

    <span style="background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.3);color:#4ade80;border-radius:999px;padding:.3rem .9rem;font-size:.75rem;font-weight:700;display:flex;align-items:center;gap:.35rem;white-space:nowrap;">
        <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;"></span> {{ __('Validé') }}
    </span>
</div>

{{-- ── DÉTAIL ARTICLES ──────────────────────────────────────────────────── --}}
<div class="card" style="padding:0;overflow:hidden;margin-bottom:1.5rem;">
    <div style="padding:1.1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-weight:700;color:#e2e8f0;font-size:.92rem;">
            <i class="fa-solid fa-boxes-stacked" style="color:#F77F00;margin-right:.4rem;"></i> {{ __('Articles commandés') }}
        </span>
        <a href="{{ route('commande.facture', $commande->id) }}"
           style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(247,127,0,.12);border:1px solid rgba(247,127,0,.3);color:#fed7aa;border-radius:999px;padding:.3rem .85rem;font-size:.75rem;font-weight:600;text-decoration:none;"
           onmouseover="this.style.background='rgba(247,127,0,.22)'" onmouseout="this.style.background='rgba(247,127,0,.12)'">
            <i class="fa-solid fa-download"></i> {{ __('Facture PDF') }}
        </a>
    </div>

    <div class="table-responsive">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:rgba(255,255,255,.025);">
                <th style="padding:.7rem 1.5rem;text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Produit') }}</th>
                <th style="padding:.7rem 1rem;text-align:center;font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Qté') }}</th>
                <th style="padding:.7rem 1.2rem;text-align:right;font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('P.U.') }}</th>
                <th style="padding:.7rem 1.5rem;text-align:right;font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Sous-total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->lignes as $ligne)
            <tr style="border-top:1px solid rgba(255,255,255,.05);">
                <td style="padding:.9rem 1.5rem;font-weight:600;color:#e2e8f0;font-size:.88rem;">
                    {{ $ligne->product?->name ?? __('Produit #:id', ['id' => $ligne->product_id]) }}
                </td>
                <td style="padding:.9rem 1rem;text-align:center;">
                    <span style="background:rgba(255,255,255,.07);border-radius:7px;padding:.2rem .55rem;font-size:.8rem;color:#94a3b8;font-weight:700;">× {{ $ligne->quantite }}</span>
                </td>
                <td style="padding:.9rem 1.2rem;text-align:right;color:#64748b;font-size:.85rem;">
                    {{ number_format($ligne->prix, 0, ',', ' ') }}&nbsp;FCFA
                </td>
                <td style="padding:.9rem 1.5rem;text-align:right;font-weight:700;color:#4ade80;font-size:.9rem;">
                    {{ number_format($ligne->prix * $ligne->quantite, 0, ',', ' ') }}&nbsp;<span style="font-size:.72rem;opacity:.7;">FCFA</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:rgba(255,255,255,.03);border-top:1px solid rgba(255,255,255,.09);">
                <td colspan="3" style="padding:1rem 1.2rem;text-align:right;font-size:.85rem;color:#94a3b8;font-weight:600;">{{ __('Total payé') }}</td>
                <td style="padding:1rem 1.5rem;text-align:right;font-size:1.15rem;font-weight:800;color:#4ade80;">
                    {{ number_format($commande->total, 0, ',', ' ') }}&nbsp;<span style="font-size:.78rem;">FCFA</span>
                </td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>

{{-- ── ACTIONS ──────────────────────────────────────────────────────────── --}}
<div style="display:flex;gap:.85rem;justify-content:center;flex-wrap:wrap;margin-bottom:1.5rem;" class="no-print">
    <a href="{{ route('boutique.clientboutique') }}"
       style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:12px;border:1px solid rgba(255,255,255,.1);color:#94a3b8;text-decoration:none;font-size:.88rem;font-weight:600;transition:all .15s;"
       onmouseover="this.style.borderColor='rgba(255,255,255,.22)';this.style.color='#e2e8f0'" onmouseout="this.style.borderColor='rgba(255,255,255,.1)';this.style.color='#94a3b8'">
        <i class="fa-solid fa-store"></i> {{ __('Retour à la boutique') }}
    </a>

    @auth
    <a href="{{ route('client.commandes') }}"
       style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:12px;border:1px solid rgba(255,255,255,.1);color:#94a3b8;text-decoration:none;font-size:.88rem;font-weight:600;transition:all .15s;"
       onmouseover="this.style.borderColor='rgba(255,255,255,.22)';this.style.color='#e2e8f0'" onmouseout="this.style.borderColor='rgba(255,255,255,.1)';this.style.color='#94a3b8'">
        <i class="fa-solid fa-clock-rotate-left"></i> {{ __('Mes commandes') }}
    </a>
    @endauth

    <button onclick="window.print()"
       style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:12px;border:none;background:#F77F00;color:#fff;font-size:.88rem;font-weight:700;cursor:pointer;transition:all .15s;"
       onmouseover="this.style.background='#e07000'" onmouseout="this.style.background='#F77F00'">
        <i class="fa-solid fa-print"></i> {{ __('Imprimer') }}
    </button>
</div>

@push('styles')
<style>
@keyframes popIn {
    from { opacity:0; transform:scale(.5); }
    to   { opacity:1; transform:scale(1); }
}
@media (max-width: 640px) {
    div[style*="grid-template-columns:repeat(3"] { grid-template-columns: 1fr !important; }
}
@media print {
    .no-print, .sidebar, .topbar { display:none !important; }
    .card { box-shadow:none !important; border:1px solid #e2e8f0 !important; }
    body { background:#fff !important; color:#000 !important; }
}
</style>
@endpush

@endsection
