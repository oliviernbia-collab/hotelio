@extends('layouts.app')

@section('content')

@php
$payMethods = [
    'carte_bancaire' => ['Carte bancaire', '#818cf8', 'rgba(79,70,229,.15)',  'rgba(79,70,229,.4)',  'fa-credit-card',   __('Visa · Mastercard')],
    'orange_money'   => ['Orange Money',   '#fb923c', 'rgba(255,98,0,.12)',   'rgba(255,98,0,.4)',   'fa-mobile-screen', __('Réseau Orange CI')],
    'mtn_momo'       => ['MTN MoMo',       '#fbbf24', 'rgba(255,198,0,.1)',   'rgba(255,198,0,.4)',  'fa-mobile-screen', __('Mobile Money MTN')],
    'moov_money'     => ['Moov Money',     '#60a5fa', 'rgba(0,60,143,.14)',   'rgba(0,60,143,.5)',   'fa-mobile-screen', __('Mobile Money Moov')],
    'wave'           => ['Wave',           '#38bdf8', 'rgba(29,184,224,.1)',  'rgba(29,184,224,.35)','fa-wave-square',   __('Transfert Wave')],
];
$processMsgsJs = [
    __('Connexion sécurisée au serveur de paiement'),
    __('Vérification des informations…'),
    __('Validation de la transaction…'),
    __('Finalisation de la commande…'),
];
@endphp

{{-- ÉTAPES --}}
<div style="display:flex;align-items:center;justify-content:center;margin-bottom:2rem;max-width:520px;margin-left:auto;margin-right:auto;">
    @foreach([['fa-cart-shopping',__('Panier'),true],['fa-lock',__('Paiement'),true],['fa-circle-check',__('Confirmation'),false]] as $i=>[$ico,$lbl,$done])
    <div style="display:flex;align-items:center;flex:1;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:.3rem;flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;{{ $done ? 'background:#F77F00;color:#fff;box-shadow:0 0 0 4px rgba(247,127,0,.2);' : 'background:rgba(255,255,255,.06);color:#334155;border:1px solid rgba(255,255,255,.08);' }}">
                <i class="fa-solid {{ $ico }}"></i>
            </div>
            <span style="font-size:.72rem;font-weight:600;{{ $done ? 'color:#F77F00;' : 'color:#334155;' }}">{{ $lbl }}</span>
        </div>
        @if($i < 2)
        <div style="height:1px;width:32px;flex-shrink:0;margin-bottom:1.3rem;background:{{ $i === 0 ? '#F77F00' : 'rgba(255,255,255,.08)' }};"></div>
        @endif
    </div>
    @endforeach
</div>

@if(empty($produits))
<div class="card" style="text-align:center;padding:3rem 2rem;">
    <i class="fa-solid fa-cart-xmark" style="font-size:3rem;color:#334155;margin-bottom:1rem;display:block;"></i>
    <div style="font-size:1.1rem;font-weight:700;color:#64748b;margin-bottom:.5rem;">{{ __('Votre panier est vide') }}</div>
    <a href="{{ route('boutique.clientboutique') }}" class="btn" style="margin-top:1rem;">
        <i class="fa-solid fa-store"></i> {{ __('Découvrir la boutique') }}
    </a>
</div>
@else

