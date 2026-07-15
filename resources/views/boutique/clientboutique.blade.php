@extends('layouts.app')

@section('content')

@php $cartTotal = array_sum(session('panier', [])); @endphp

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-store"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Catalogue Boutique') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Ajoutez des produits à votre panier — le total se met à jour automatiquement.') }}</p>
            </div>
        </div>
        <a href="{{ route('commande.create') }}" class="btn" id="cartBtn">
            <i class="fa-solid fa-cart-shopping"></i>
            <span id="cartLabel">
                @if($cartTotal > 0)
                    {{ $cartTotal }} {{ __('article(s)') }} —
                    @php
                        $cartAmt = 0;
                        foreach(session('panier', []) as $pid => $qty) {
                            $p = $produits->firstWhere('id', $pid);
                            if ($p) $cartAmt += $p->price * $qty;
                        }
                    @endphp
                    {{ number_format($cartAmt, 0, ',', ' ') }} FCFA
                @else
                    {{ __('Mon panier') }}
                @endif
            </span>
        </a>
    </div>
</div>

{{-- BANDEAU TOTAL FLOTTANT (visible quand panier non vide) --}}
<div id="cartSummary" style="display:{{ $cartTotal > 0 ? 'flex' : 'none' }};align-items:center;justify-content:space-between;padding:.85rem 1.25rem;background:var(--ci-green-dim);border:1px solid var(--ci-green-border);border-radius:16px;margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;gap:.75rem;color:#a7f3d0;font-weight:700;">
        <i class="fa-solid fa-cart-shopping"></i>
        <span id="summaryCount">{{ $cartTotal }}</span> {{ __('article(s) dans votre panier') }}
    </div>
    <div style="display:flex;align-items:center;gap:1rem;">
        <span style="font-size:1.2rem;font-weight:800;color:var(--ci-green);" id="summaryTotal">
            @php
                $initTotal = 0;
                foreach(session('panier', []) as $pid => $qty) {
                    $p = $produits->firstWhere('id', $pid);
                    if ($p) $initTotal += $p->price * $qty;
                }
            @endphp
            {{ number_format($initTotal, 0, ',', ' ') }} FCFA
        </span>
        <a href="{{ route('commande.create') }}" class="btn btn-green" style="font-size:.85rem;padding:.55rem 1rem;">
            <i class="fa-solid fa-arrow-right"></i> {{ __('Valider') }}
        </a>
    </div>
</div>

{{-- GRILLE PRODUITS --}}
<div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;">

    @forelse($produits as $produit)
    <div class="card card-hover"
         data-product-id="{{ $produit->id }}"
         data-price="{{ $produit->price }}"
         data-add-url="{{ route('panier.add', $produit->id) }}"
         data-dec-url="{{ route('panier.decrease', $produit->id) }}"
         style="padding:0;overflow:hidden;display:flex;flex-direction:column;">

        {{-- IMAGE --}}
        <div style="position:relative;width:100%;height:180px;overflow:hidden;background:var(--bg-input);flex-shrink:0;">
            <img src="{{ asset('storage/'.$produit->image) }}"
                 alt="{{ $produit->nom }}"
                 style="width:100%;height:100%;object-fit:cover;transition:transform .4s ease;"
                 onmouseover="this.style.transform='scale(1.06)'"
                 onmouseout="this.style.transform='scale(1)'">
            <div style="position:absolute;top:.75rem;right:.75rem;">
                <span class="badge badge-secondary" style="backdrop-filter:blur(8px);background:rgba(15,23,42,.7);">
                    <i class="fa-solid fa-tag"></i> {{ $produit->categorie }}
                </span>
            </div>
        </div>

        {{-- CONTENU --}}
        <div style="padding:1.25rem;display:flex;flex-direction:column;flex:1;gap:.75rem;">

            <div style="font-weight:700;font-size:1rem;color:var(--text-primary);">{{ $produit->nom }}</div>

            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:1.2rem;font-weight:800;color:var(--ci-orange);">
                    {{ number_format($produit->price, 0, ',', ' ') }}
                    <span style="font-size:.72rem;font-weight:600;opacity:.7;">FCFA</span>
                </div>
                <div style="font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:.3rem;">
                    <i class="fa-solid fa-box"></i> {{ $produit->stock }}
                </div>
            </div>

            <a href="{{ route('show', $produit->id) }}"
               class="btn-secondary"
               style="justify-content:center;text-align:center;font-size:.85rem;">
                <i class="fa-solid fa-eye"></i> {{ __('Voir le détail') }}
            </a>

            {{-- CONTRÔLES PANIER --}}
            <div style="background:var(--bg-input);border-radius:14px;border:1px solid var(--border);padding:.75rem;">
                <div style="display:flex;align-items:center;justify-content:center;gap:.75rem;">

                    <button type="button"
                            onclick="updateCart(this, false)"
                            style="width:36px;height:36px;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;cursor:pointer;font-size:.85rem;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                            onmouseover="this.style.background='rgba(230,57,70,.25)'"
                            onmouseout="this.style.background='rgba(230,57,70,.1)'">
                        <i class="fa-solid fa-minus"></i>
                    </button>

                    <span data-qty-for="{{ $produit->id }}"
                          style="font-weight:800;font-size:1.15rem;min-width:2.5rem;text-align:center;color:var(--ci-orange);">
                        {{ session('panier')[$produit->id] ?? 0 }}
                    </span>

                    <button type="button"
                            onclick="updateCart(this, true)"
                            style="width:36px;height:36px;border-radius:10px;border:1px solid rgba(0,154,68,.3);background:rgba(0,154,68,.1);color:#a7f3d0;cursor:pointer;font-size:.85rem;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                            onmouseover="this.style.background='rgba(0,154,68,.25)'"
                            onmouseout="this.style.background='rgba(0,154,68,.1)'">
                        <i class="fa-solid fa-plus"></i>
                    </button>

                </div>

                {{-- SOUS-TOTAL PAR PRODUIT --}}
                @php $qtyProd = session('panier')[$produit->id] ?? 0; @endphp
                <div data-subtotal-for="{{ $produit->id }}"
                     style="text-align:center;margin-top:.5rem;font-size:.82rem;font-weight:700;color:var(--ci-green);display:{{ $qtyProd > 0 ? 'block' : 'none' }};">
                    = {{ number_format($produit->price * $qtyProd, 0, ',', ' ') }} FCFA
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">
        <i class="fa-solid fa-box-open" style="font-size:2.5rem;opacity:.4;margin-bottom:.75rem;display:block;"></i>
        <p>Aucun produit disponible pour le moment.</p>
    </div>
    @endforelse

