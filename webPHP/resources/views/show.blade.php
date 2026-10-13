@extends('layouts.app')

@section('content')

<div style="max-width:960px;margin:0 auto;">

    {{-- BREADCRUMB --}}
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.85rem;color:var(--text-muted);">
        <a href="{{ route('boutique.clientboutique') }}" style="color:var(--ci-orange);text-decoration:none;">
            <i class="fa-solid fa-store"></i> {{ __('Boutique') }}
        </a>
        <i class="fa-solid fa-chevron-right" style="font-size:.65rem;"></i>
        <span style="color:var(--text-secondary);">{{ $produit->name }}</span>
    </div>

    {{-- CARTE PRODUIT --}}
    <div class="card" style="padding:0;overflow:hidden;margin-bottom:1.5rem;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">

            {{-- IMAGE --}}
            <div style="position:relative;height:420px;overflow:hidden;background:var(--bg-input);">
                <img src="{{ $produit->image ? $produit->image_url : asset('images/logo.png') }}"
                     alt="{{ $produit->name }}"
                     style="width:100%;height:100%;object-fit:cover;transition:transform .5s ease;"
                     onmouseover="this.style.transform='scale(1.04)'"
                     onmouseout="this.style.transform='scale(1)'">
            </div>

            {{-- INFOS --}}
            <div style="padding:2rem;display:flex;flex-direction:column;gap:1.25rem;">

                <div>
                    <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);line-height:1.2;margin-bottom:.5rem;">
                        <i class="fa-solid fa-box-open" style="color:var(--ci-orange);"></i>
                        {{ $produit->name }}
                    </div>
                    <div style="color:var(--text-secondary);font-size:.9rem;line-height:1.6;">
                        {{ $produit->description }}
                    </div>
                </div>

                <div style="padding:1rem;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);border-radius:16px;">
                    <div id="priceLabel" style="font-size:.78rem;color:var(--ci-orange);font-weight:600;opacity:.75;margin-bottom:.25rem;">
                        {{ __('Prix unitaire') }}
                    </div>
                    <div id="priceDisplay" style="font-size:2rem;font-weight:800;color:var(--ci-orange);">
                        {{ number_format($produit->price, 0, ',', ' ') }} <span style="font-size:.95rem;font-weight:600;opacity:.8;">FCFA</span>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:.5rem;padding:.75rem 1rem;background:var(--bg-input);border-radius:12px;border:1px solid var(--border);">
                    <i class="fa-solid fa-box" style="color:var(--text-muted);"></i>
                    <span style="color:var(--text-secondary);font-size:.9rem;">{{ __('Stock disponible :') }}</span>
                    <strong style="color:{{ $produit->stock > 0 ? 'var(--ci-green)' : '#fca5a5' }};">{{ $produit->stock }}</strong>
                </div>

                {{-- QUANTITÉ --}}
                <div>
                    <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:.5rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;">{{ __('Quantité') }}</div>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <button onclick="changeQty(-1)"
                                data-btn-minus
                                style="width:40px;height:40px;border-radius:12px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;transition:all .2s;opacity:.4;"
                                onmouseover="this.style.background='rgba(230,57,70,.25)'"
                                onmouseout="this.style.background='rgba(230,57,70,.1)'">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <span id="qty" style="font-size:1.4rem;font-weight:800;color:var(--text-primary);min-width:2.5rem;text-align:center;">1</span>
                        <button onclick="changeQty(1)"
                                style="width:40px;height:40px;border-radius:12px;border:1px solid rgba(0,154,68,.3);background:rgba(0,154,68,.1);color:#a7f3d0;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;transition:background .2s;"
                                onmouseover="this.style.background='rgba(0,154,68,.25)'"
                                onmouseout="this.style.background='rgba(0,154,68,.1)'">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <form method="POST" action="{{ route('panier.add', $produit->id) }}" style="flex:1;">
                        @csrf
                        <input type="hidden" name="qty" id="qtyInput" value="1">
                        <button type="submit" class="btn-secondary" style="width:100%;justify-content:center;" onclick="showToastMsg()">
                            <i class="fa-solid fa-cart-plus"></i> {{ __('Ajouter au panier') }}
                        </button>
                    </form>
                    <button type="button" onclick="openPayModal()" class="btn" style="flex:1;justify-content:center;border:none;cursor:pointer;">
                        <i class="fa-solid fa-bolt"></i> {{ __('Acheter') }}
                    </button>
                </div>

                <a href="{{ route('boutique.clientboutique') }}" style="color:var(--text-muted);font-size:.85rem;display:flex;align-items:center;gap:.4rem;">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Retour à la boutique') }}
                </a>

            </div>
        </div>
    </div>

    {{-- PANIER FLOTTANT (compteur) --}}
    @php
        $panier = session('panier', []);
        $cartCount = array_sum($panier);
    @endphp
    @if($cartCount > 0)
    <div style="position:fixed;bottom:24px;right:24px;z-index:100;">
        <a href="{{ route('commande.create') }}" class="btn" style="border-radius:999px;padding:.85rem 1.5rem;box-shadow:0 8px 32px rgba(247,127,0,.4);">
            <i class="fa-solid fa-cart-shopping"></i>
            {{ $cartCount }} {{ __('article(s) — Voir le panier') }}
        </a>
    </div>
    @endif

