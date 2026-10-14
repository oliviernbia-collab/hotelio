@extends('layouts.app')
@section('title', __('Paiement de la réservation'))

@section('content')

@php
$methodsMap = [
    'Orange Money'     => ['img'=>'images/payment-methods/orange-money.svg', 'sub'=>__('Paiement mobile sécurisé'),   'bg'=>'#FF6600', 'slug'=>'orange-money',     'gateway'=>true],
    'MTN Mobile Money' => ['img'=>'images/payment-methods/mtn-momo.png',     'sub'=>__('Paiement mobile instantané'), 'bg'=>'#003366', 'slug'=>'mtn-mobile-money', 'gateway'=>true],
    'Wave'             => ['img'=>'images/payment-methods/wave.png',        'sub'=>__('Transfert rapide 0 frais'),   'bg'=>'#1DC8E5', 'slug'=>'wave',             'gateway'=>true],
    'Moov Money'       => ['img'=>'images/payment-methods/moov.png',        'sub'=>__('Paiement mobile Moov'),       'bg'=>'#0B5FA5', 'slug'=>'moov-money',       'gateway'=>true],
    'DJAMO'            => ['badge'=>'DJAMO',  'sub'=>__('Compte et carte DJAMO'),       'bg'=>'#6C2BD9', 'fg'=>'#fff',    'slug'=>'djamo',            'gateway'=>true],
    'Espèces'          => ['fa'=>'fa-money-bill-wave',  'sub'=>__('Payer à la réception'), 'bg'=>'#15803d', 'fg'=>'#fff', 'slug'=>'especes',  'gateway'=>false],
    'Virement'         => ['fa'=>'fa-building-columns', 'sub'=>'IBAN, SWIFT',              'bg'=>'#4f46e5', 'fg'=>'#fff', 'slug'=>'virement', 'gateway'=>false],
];
$pm      = $reservation->payment_method ?? 'Orange Money';
$pmData  = $methodsMap[$pm] ?? $methodsMap['Orange Money'];
$pmSlug  = $pmData['slug'];
@endphp

<style>
.payment-container { max-width: 860px; margin: 0 auto; }
.payment-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.5rem; }
.summary-row { display: flex; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid rgba(148,163,184,.1); }
.summary-row:last-child { border-bottom: none; font-weight: 700; font-size: 1.05rem; padding-top: .9rem; color: var(--ci-green); }
</style>

