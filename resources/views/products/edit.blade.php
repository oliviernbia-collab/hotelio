@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Modifier le produit') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ $product->name }}</p>
            </div>
        </div>
        <a href="{{ route('boutique.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Retour à la boutique') }}
        </a>
    </div>
</div>

<div style="max-width:680px;margin:0 auto;">
    <div class="card">

        @if($product->image)
        <div style="margin-bottom:1.5rem;border-radius:16px;overflow:hidden;height:200px;background:var(--bg-input);">
            <img src="{{ asset('storage/' . $product->image) }}"
                 alt="{{ $product->name }}"
                 style="width:100%;height:100%;object-fit:cover;">
        </div>
        @endif

        <form method="POST"
              action="{{ route('products.update', $product->id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.625rem;">
                    <label><i class="fa-solid fa-signature"></i> {{ __('Nom') }}</label>
                    <input name="name" value="{{ $product->name }}" required>
                </div>
                <div class="form-group" style="padding-left:.625rem;">
                    <label><i class="fa-solid fa-layer-group"></i> {{ __('Catégorie') }}</label>
                    <input name="category" value="{{ $product->category }}">
                </div>
            </div>

            <div class="grid" style="grid-template-columns:1fr 1fr;gap:0;">
                <div class="form-group" style="padding-right:.625rem;">
                    <label><i class="fa-solid fa-coins"></i> {{ __('Prix (FCFA)') }}</label>
                    <input type="number" name="price" value="{{ $product->price }}" required>
                </div>
                <div class="form-group" style="padding-left:.625rem;">
                    <label><i class="fa-solid fa-cubes"></i> {{ __('Stock') }}</label>
                    <input type="number" name="stock" value="{{ $product->stock }}" required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-icons"></i> {{ __('Emoji') }}</label>
                <input name="emoji" value="{{ $product->emoji }}" placeholder="{{ __('Ex : 💧') }}">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left"></i> {{ __('Description') }}</label>
                <textarea name="description" rows="3">{{ $product->description }}</textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i> {{ __('Nouvelle image (optionnel)') }}</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:.5rem;">
                <a href="{{ route('boutique.index') }}" class="btn-secondary">{{ __('Annuler') }}</a>
                <button type="submit" class="btn">
                    <i class="fa-solid fa-check"></i> {{ __('Enregistrer les modifications') }}
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