</div>

{{-- MODAL PAIEMENT --}}
<div id="payModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.8);backdrop-filter:blur(7px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:#1a2235;border-radius:20px;max-width:480px;width:100%;border:1px solid rgba(255,255,255,.1);box-shadow:0 32px 80px rgba(0,0,0,.7);position:relative;animation:slideUp .22s ease;max-height:90vh;overflow-y:auto;">

        {{-- En-tête fixe --}}
        <div style="padding:1.5rem 1.5rem 0;position:sticky;top:0;background:#1a2235;z-index:1;padding-bottom:.75rem;border-bottom:1px solid rgba(255,255,255,.06);">
            <button onclick="closePayModal()" style="position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,.07);border:none;color:#94a3b8;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(255,255,255,.14)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 style="margin:0 0 .2rem;font-size:1rem;color:#f1f5f9;display:flex;align-items:center;gap:.5rem;">
                <span style="background:rgba(247,127,0,.18);border-radius:7px;padding:.3rem .4rem;"><i class="fa-solid fa-lock" style="color:#F77F00;font-size:.8rem;"></i></span>
                {{ __('Paiement sécurisé') }}
            </h3>
            <p style="margin:0;font-size:.75rem;color:#475569;">{{ __('Choisissez un moyen de paiement puis saisissez vos informations.') }}</p>
        </div>

        <div style="padding:1.25rem 1.5rem 1.5rem;">

            {{-- Résumé montant --}}
            <div style="background:rgba(247,127,0,.09);border:1px solid rgba(247,127,0,.22);border-radius:12px;padding:.8rem 1rem;margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;gap:.5rem;">
                <div>
                    <div style="font-size:.78rem;color:#94a3b8;">{{ $produit->name }}</div>
                    <div id="modalQtyLabel" style="font-size:.72rem;color:#64748b;margin-top:.1rem;">1 {{ __('article') }}</div>
                </div>
                <div id="modalPriceDisplay" style="font-size:1.3rem;font-weight:800;color:#F77F00;white-space:nowrap;">
                    {{ number_format($produit->price, 0, ',', ' ') }}&nbsp;FCFA
                </div>
            </div>

            {{-- Formulaire --}}
            <form id="payForm" method="POST" action="{{ route('commande.buy-now') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $produit->id }}">
                <input type="hidden" name="qty"            id="modalQtyInput" value="1">
                <input type="hidden" name="payment_method" id="mPayMethodInput" value="">

                {{-- Liste des moyens de paiement --}}
                <div style="display:flex;flex-direction:column;gap:.45rem;margin-bottom:0;" id="mPayList">

                    @foreach([
                        ['carte_bancaire','Carte bancaire','#818cf8','rgba(79,70,229,.13)','rgba(79,70,229,.38)','fa-credit-card',null,__('Visa · Mastercard')],
                        ['orange_money',  'Orange Money',  '#fb923c','rgba(255,98,0,.11)', 'rgba(255,98,0,.38)', null,'orange-money.svg',__('Réseau Orange CI')],
                        ['mtn_momo',      'MTN MoMo',      '#fbbf24','rgba(255,198,0,.09)','rgba(255,198,0,.38)',null,'mtn-momo.png',__('Mobile Money MTN')],
                        ['moov_money',    'Moov Money',    '#60a5fa','rgba(0,60,143,.13)', 'rgba(0,60,143,.45)', null,'moov.png',__('Mobile Money Moov')],
                        ['wave',          'Wave',          '#38bdf8','rgba(29,184,224,.09)','rgba(29,184,224,.32)',null,'wave.png',__('Transfert Wave')],
                    ] as [$mkey,$mlabel,$mcolor,$mbg,$mborder,$micon,$mlogo,$msub])
                    <button type="button"
                        data-mmethod="{{ $mkey }}"
                        onclick="mSelectPay('{{ $mkey }}')"
                        style="display:flex;align-items:center;gap:.8rem;padding:.75rem .9rem;border-radius:11px;border:1px solid {{ $mborder }};background:{{ $mbg }};cursor:pointer;text-align:left;width:100%;transition:all .15s;"
                        onmouseover="if(!this.classList.contains('m-active-pay'))this.style.filter='brightness(1.12)'"
                        onmouseout="if(!this.classList.contains('m-active-pay'))this.style.filter=''">
                        <span style="width:34px;height:34px;border-radius:8px;background:{{ $mlogo ? '#fff' : 'rgba(0,0,0,.2)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                            @if($mlogo)
                                <img src="{{ asset('images/payment-methods/'.$mlogo) }}" alt="{{ $mlabel }}" style="width:100%;height:100%;object-fit:contain;padding:4px;box-sizing:border-box;">
                            @else
                                <i class="fa-solid {{ $micon }}" style="color:{{ $mcolor }};font-size:.88rem;"></i>
                            @endif
                        </span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.84rem;font-weight:700;color:#e2e8f0;">{{ $mlabel }}</div>
                            <div style="font-size:.7rem;color:#64748b;margin-top:.06rem;">{{ $msub }}</div>
                        </div>
                        <span class="m-pay-radio" style="width:17px;height:17px;border-radius:50%;border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;"></span>
                    </button>
                    @endforeach

                </div>

                {{-- Panneau infos de paiement --}}
                <div id="mPayDetailsPanel" style="display:none;margin-top:.85rem;">
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.85rem;">
                        <div style="flex:1;height:1px;background:rgba(255,255,255,.07);"></div>
                        <span id="mPayDetailTitle" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:#475569;font-weight:600;white-space:nowrap;"></span>
                        <div style="flex:1;height:1px;background:rgba(255,255,255,.07);"></div>
                    </div>

                    {{-- CARTE --}}
                    <div class="m-pay-detail" id="mdetail-carte_bancaire" style="display:none;">
                        <div style="margin-bottom:.65rem;">
                            <label style="display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.3rem;">{{ __('Titulaire') }}</label>
                            <input type="text" name="card_holder" id="m_card_holder" placeholder="NOM PRÉNOM" autocomplete="cc-name"
                                style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:.65rem .8rem;color:#e2e8f0;font-size:.85rem;outline:none;box-sizing:border-box;" oninput="mCheckFields()">
                        </div>
                        <div style="margin-bottom:.65rem;">
                            <label style="display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.3rem;">{{ __('Numéro de carte') }}</label>
                            <div style="position:relative;">
                                <input type="text" name="card_number" id="m_card_number" placeholder="0000  0000  0000  0000" maxlength="19" autocomplete="cc-number"
                                    style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:.65rem 2.6rem .65rem .8rem;color:#e2e8f0;font-size:.88rem;font-family:monospace;outline:none;box-sizing:border-box;letter-spacing:.05em;" oninput="mFormatCard(this);mCheckFields()">
                                <span id="mCardTypeIcon" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.82rem;color:#475569;">
                                    <i class="fa-regular fa-credit-card"></i>
                                </span>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
                            <div>
                                <label style="display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.3rem;">{{ __('Expiration') }}</label>
                                <input type="text" name="card_expiry" id="m_card_expiry" placeholder="MM / AA" maxlength="7" autocomplete="cc-exp"
                                    style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:.65rem .8rem;color:#e2e8f0;font-size:.85rem;font-family:monospace;outline:none;box-sizing:border-box;" oninput="mFormatExpiry(this);mCheckFields()">
                            </div>
                            <div>
                                <label style="display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.3rem;">CVV</label>
                                <input type="password" name="card_cvv" id="m_card_cvv" placeholder="•••" maxlength="4" autocomplete="cc-csc"
                                    style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:.65rem .8rem;color:#e2e8f0;font-size:.85rem;font-family:monospace;outline:none;box-sizing:border-box;" oninput="mCheckFields()">
                            </div>
                        </div>
                    </div>

                    {{-- MOBILE MONEY --}}
                    @foreach(['orange_money','mtn_momo','moov_money','wave'] as $mkey)
                    <div class="m-pay-detail" id="mdetail-{{ $mkey }}" style="display:none;">
                        <label style="display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.3rem;">{{ __('Numéro de téléphone') }}</label>
                        <div style="display:flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;overflow:hidden;" id="mPhoneWrap-{{ $mkey }}">
                            <span style="padding:.65rem .7rem;background:rgba(255,255,255,.04);border-right:1px solid rgba(255,255,255,.08);color:#94a3b8;font-size:.78rem;white-space:nowrap;flex-shrink:0;">
                                🇨🇮 +225
                            </span>
                            <input type="tel" name="phone_number" id="mphone-{{ $mkey }}" placeholder="07 00 00 00 00" maxlength="14"
                                style="flex:1;background:transparent;border:none;padding:.65rem .8rem;color:#e2e8f0;font-size:.85rem;font-family:monospace;outline:none;"
                                oninput="mFormatPhone(this);mCheckFields()">
                        </div>
                        <div style="margin-top:.4rem;font-size:.72rem;color:#475569;">{{ __('Un code SMS vous sera envoyé pour confirmer.') }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- Bouton confirmer --}}
                <button type="submit" id="mConfirmBtn" disabled
                    style="width:100%;margin-top:1rem;padding:.9rem;border-radius:12px;border:none;background:rgba(255,255,255,.05);color:#334155;font-size:.9rem;font-weight:700;cursor:not-allowed;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem;">
                    <i class="fa-solid fa-lock"></i>
                    <span id="mConfirmLabel">{{ __('Sélectionnez un moyen de paiement') }}</span>
                </button>

                <div style="text-align:center;margin-top:.65rem;font-size:.7rem;color:#334155;display:flex;align-items:center;justify-content:center;gap:.35rem;">
                    <i class="fa-solid fa-shield-halved"></i> {{ __('Paiement 100 % sécurisé · SSL') }}
                </div>

            </form>
        </div>
    </div>