<form method="POST" action="{{ route('commande.store') }}" id="checkoutForm">
    @csrf
    <input type="hidden" name="payment_method" id="payMethodInput" value="">

    <div style="display:grid;grid-template-columns:1fr 400px;gap:1.5rem;align-items:start;" id="checkoutGrid">

        {{-- ── GAUCHE : récap commande ──────────────────────────────────── --}}
        <div>
            <div class="card" style="padding:0;overflow:hidden;">
                <div style="padding:1.1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-weight:700;color:#e2e8f0;font-size:.92rem;">
                        <i class="fa-solid fa-list-check" style="color:#F77F00;margin-right:.4rem;"></i> {{ __('Récapitulatif') }}
                    </span>
                    <span style="background:rgba(247,127,0,.15);border:1px solid rgba(247,127,0,.3);color:#fed7aa;border-radius:999px;padding:.2rem .7rem;font-size:.75rem;font-weight:600;">
                        {{ count($produits) }} {{ __('article(s)') }}
                    </span>
                </div>

                <div class="table-responsive">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:rgba(255,255,255,.025);">
                            <th style="padding:.7rem 1.5rem;text-align:left;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Produit') }}</th>
                            <th style="padding:.7rem 1rem;text-align:center;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Qté') }}</th>
                            <th style="padding:.7rem 1.5rem;text-align:right;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#475569;font-weight:600;">{{ __('Sous-total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produits as $p)
                        <tr style="border-top:1px solid rgba(255,255,255,.05);">
                            <td style="padding:.9rem 1.5rem;">
                                <div style="font-weight:600;color:#e2e8f0;font-size:.88rem;">{{ $p->name }}</div>
                                <div style="font-size:.73rem;color:#64748b;margin-top:.12rem;">{{ number_format($p->price, 0, ',', ' ') }} FCFA {{ __('/ unité') }}</div>
                            </td>
                            <td style="padding:.9rem 1rem;text-align:center;">
                                <span style="background:rgba(255,255,255,.07);border-radius:7px;padding:.22rem .6rem;font-size:.8rem;color:#94a3b8;font-weight:700;">× {{ $p->qty }}</span>
                            </td>
                            <td style="padding:.9rem 1.5rem;text-align:right;font-weight:700;color:#4ade80;font-size:.92rem;">
                                {{ number_format($p->subtotal, 0, ',', ' ') }}&nbsp;<span style="font-size:.72rem;opacity:.7;">FCFA</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div style="padding:1.1rem 1.5rem;border-top:1px solid rgba(255,255,255,.07);display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,.02);">
                    <span style="font-size:.85rem;color:#94a3b8;">{{ __('Total à payer') }}</span>
                    <span style="font-size:1.45rem;font-weight:800;color:#4ade80;">
                        {{ number_format($total, 0, ',', ' ') }}&nbsp;<span style="font-size:.82rem;font-weight:600;opacity:.8;">FCFA</span>
                    </span>
                </div>
            </div>

            <a href="{{ route('boutique.clientboutique') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:#475569;font-size:.8rem;margin-top:.85rem;text-decoration:none;" onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#475569'">
                <i class="fa-solid fa-arrow-left"></i> {{ __('Continuer mes achats') }}
            </a>
        </div>

        {{-- ── DROITE : moyen de paiement ───────────────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            <div class="card" style="padding:1.4rem;">
                <div style="font-weight:700;color:#e2e8f0;margin-bottom:1.1rem;font-size:.92rem;display:flex;align-items:center;gap:.5rem;">
                    <span style="width:28px;height:28px;border-radius:7px;background:rgba(247,127,0,.15);display:inline-flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-lock" style="color:#F77F00;font-size:.75rem;"></i>
                    </span>
                    {{ __('Moyen de paiement') }}
                </div>

                <div style="display:flex;flex-direction:column;gap:.5rem;" id="payList">
                    @foreach($payMethods as $key => [$label, $color, $bg, $border, $icon, $sub])
                    <button type="button"
                        data-method="{{ $key }}"
                        onclick="selectPay('{{ $key }}')"
                        style="display:flex;align-items:center;gap:.8rem;padding:.8rem .95rem;border-radius:11px;border:1px solid {{ $border }};background:{{ $bg }};cursor:pointer;text-align:left;width:100%;transition:all .15s;"
                        onmouseover="if(!this.classList.contains('active-pay'))this.style.filter='brightness(1.14)'"
                        onmouseout="if(!this.classList.contains('active-pay'))this.style.filter=''">
                        <span style="width:36px;height:36px;border-radius:9px;background:rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $icon }}" style="color:{{ $color }};font-size:.9rem;"></i>
                        </span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.85rem;font-weight:700;color:#e2e8f0;">{{ $label }}</div>
                            <div style="font-size:.71rem;color:#64748b;margin-top:.08rem;">{{ $sub }}</div>
                        </div>
                        <span class="pay-radio" style="width:18px;height:18px;border-radius:50%;border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;"></span>
                    </button>
                    @endforeach
                </div>

                {{-- ── PANNEAU SAISIE INFOS PAIEMENT ─────────────────────── --}}
                <div id="payDetailsPanel" style="display:none;margin-top:1rem;overflow:hidden;">

                    {{-- Séparateur --}}
                    <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
                        <div style="flex:1;height:1px;background:rgba(255,255,255,.07);"></div>
                        <span id="payDetailTitle" style="font-size:.73rem;text-transform:uppercase;letter-spacing:.08em;color:#475569;font-weight:600;white-space:nowrap;">{{ __('Informations de paiement') }}</span>
                        <div style="flex:1;height:1px;background:rgba(255,255,255,.07);"></div>
                    </div>

                    {{-- CARTE BANCAIRE ---------------------------------------- --}}
                    <div class="pay-detail" id="detail-carte_bancaire" style="display:none;">
                        {{-- Nom --}}
                        <div style="margin-bottom:.75rem;">
                            <label style="display:block;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.35rem;">{{ __('Titulaire de la carte') }}</label>
                            <input type="text" name="card_holder" id="card_holder" placeholder="NOM PRÉNOM" autocomplete="cc-name"
                                style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.7rem .9rem;color:#e2e8f0;font-size:.88rem;outline:none;box-sizing:border-box;transition:border-color .15s;"
                                onfocus="this.style.borderColor=currentColor" onblur="this.style.borderColor='rgba(255,255,255,.1)'" oninput="checkFields()">
                        </div>
                        {{-- Numéro de carte --}}
                        <div style="margin-bottom:.75rem;">
                            <label style="display:block;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.35rem;">{{ __('Numéro de carte') }}</label>
                            <div style="position:relative;">
                                <input type="text" name="card_number" id="card_number" placeholder="0000  0000  0000  0000" maxlength="19" autocomplete="cc-number"
                                    style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.7rem 2.8rem .7rem .9rem;color:#e2e8f0;font-size:.9rem;font-family:monospace;outline:none;box-sizing:border-box;letter-spacing:.05em;transition:border-color .15s;"
                                    onfocus="this.style.borderColor=currentColor" onblur="this.style.borderColor='rgba(255,255,255,.1)'" oninput="formatCardNumber(this);checkFields()">
                                <span id="cardTypeIcon" style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);font-size:.85rem;color:#475569;">
                                    <i class="fa-regular fa-credit-card"></i>
                                </span>
                            </div>
                        </div>
                        {{-- Expiry + CVV --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;">
                            <div>
                                <label style="display:block;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.35rem;">{{ __('Expiration') }}</label>
                                <input type="text" name="card_expiry" id="card_expiry" placeholder="MM / AA" maxlength="7" autocomplete="cc-exp"
                                    style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.7rem .9rem;color:#e2e8f0;font-size:.88rem;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color .15s;"
                                    onfocus="this.style.borderColor=currentColor" onblur="this.style.borderColor='rgba(255,255,255,.1)'" oninput="formatExpiry(this);checkFields()">
                            </div>
                            <div>
                                <label style="display:block;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.35rem;">CVV</label>
                                <input type="password" name="card_cvv" id="card_cvv" placeholder="•••" maxlength="4" autocomplete="cc-csc"
                                    style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.7rem .9rem;color:#e2e8f0;font-size:.88rem;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color .15s;"
                                    onfocus="this.style.borderColor=currentColor" onblur="this.style.borderColor='rgba(255,255,255,.1)'" oninput="checkFields()">
                            </div>
                        </div>
                        <div style="margin-top:.6rem;display:flex;align-items:center;gap:.35rem;font-size:.72rem;color:#475569;">
                            <i class="fa-solid fa-lock"></i> {{ __('Vos données sont chiffrées et sécurisées') }}
                        </div>
                    </div>

                    {{-- MOBILE MONEY (Orange / MTN / Moov / Wave) ------------- --}}
                    @foreach(['orange_money','mtn_momo','moov_money','wave'] as $mkey)
                    <div class="pay-detail" id="detail-{{ $mkey }}" style="display:none;">
                        <label style="display:block;font-size:.73rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.4rem;">{{ __('Numéro de téléphone') }}</label>
                        <div style="display:flex;align-items:center;gap:0;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;overflow:hidden;transition:border-color .15s;" id="phoneWrap-{{ $mkey }}">
                            <span style="padding:.7rem .75rem;background:rgba(255,255,255,.04);border-right:1px solid rgba(255,255,255,.08);color:#94a3b8;font-size:.82rem;white-space:nowrap;display:flex;align-items:center;gap:.35rem;flex-shrink:0;">
                                🇨🇮 +225
                            </span>
                            <input type="tel" name="phone_number" id="phone-{{ $mkey }}" placeholder="07 00 00 00 00" maxlength="14"
                                style="flex:1;background:transparent;border:none;padding:.7rem .9rem;color:#e2e8f0;font-size:.88rem;font-family:monospace;outline:none;letter-spacing:.04em;"
                                onfocus="document.getElementById('phoneWrap-{{ $mkey }}').style.borderColor=currentColor"
                                onblur="document.getElementById('phoneWrap-{{ $mkey }}').style.borderColor='rgba(255,255,255,.1)'"
                                oninput="formatPhone(this);checkFields()">
                        </div>
                        <div style="margin-top:.5rem;font-size:.75rem;color:#475569;">
                            {{ __('Un code de confirmation vous sera envoyé par SMS.') }}
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- Bouton confirmer --}}
            <button type="submit" id="confirmBtn" disabled
                style="width:100%;padding:.95rem;border-radius:13px;border:none;background:rgba(255,255,255,.05);color:#334155;font-size:.95rem;font-weight:700;cursor:not-allowed;transition:all .22s;display:flex;align-items:center;justify-content:center;gap:.55rem;">
                <i class="fa-solid fa-lock" id="confirmIcon"></i>
                <span id="confirmLabel">{{ __('Sélectionnez un moyen de paiement') }}</span>
            </button>

            <div style="text-align:center;font-size:.72rem;color:#334155;display:flex;align-items:center;justify-content:center;gap:.4rem;">
                <i class="fa-solid fa-shield-halved"></i> {{ __('Paiement 100 % sécurisé · Chiffrement SSL') }}
            </div>

        </div>
    </div>
</form>

@endif

{{-- ── OVERLAY TRAITEMENT ────────────────────────────────────────────────── --}}
<div id="processingOverlay" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(8,12,24,.93);backdrop-filter:blur(10px);align-items:center;justify-content:center;flex-direction:column;gap:1.75rem;">
    <div style="position:relative;width:80px;height:80px;">
        <div style="position:absolute;inset:0;border-radius:50%;border:3px solid rgba(247,127,0,.15);border-top-color:#F77F00;animation:spin .75s linear infinite;"></div>
        <div style="position:absolute;inset:8px;border-radius:50%;border:2px solid rgba(247,127,0,.08);border-top-color:rgba(247,127,0,.5);animation:spin 1.2s linear infinite reverse;"></div>
    </div>
    <div style="text-align:center;">
        <div style="font-size:1.1rem;font-weight:700;color:#f1f5f9;margin-bottom:.4rem;" id="overlayTitle">{{ __('Traitement en cours…') }}</div>
        <div style="font-size:.82rem;color:#64748b;" id="overlayText">{{ __('Connexion sécurisée au serveur de paiement') }}</div>
    </div>
    <div style="display:flex;gap:.5rem;" id="overlayDots">
        <span style="width:8px;height:8px;border-radius:50%;background:#F77F00;animation:pulse 1.2s ease-in-out 0s infinite;"></span>
        <span style="width:8px;height:8px;border-radius:50%;background:#F77F00;animation:pulse 1.2s ease-in-out .2s infinite;"></span>
        <span style="width:8px;height:8px;border-radius:50%;background:#F77F00;animation:pulse 1.2s ease-in-out .4s infinite;"></span>
    </div>
</div>

@push('styles')
<style>
@keyframes spin     { to { transform: rotate(360deg); } }
@keyframes pulse    { 0%,100% { opacity:.3; transform:scale(.8); } 50% { opacity:1; transform:scale(1.1); } }
@keyframes slideDown{ from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
@media (max-width: 860px) { #checkoutGrid { grid-template-columns: 1fr !important; } }
input::placeholder { color:#334155 !important; }
input:focus { outline:none; }
</style>
@endpush

<script>
const payColors = {
    carte_bancaire: '#818cf8', orange_money: '#fb923c',
    mtn_momo: '#fbbf24', moov_money: '#60a5fa', wave: '#38bdf8'
};
const payLabels = {
    carte_bancaire: @json(__('Confirmer le paiement')),
    orange_money:   @json(__('Payer avec Orange Money')),
    mtn_momo:       @json(__('Payer avec MTN MoMo')),
    moov_money:     @json(__('Payer avec Moov Money')),
    wave:           @json(__('Payer avec Wave')),
};
const detailTitles = {
    carte_bancaire: @json(__('Informations de la carte')),
    orange_money:   @json(__('Numéro Orange Money')),
    mtn_momo:       @json(__('Numéro MTN MoMo')),
    moov_money:     @json(__('Numéro Moov Money')),
    wave:           @json(__('Numéro Wave')),
};

let currentMethod = null;
let currentColor  = '#F77F00';

function selectPay(method) {
    currentMethod = method;
    currentColor  = payColors[method];
    document.getElementById('payMethodInput').value = method;

    // reset all buttons
    document.querySelectorAll('#payList button').forEach(btn => {
        btn.classList.remove('active-pay');
        btn.style.boxShadow = '';
        btn.style.filter    = '';
        const r = btn.querySelector('.pay-radio');
        r.innerHTML = '';
        r.style.borderColor = 'rgba(255,255,255,.15)';
        r.style.background  = 'transparent';
    });

    // activate selected
    const active = document.querySelector('[data-method="' + method + '"]');
    active.classList.add('active-pay');
    active.style.boxShadow = '0 0 0 2px ' + currentColor;
    active.querySelector('.pay-radio').innerHTML = '<i class="fa-solid fa-check" style="font-size:.55rem;color:#fff;"></i>';
    active.querySelector('.pay-radio').style.borderColor = currentColor;
    active.querySelector('.pay-radio').style.background  = currentColor;

    // show details panel
    const panel = document.getElementById('payDetailsPanel');
    panel.style.display = 'block';
    panel.style.animation = 'slideDown .28s ease';

    // update separator title color
    document.getElementById('payDetailTitle').textContent = detailTitles[method];
    document.getElementById('payDetailTitle').style.color = currentColor;

    // hide all detail sections, show the right one
    document.querySelectorAll('.pay-detail').forEach(d => d.style.display = 'none');
    document.getElementById('detail-' + method).style.display = 'block';

    // color focus on inputs
    document.querySelectorAll('#detail-' + method + ' input').forEach(inp => {
        inp.addEventListener('focus',  () => inp.style.borderColor = currentColor, { once: false });
        inp.addEventListener('blur',   () => inp.style.borderColor = 'rgba(255,255,255,.1)', { once: false });
    });
    const phoneWrap = document.getElementById('phoneWrap-' + method);
    if (phoneWrap) {
        phoneWrap.querySelectorAll('input').forEach(inp => {
            inp.addEventListener('focus',  () => phoneWrap.style.borderColor = currentColor, { once: false });
            inp.addEventListener('blur',   () => phoneWrap.style.borderColor = 'rgba(255,255,255,.1)', { once: false });
        });
    }

    // reset confirm btn
    const btn = document.getElementById('confirmBtn');
    btn.disabled         = true;
    btn.style.background = 'rgba(255,255,255,.05)';
    btn.style.color      = '#334155';
    btn.style.cursor     = 'not-allowed';
    btn.style.boxShadow  = 'none';
    document.getElementById('confirmLabel').textContent = @json(__('Remplissez les informations ci-dessous'));

    checkFields();
}

function checkFields() {
    if (!currentMethod) return;
    const btn = document.getElementById('confirmBtn');
    let ok = false;

    if (currentMethod === 'carte_bancaire') {
        const holder = document.getElementById('card_holder')?.value.trim();
        const number = document.getElementById('card_number')?.value.replace(/\s/g,'');
        const expiry = document.getElementById('card_expiry')?.value.replace(/\s/g,'');
        const cvv    = document.getElementById('card_cvv')?.value.trim();
        ok = holder.length >= 3 && number.length === 16 && expiry.length >= 4 && cvv.length >= 3;
    } else {
        const phone = document.getElementById('phone-' + currentMethod)?.value.replace(/\s/g,'');
        ok = phone && phone.length >= 8;
    }

    if (ok) {
        btn.disabled         = false;
        btn.style.background = currentColor;
        btn.style.color      = '#0f172a';
        btn.style.cursor     = 'pointer';
        btn.style.boxShadow  = '0 8px 28px ' + currentColor + '50';
        document.getElementById('confirmLabel').textContent = payLabels[currentMethod];
    } else {
        btn.disabled         = true;
        btn.style.background = currentColor + '33';
        btn.style.color      = currentColor + 'aa';
        btn.style.cursor     = 'not-allowed';
        btn.style.boxShadow  = 'none';
        document.getElementById('confirmLabel').textContent = payLabels[currentMethod];
    }
}

// ── Formatage numéro de carte (XXXX XXXX XXXX XXXX)
function formatCardNumber(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 16);
    input.value = v.match(/.{1,4}/g)?.join('  ') || v;
    // icône type de carte
    const icon = document.getElementById('cardTypeIcon');
    if (v.startsWith('4'))      icon.innerHTML = '<i class="fa-brands fa-cc-visa" style="color:#818cf8;font-size:1.1rem;"></i>';
    else if (/^5[1-5]/.test(v)) icon.innerHTML = '<i class="fa-brands fa-cc-mastercard" style="color:#fb923c;font-size:1.1rem;"></i>';
    else                         icon.innerHTML = '<i class="fa-regular fa-credit-card" style="color:#475569;"></i>';
}

// ── Formatage date expiration (MM / AA)
function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 4);
    if (v.length >= 3) v = v.slice(0,2) + ' / ' + v.slice(2);
    input.value = v;
}

// ── Formatage téléphone (XX XX XX XX XX)
function formatPhone(input) {
    let v = input.value.replace(/\D/g, '').slice(0, 10);
    input.value = v.match(/.{1,2}/g)?.join(' ') || v;
}

// ── Overlay traitement
const processMsgs = @json($processMsgsJs);
let msgIdx = 0;

document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    document.getElementById('processingOverlay').style.display = 'flex';
    setInterval(() => {
        msgIdx = (msgIdx + 1) % processMsgs.length;
        document.getElementById('overlayText').textContent = processMsgs[msgIdx];
    }, 900);
});
</script>

@endsection