</div>

<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

// Prix initiaux depuis PHP (pour calcul total)
const PRICES = {};
@foreach($produits as $produit)
PRICES[{{ $produit->id }}] = {{ $produit->price }};
@endforeach

async function updateCart(btn, isAdd) {
    const card = btn.closest('[data-product-id]');
    const productId = card.dataset.productId;
    const url = isAdd ? card.dataset.addUrl : card.dataset.decUrl;

    btn.disabled = true;
    btn.style.opacity = '.5';

    try {
        await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `_token=${encodeURIComponent(CSRF)}`
        });

        // Mise à jour quantité
        const qtyEl = card.querySelector(`[data-qty-for="${productId}"]`);
        let qty = parseInt(qtyEl.textContent.trim()) || 0;
        qty = isAdd ? qty + 1 : Math.max(0, qty - 1);
        qtyEl.textContent = qty;

        // Sous-total du produit
        const subtotalEl = card.querySelector(`[data-subtotal-for="${productId}"]`);
        if (subtotalEl) {
            if (qty > 0) {
                const price = parseFloat(card.dataset.price);
                subtotalEl.textContent = '= ' + (price * qty).toLocaleString('fr-FR') + ' FCFA';
                subtotalEl.style.display = 'block';
            } else {
                subtotalEl.style.display = 'none';
            }
        }

        recalcCartSummary();

    } catch (e) {
        console.error('Erreur panier', e);
    } finally {
        btn.disabled = false;
        btn.style.opacity = '1';
    }
}

function recalcCartSummary() {
    let totalCount = 0;
    let totalAmount = 0;

    document.querySelectorAll('[data-product-id]').forEach(card => {
        const productId = card.dataset.productId;
        const price = parseFloat(PRICES[productId] || 0);
        const qty = parseInt(card.querySelector(`[data-qty-for="${productId}"]`)?.textContent.trim()) || 0;
        totalCount += qty;
        totalAmount += price * qty;
    });

    // Bandeau total
    const summaryEl = document.getElementById('cartSummary');
    if (totalCount > 0) {
        summaryEl.style.display = 'flex';
        document.getElementById('summaryCount').textContent = totalCount;
        document.getElementById('summaryTotal').textContent = totalAmount.toLocaleString('fr-FR') + ' FCFA';
    } else {
        summaryEl.style.display = 'none';
    }

    // Bouton cart dans le header
    const cartLabel = document.getElementById('cartLabel');
    if (cartLabel) {
        cartLabel.textContent = totalCount > 0
            ? `${totalCount} {{ __('article(s)') }} — ${totalAmount.toLocaleString('fr-FR')} FCFA`
            : '{{ __('Mon panier') }}';
    }
}
</script>

@endsection