</div>

<style>
@keyframes slideUp  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes mSlideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
#mPayDetailsPanel { animation: mSlideDown .25s ease; }
</style>

<script>
const mPayColors = {
    carte_bancaire:'#818cf8', orange_money:'#fb923c',
    mtn_momo:'#fbbf24', moov_money:'#60a5fa', wave:'#38bdf8'
};
const mDetailTitles = {
    carte_bancaire: @json(__('Informations de la carte')),
    orange_money:   @json(__('Numéro Orange Money')),
    mtn_momo:       @json(__('Numéro MTN MoMo')),
    moov_money:     @json(__('Numéro Moov Money')),
    wave:           @json(__('Numéro Wave')),
};
let mCurrentMethod = null;
let mCurrentColor  = '#F77F00';

function openPayModal() {
    const modal = document.getElementById('payModal');
    // reset state
    mCurrentMethod = null;
    document.querySelectorAll('.m-pay-detail').forEach(d => d.style.display = 'none');
    document.getElementById('mPayDetailsPanel').style.display = 'none';
    const btn = document.getElementById('mConfirmBtn');
    btn.disabled = true; btn.style.background='rgba(255,255,255,.05)';
    btn.style.color='#334155'; btn.style.cursor='not-allowed'; btn.style.boxShadow='';
    document.getElementById('mConfirmLabel').textContent = @json(__('Sélectionnez un moyen de paiement'));
    document.querySelectorAll('#mPayList button').forEach(b => {
        b.classList.remove('m-active-pay'); b.style.boxShadow=''; b.style.filter='';
        const r = b.querySelector('.m-pay-radio');
        r.innerHTML=''; r.style.borderColor='rgba(255,255,255,.15)'; r.style.background='transparent';
    });
    // sync qty
    document.getElementById('modalQtyInput').value = qty;
    document.getElementById('modalQtyLabel').textContent = qty + ' ' + (qty > 1 ? @json(__('articles')) : @json(__('article')));
    document.getElementById('modalPriceDisplay').innerHTML = (UNIT_PRICE * qty).toLocaleString('fr-FR') + '&nbsp;FCFA';
    modal.style.display = 'flex';
}
function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
}

