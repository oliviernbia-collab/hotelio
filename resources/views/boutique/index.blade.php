@extends('layouts.app')

@section('content')

@push('styles')
<style>
/* ── Image produit ── */
.product-img-wrap {
    width: 100%;
    height: 160px;
    border-radius: 12px;
    overflow: hidden;
    background: var(--bg-input);
    margin-bottom: 1rem;
    flex-shrink: 0;
}
.product-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
}
.product-img-wrap:hover img { transform: scale(1.06); }

.btn-danger-sm {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .5rem .9rem;
    border-radius: 10px;
    border: 1px solid rgba(230,57,70,.3);
    background: rgba(230,57,70,.1);
    color: #fca5a5;
    font-size: .82rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s;
}
.btn-danger-sm:hover { background: rgba(230,57,70,.25); }

/* ── MOBILE ── */
@media (max-width: 767px) {
    /* Catalogue + formulaire en colonne unique */
    .boutique-layout {
        grid-template-columns: 1fr !important;
    }
    /* Produits en liste à 1 colonne */
    .products-grid {
        grid-template-columns: 1fr !important;
    }
    /* Carte produit horizontale */
    .product-card {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: .85rem !important;
        padding: .85rem !important;
    }
    /* Vignette carrée à gauche */
    .product-card .product-img-wrap {
        width: 72px !important;
        height: 72px !important;
        border-radius: 12px !important;
        margin-bottom: 0 !important;
        flex-shrink: 0 !important;
    }
    /* Corps texte occupe l'espace restant */
    .product-card .product-body {
        flex: 1;
        min-width: 0;
    }
    /* Nom sur une ligne */
    .product-card .product-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: .9rem !important;
    }
    /* Prix + stock en ligne */
    .product-card .product-price-row {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: wrap;
        margin-bottom: .5rem !important;
    }
}
</style>
@endpush

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-store"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Boutique') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Gérez les produits et le stock disponible.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ONGLETS --}}
@if(auth()->user()->role === 'prestataire')
<div class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:1.25rem;">
    <a href="{{ route('products.index') }}"
       class="tag {{ ($vue ?? 'tous') === 'tous' ? 'active' : '' }}">
        <i class="fa-solid fa-store"></i> {{ __('Tous les produits') }}
    </a>
    <a href="{{ route('products.index', ['vue' => 'mes']) }}"
       class="tag {{ ($vue ?? 'tous') === 'mes' ? 'active' : '' }}">
        <i class="fa-solid fa-box"></i> {{ __('Mes produits') }}
    </a>
</div>
@endif

{{-- GRID CATALOGUE + FORMULAIRE --}}
<div class="grid boutique-layout" style="grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">

    {{-- CATALOGUE --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-boxes-stacked"></i>
                {{ ($vue ?? 'tous') === 'mes' ? __('Mes produits') : __('Catalogue') }}
                ({{ $products->total() }} {{ __('produits') }})
            </span>
        </div>

        <div class="grid products-grid" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;">
            @forelse($products as $product)
            <div class="card card-hover product-card" style="padding:1rem;background:var(--bg-input);">

                <div class="product-img-wrap">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="{{ __('image par défaut') }}">
                    @endif
                </div>

                <div class="product-body">
                {{-- NOM --}}
                <div class="product-name" style="display:flex;align-items:center;gap:.5rem;font-weight:700;font-size:.95rem;margin-bottom:.25rem;">
                    @if($product->emoji)<span>{{ $product->emoji }}</span>@endif
                    {{ $product->name }}
                </div>

                {{-- CATÉGORIE --}}
                <div style="margin-bottom:.5rem;">
                    <span class="badge badge-secondary">
                        <i class="fa-solid fa-layer-group"></i> {{ $product->category }}
                    </span>
                </div>

                {{-- PRIX + STOCK --}}
                <div class="product-price-row" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                    <div style="font-weight:800;color:var(--ci-green);font-size:.95rem;">
                        <i class="fa-solid fa-coins"></i>
                        {{ number_format($product->price, 0, ',', ' ') }} <span style="font-size:.72rem;opacity:.7;">FCFA</span>
                    </div>
                    <span class="tag" style="font-size:.78rem;">
                        <i class="fa-solid fa-box"></i> {{ $product->stock }}
                    </span>
                </div>

                {{-- BADGE autre prestataire --}}
                @if($product->user_id && $product->user_id !== auth()->id())
                <div style="margin-bottom:.6rem;">
                    <span style="padding:.2rem .55rem;border-radius:999px;font-size:.7rem;font-weight:600;background:rgba(148,163,184,.12);color:#94a3b8;border:1px solid rgba(148,163,184,.2);">
                        <i class="fa-solid fa-user"></i> {{ __('Autre prestataire') }}
                    </span>
                </div>
                @endif

                {{-- ACTIONS --}}
                @if(!$product->user_id || $product->user_id === auth()->id() || auth()->user()->role === 'admin')
                <div style="display:flex;gap:.5rem;">
                    <a href="{{ route('products.edit', $product->id) }}"
                       class="btn-secondary"
                       style="flex:1;justify-content:center;font-size:.82rem;">
                        <i class="fa-solid fa-pen"></i> {{ __('Modifier') }}
                    </a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                          onsubmit="return confirm('{{ __('Supprimer ce produit ?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-sm">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
                </div>{{-- /product-body --}}

            </div>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">
                <i class="fa-solid fa-box-open" style="font-size:2rem;opacity:.4;margin-bottom:.5rem;display:block;"></i>
                <p>{{ __('Aucun produit dans le catalogue.') }}</p>
            </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($products->hasPages())
        <div style="margin-top:1.25rem;">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- FORMULAIRE AJOUT --}}
    <div class="card" style="align-self:start;">
        <div class="card-title" style="margin-bottom:1.25rem;">
            <span><i class="fa-solid fa-plus-circle"></i> {{ __('Ajouter un produit') }}</span>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label><i class="fa-solid fa-signature"></i> {{ __('Nom') }}</label>
                <input name="name" placeholder="{{ __('Ex : Eau minérale') }}" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-coins"></i> {{ __('Prix (FCFA)') }}</label>
                <input type="number" name="price" placeholder="0" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-cubes"></i> {{ __('Stock') }}</label>
                <input type="number" name="stock" placeholder="0" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-layer-group"></i> {{ __('Catégorie') }}</label>
                <input name="category" placeholder="{{ __('Ex : Boissons') }}">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-icons"></i> {{ __('Emoji') }}</label>
                <input name="emoji" placeholder="{{ __('Ex : 💧') }}">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left"></i> {{ __('Description') }}</label>
                <textarea rows="3" name="description" placeholder="{{ __('Description du produit…') }}"></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i> {{ __('Image produit') }}</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-check"></i> {{ __('Ajouter le produit') }}
            </button>
        </form>
    </div>

</div>

@endsection