<div class="payment-container">

    {{-- HEADER --}}
    <div class="card" style="margin-bottom:1.5rem;text-align:center;background:linear-gradient(135deg,rgba(247,127,0,.1),rgba(0,154,68,.08));border-color:var(--ci-orange-border);">
        <div style="display:flex;justify-content:center;margin-bottom:.5rem;">
            @if(isset($pmData['img']))
                <span style="width:72px;height:52px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;padding:6px;">
                    <img src="{{ asset($pmData['img']) }}" alt="{{ $pm }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                </span>
            @elseif(isset($pmData['fa']))
                <span style="width:52px;height:52px;border-radius:14px;background:{{ $pmData['bg'] }};color:{{ $pmData['fg'] }};display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                    <i class="fa-solid {{ $pmData['fa'] }}"></i>
                </span>
            @else
                <span style="width:52px;height:52px;border-radius:14px;background:{{ $pmData['bg'] }};color:{{ $pmData['fg'] }};display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:800;">
                    {{ $pmData['badge'] }}
                </span>
            @endif
        </div>
        <div style="font-size:1.3rem;font-weight:700;">{{ __('Finaliser le paiement') }}</div>
        <p style="color:var(--text-secondary);margin:.25rem 0 0;">{{ __('Réservation') }} <code style="color:var(--ci-orange);">{{ $reservation->code }}</code></p>
    </div>

    <div class="payment-grid">

        {{-- FORMULAIRE --}}
        <div class="card">

            {{-- MÉTHODE CHOISIE (affichage) --}}
            <div class="card-title" style="margin-bottom:1.25rem;">
                <span><i class="fa-solid fa-wallet" style="color:var(--ci-orange)"></i> {{ __('Méthode de paiement') }}</span>
            </div>

            <div style="display:flex;align-items:center;gap:1rem;padding:1rem 1.25rem;border-radius:14px;border:2px solid var(--ci-orange-border);background:var(--ci-orange-dim);margin-bottom:1.5rem;">
                <div style="width:60px;height:48px;border-radius:12px;background:{{ isset($pmData['img']) ? '#fff' : $pmData['bg'] }};color:{{ $pmData['fg'] ?? '#000' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;padding:4px;">
                    @if(isset($pmData['img']))
                        <img src="{{ asset($pmData['img']) }}" alt="{{ $pm }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                    @elseif(isset($pmData['fa']))
                        <i class="fa-solid {{ $pmData['fa'] }}" style="font-size:1.3rem;"></i>
                    @else
                        <span style="font-size:.68rem;font-weight:800;">{{ $pmData['badge'] }}</span>
                    @endif
                </div>
                <div>
                    <div style="font-weight:700;font-size:1rem;color:var(--text-primary);">{{ $pm }}</div>
                    <div style="font-size:.8rem;color:var(--text-secondary);">{{ $pmData['sub'] }}</div>
                </div>
                <span class="badge badge-warning" style="margin-left:auto;">
                    <i class="fa-solid fa-check"></i> {{ __('Sélectionné') }}
                </span>
            </div>

            {{-- FORMULAIRE DÉTAILS --}}
            <form method="POST" action="{{ route('reservations.payment.process', $reservation) }}">
                @csrf
                <input type="hidden" name="payment_method" value="{{ $pm }}">

                <div style="font-size:.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:1rem;">
                    <i class="fa-solid fa-circle-info" style="color:var(--ci-orange)"></i>
                    {{ __('Informations de paiement') }}
                </div>

                {{-- Moyens gérés par Jèko (redirection vers la page sécurisée de l'opérateur) --}}
                @if($pmData['gateway'])
                <div style="padding:1rem 1.1rem;background:{{ $pmData['bg'] }}14;border:1px solid {{ $pmData['bg'] }}55;border-radius:14px;font-size:.85rem;color:var(--text-secondary);line-height:1.7;margin-bottom:1rem;">
                    <div style="font-weight:700;margin-bottom:.4rem;font-size:.92rem;color:var(--text-primary);">
                        <i class="fa-solid fa-lock"></i> {{ __('Paiement sécurisé via Jèko') }}
                    </div>
                    {{ __('Vous allez être redirigé vers la page de paiement sécurisée :pm pour finaliser la transaction depuis votre téléphone.', ['pm' => $pm]) }}
                </div>
                @endif

                {{-- Espèces --}}
                @if($pmSlug === 'especes')
                <div style="padding:1.1rem 1.25rem;background:rgba(21,128,61,.08);border:1px solid rgba(21,128,61,.25);border-radius:14px;font-size:.88rem;color:#a7f3d0;line-height:1.7;margin-bottom:1rem;">
                    <div style="font-weight:700;margin-bottom:.5rem;font-size:.95rem;">
                        <i class="fa-solid fa-hand-holding-dollar"></i> {{ __('Paiement à la réception') }}
                    </div>
                    {{ __('Aucune information bancaire requise.') }}<br>
                    {{ __('Présentez-vous à la réception avec le code') }} <strong>{{ $reservation->code }}</strong> {{ __('et réglez en espèces à votre arrivée.') }}
                </div>
                @endif

                {{-- Virement bancaire --}}
                @if($pmSlug === 'virement')
                <div class="form-group">
                    <label><i class="fa-solid fa-user"></i> {{ __('Nom du titulaire du compte') }}</label>
                    <input type="text" name="pm_holder" placeholder="{{ __('Nom complet') }}">
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-building-columns"></i> {{ __('Nom de la banque') }}</label>
                    <input type="text" name="pm_bank" placeholder="{{ __('Ex : Société Générale CI') }}">
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-hashtag"></i> IBAN</label>
                    <input type="text" name="pm_iban" placeholder="CI00 0000 0000 0000 0000 0000 000" style="letter-spacing:.05em;">
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-globe"></i> {{ __('Code SWIFT / BIC') }}</label>
                    <input type="text" name="pm_swift" placeholder="{{ __('Ex : SGCIABAB') }}" maxlength="11" style="text-transform:uppercase;">
                </div>
                <div style="padding:.85rem 1rem;background:rgba(79,70,229,.08);border:1px solid rgba(79,70,229,.25);border-radius:12px;font-size:.82rem;color:#a5b4fc;margin-bottom:1rem;">
                    <i class="fa-solid fa-circle-info"></i> {{ __('Le virement peut prendre 1 à 3 jours ouvrés. Votre réservation sera confirmée à réception.') }}
                </div>
                @endif

                {{-- BOUTON --}}
                <button type="submit" class="btn" style="width:100%;justify-content:center;font-size:1rem;padding:1rem;margin-top:.5rem;">
                    <i class="fa-solid {{ $pmData['gateway'] ? 'fa-arrow-right' : 'fa-lock' }}"></i>
                    {{ $pmData['gateway'] ? __('Continuer vers le paiement') : __('Confirmer et payer') }}
                    {{ number_format($reservation->total, 0, ',', ' ') }} FCFA
                </button>
                <p style="text-align:center;font-size:.8rem;color:var(--text-muted);margin-top:.75rem;">
                    <i class="fa-solid fa-shield-halved" style="color:var(--ci-green)"></i>
                    {{ __('Paiement 100% sécurisé — Un email de confirmation sera envoyé') }}
                </p>
            </form>
        </div>

        {{-- SIDEBAR RÉSUMÉ --}}
        <div>
            <div class="card" style="margin-bottom:1rem;">
                <div class="card-title"><span><i class="fa-solid fa-receipt" style="color:var(--ci-green)"></i> {{ __('Résumé') }}</span></div>
                <div class="summary-row"><span>{{ __('Hôtel') }}</span><strong>{{ $reservation->hotel->name ?? $reservation->hotel->short_name }}</strong></div>
                <div class="summary-row"><span>{{ __('Chambre') }}</span><strong>{{ $reservation->room }}</strong></div>
                <div class="summary-row"><span>{{ __('Arrivée') }}</span><strong>{{ $reservation->checkin->format('d/m/Y') }}</strong></div>
                <div class="summary-row"><span>{{ __('Départ') }}</span><strong>{{ $reservation->checkout->format('d/m/Y') }}</strong></div>
                <div class="summary-row"><span>{{ __('Durée') }}</span><strong>{{ $reservation->nights }} {{ __('nuit(s)') }}</strong></div>
                <div class="summary-row"><span>{{ __('Prix / nuit') }}</span><strong>{{ number_format($reservation->price, 0, ',', ' ') }} FCFA</strong></div>
                <div class="summary-row"><span>{{ __('Total') }}</span><strong>{{ number_format($reservation->total, 0, ',', ' ') }} FCFA</strong></div>
            </div>

            <div class="card" style="background:rgba(0,154,68,.08);border-color:rgba(0,154,68,.25);margin-bottom:1rem;">
                <div style="font-size:.85rem;font-weight:700;color:var(--ci-green);margin-bottom:.75rem;">
                    <i class="fa-solid fa-circle-check"></i> {{ __('Garanties incluses') }}
                </div>
                <div style="font-size:.82rem;color:#a7f3d0;display:flex;flex-direction:column;gap:.5rem;">
                    <span><i class="fa-solid fa-check"></i> {{ __('Confirmation instantanée') }}</span>
                    <span><i class="fa-solid fa-check"></i> {{ __('Facture PDF envoyée par email') }}</span>
                    <span><i class="fa-solid fa-check"></i> {{ __('Remboursement jusqu\'à 95% en cas d\'annulation') }}</span>
                    <span><i class="fa-solid fa-check"></i> {{ __('Support 24h/24') }}</span>
                </div>
            </div>

            <a href="{{ route('hotels.show', $reservation->hotel_id) }}" class="btn-secondary" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-arrow-left"></i> {{ __('Modifier la réservation') }}
            </a>
        </div>

    </div>
</div>

@endsection