function mSelectPay(method) {
    mCurrentMethod = method;
    mCurrentColor  = mPayColors[method];
    document.getElementById('mPayMethodInput').value = method;

    document.querySelectorAll('#mPayList button').forEach(btn => {
        btn.classList.remove('m-active-pay'); btn.style.boxShadow=''; btn.style.filter='';
        const r = btn.querySelector('.m-pay-radio');
        r.innerHTML=''; r.style.borderColor='rgba(255,255,255,.15)'; r.style.background='transparent';
    });
    const active = document.querySelector('[data-mmethod="' + method + '"]');
    active.classList.add('m-active-pay');
    active.style.boxShadow = '0 0 0 2px ' + mCurrentColor;
    active.querySelector('.m-pay-radio').innerHTML = '<i class="fa-solid fa-check" style="font-size:.5rem;color:#fff;"></i>';
    active.querySelector('.m-pay-radio').style.borderColor = mCurrentColor;
    active.querySelector('.m-pay-radio').style.background  = mCurrentColor;

    const panel = document.getElementById('mPayDetailsPanel');
    panel.style.display = 'block';
    document.getElementById('mPayDetailTitle').textContent = mDetailTitles[method];
    document.getElementById('mPayDetailTitle').style.color = mCurrentColor;
    document.querySelectorAll('.m-pay-detail').forEach(d => d.style.display = 'none');
    document.getElementById('mdetail-' + method).style.display = 'block';

    // input focus color
    document.querySelectorAll('#mdetail-' + method + ' input').forEach(inp => {
        inp.style.borderColor = 'rgba(255,255,255,.1)';
        inp.onfocus = () => inp.style.borderColor = mCurrentColor;
        inp.onblur  = () => inp.style.borderColor = 'rgba(255,255,255,.1)';
    });
    const pw = document.getElementById('mPhoneWrap-' + method);
    if (pw) {
        pw.querySelectorAll('input').forEach(inp => {
            inp.onfocus = () => pw.style.borderColor = mCurrentColor;
            inp.onblur  = () => pw.style.borderColor = 'rgba(255,255,255,.1)';
        });
    }

    const btn = document.getElementById('mConfirmBtn');
    btn.disabled = true; btn.style.background = mCurrentColor + '33';
    btn.style.color = mCurrentColor + 'aa'; btn.style.cursor = 'not-allowed'; btn.style.boxShadow='';
    document.getElementById('mConfirmLabel').textContent = @json(__('Remplissez les informations ci-dessous'));
    mCheckFields();
}

function mCheckFields() {
    if (!mCurrentMethod) return;
    const btn = document.getElementById('mConfirmBtn');
    let ok = false;
    if (mCurrentMethod === 'carte_bancaire') {
        const h = document.getElementById('m_card_holder')?.value.trim();
        const n = document.getElementById('m_card_number')?.value.replace(/\s/g,'');
        const e = document.getElementById('m_card_expiry')?.value.replace(/\s/g,'');
        const c = document.getElementById('m_card_cvv')?.value.trim();
        ok = h?.length >= 3 && n?.length === 16 && e?.length >= 4 && c?.length >= 3;
    } else {
        const p = document.getElementById('mphone-' + mCurrentMethod)?.value.replace(/\s/g,'');
        ok = p && p.length >= 8;
    }
    if (ok) {
        btn.disabled = false; btn.style.background = mCurrentColor;
        btn.style.color = '#0f172a'; btn.style.cursor = 'pointer';
        btn.style.boxShadow = '0 6px 20px ' + mCurrentColor + '50';
        document.getElementById('mConfirmLabel').textContent = @json(__('Confirmer le paiement'));
    } else {
        btn.disabled = true; btn.style.background = mCurrentColor + '33';
        btn.style.color = mCurrentColor + 'aa'; btn.style.cursor = 'not-allowed'; btn.style.boxShadow='';
        document.getElementById('mConfirmLabel').textContent = @json(__('Remplissez les informations ci-dessous'));
    }
}

function mFormatCard(input) {
    let v = input.value.replace(/\D/g,'').slice(0,16);
    input.value = v.match(/.{1,4}/g)?.join('  ') || v;
    const icon = document.getElementById('mCardTypeIcon');
    if (v.startsWith('4'))      icon.innerHTML = '<i class="fa-brands fa-cc-visa" style="color:#818cf8;font-size:1rem;"></i>';
    else if (/^5[1-5]/.test(v)) icon.innerHTML = '<i class="fa-brands fa-cc-mastercard" style="color:#fb923c;font-size:1rem;"></i>';
    else                         icon.innerHTML = '<i class="fa-regular fa-credit-card" style="color:#475569;"></i>';
}
function mFormatExpiry(input) {
    let v = input.value.replace(/\D/g,'').slice(0,4);
    if (v.length >= 3) v = v.slice(0,2) + ' / ' + v.slice(2);
    input.value = v;
}
function mFormatPhone(input) {
    let v = input.value.replace(/\D/g,'').slice(0,10);
    input.value = v.match(/.{1,2}/g)?.join(' ') || v;
}

document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>

{{-- TOAST --}}
<div id="toastLocal" style="display:none;position:fixed;top:20px;right:20px;z-index:999;
     background:var(--ci-green);color:#fff;padding:.85rem 1.25rem;border-radius:14px;
     font-size:.88rem;font-weight:600;display:none;align-items:center;gap:.6rem;
     box-shadow:0 8px 32px rgba(0,154,68,.4);">
    <i class="fa-solid fa-circle-check"></i> {{ __('Produit ajouté au panier') }}
</div>

<script>
const UNIT_PRICE = {{ $produit->price }};
const MAX_STOCK  = {{ $produit->stock }};
let qty = 1;

function changeQty(val) {
    qty = Math.min(MAX_STOCK, Math.max(1, qty + val));

    document.getElementById('qty').textContent = qty;
    document.getElementById('qtyInput').value  = qty;

    const total = UNIT_PRICE * qty;
    const label = document.getElementById('priceLabel');
    const display = document.getElementById('priceDisplay');

    if (qty > 1) {
        label.textContent = qty + ' × ' + UNIT_PRICE.toLocaleString('fr-FR') + ' FCFA';
        display.innerHTML = total.toLocaleString('fr-FR') + ' <span style="font-size:.95rem;font-weight:600;opacity:.8;">FCFA</span>';
    } else {
        label.textContent = @json(__('Prix unitaire'));
        display.innerHTML = UNIT_PRICE.toLocaleString('fr-FR') + ' <span style="font-size:.95rem;font-weight:600;opacity:.8;">FCFA</span>';
    }

    document.querySelector('[data-btn-minus]').style.opacity = qty <= 1 ? '.4' : '1';
}

function showToastMsg() {
    const t = document.getElementById('toastLocal');
    t.style.display = 'flex';
    setTimeout(() => t.style.display = 'none', 2500);
}
</script>

@push('styles')
<style>
@media (max-width: 720px) {
    .card > div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    .card > div[style*="grid-template-columns:1fr 1fr"] > div:first-child {
        height: 250px !important;
    }
}
</style>
@endpush

@endsection
