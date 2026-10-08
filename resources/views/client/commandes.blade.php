@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Mes commandes') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Historique de vos achats en boutique.') }}</p>
            </div>
        </div>
        <a href="{{ route('boutique.clientboutique') }}" class="btn-secondary">
            <i class="fa-solid fa-store"></i> {{ __('Retour à la boutique') }}
        </a>
    </div>
</div>

{{-- FILTRE PAR DATE --}}
<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('client.commandes') }}">
        <div style="display:flex;align-items:flex-end;gap:.75rem;flex-wrap:wrap;">
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-calendar-day" style="color:var(--ci-orange)"></i> {{ __('Date début') }}</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       style="padding:.6rem .9rem;font-size:.88rem;">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-calendar-check" style="color:var(--ci-green)"></i> {{ __('Date fin') }}</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       style="padding:.6rem .9rem;font-size:.88rem;">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-filter" style="color:var(--ci-orange)"></i> {{ __('Statut') }}</label>
                <select name="statut" class="form-select" style="padding:.6rem .9rem;font-size:.88rem;">
                    <option value="">{{ __('Tous') }}</option>
                    <option value="payé"       {{ request('statut') === 'payé'       ? 'selected' : '' }}>{{ __('Payée') }}</option>
                    <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                    <option value="annulée"    {{ request('statut') === 'annulée'    ? 'selected' : '' }}>{{ __('Annulée') }}</option>
                </select>
            </div>
            <button type="submit" class="btn" style="padding:.65rem 1.25rem;flex-shrink:0;">
                <i class="fa-solid fa-magnifying-glass"></i> {{ __('Filtrer') }}
            </button>
            @if(request()->hasAny(['date_debut','date_fin','statut']))
            <a href="{{ route('client.commandes') }}" class="btn-secondary" style="padding:.65rem 1.1rem;flex-shrink:0;">
                <i class="fa-solid fa-xmark"></i> {{ __('Réinitialiser') }}
            </a>
            @endif
        </div>

        @if(request()->hasAny(['date_debut','date_fin','statut']))
        <div style="margin-top:.85rem;padding:.6rem 1rem;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);border-radius:10px;font-size:.82rem;color:var(--ci-orange);display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-circle-info"></i>
            {{ __('Filtre actif') }} —
            @if(request('date_debut')) Du <strong>{{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}</strong> @endif
            @if(request('date_fin')) au <strong>{{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}</strong> @endif
            @if(request('statut')) · {{ __('Statut :') }} <strong>{{ request('statut') }}</strong> @endif
            · <strong>{{ $nb }}</strong> {{ __('résultat(s)') }}
        </div>
        @endif
    </form>
</div>

{{-- STATS --}}
<div class="grid" style="grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:1.5rem;">
    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:var(--ci-orange);">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--text-primary);">{{ $nb }}</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Commandes') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-green-dim);border:1px solid var(--ci-green-border);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:var(--ci-green);">
            <i class="fa-solid fa-coins"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary);">{{ number_format($total, 0, ',', ' ') }}</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">FCFA dépensés</div>
    </div>
    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:48px;height:48px;border-radius:14px;background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.25);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:var(--ci-green);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:var(--text-primary);">{{ $payees }}</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Payées') }}</div>
    </div>
</div>

{{-- LISTE DES COMMANDES --}}
@forelse($commandes as $commande)
<div class="card" style="margin-bottom:1.25rem;">

    {{-- EN-TÊTE DE LA COMMANDE --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <div style="width:40px;height:40px;border-radius:12px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;color:var(--ci-orange);font-size:.9rem;flex-shrink:0;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div style="font-weight:700;color:var(--text-primary);">Commande #{{ $commande->id }}</div>
                <div style="font-size:.8rem;color:var(--text-muted);">
                    <i class="fa-regular fa-clock"></i>
                    {{ $commande->created_at?->format('d/m/Y à H:i') ?? '—' }}
                </div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="font-weight:800;font-size:1.1rem;color:var(--ci-green);">
                {{ number_format($commande->total, 0, ',', ' ') }} FCFA
            </div>
            @if($commande->statut === 'payé' || $commande->statut === 'payée')
                <span class="badge badge-success"><i class="fa-solid fa-check"></i> {{ __('Payée') }}</span>
            @elseif($commande->statut === 'en_attente' || $commande->statut === 'en attente')
                <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> {{ __('En attente') }}</span>
            @else
                <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> {{ __('Annulée') }}</span>
            @endif
            <a href="{{ route('commande.facture', $commande->id) }}" class="btn-secondary" style="font-size:.8rem;padding:.4rem .85rem;">
                <i class="fa-solid fa-download"></i> Facture
            </a>
        </div>
    </div>

    {{-- PRODUITS DE LA COMMANDE --}}
    <div style="display:flex;flex-direction:column;gap:.6rem;">
        @foreach($commande->lignes as $ligne)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem .85rem;background:var(--bg-input);border-radius:12px;border:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:.75rem;">
                {{-- Image produit --}}
                @if($ligne->product && $ligne->product->image)
                <img src="{{ $ligne->product->image_url }}"
                     alt="{{ $ligne->product->name }}"
                     style="width:40px;height:40px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                @else
                <div style="width:40px;height:40px;border-radius:8px;background:var(--ci-orange-dim);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--ci-orange);font-size:1rem;">
                    <i class="fa-solid fa-box"></i>
                </div>
                @endif
                <div>
                    <div style="font-weight:600;font-size:.9rem;color:var(--text-primary);">
                        {{ $ligne->product->name ?? 'Produit #' . $ligne->product_id }}
                    </div>
                    @if($ligne->product && $ligne->product->category)
                    <div style="font-size:.75rem;color:var(--text-muted);">{{ $ligne->product->category }}</div>
                    @endif
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:1.5rem;flex-shrink:0;">
                <span class="badge badge-secondary">× {{ $ligne->quantite }}</span>
                <span style="font-size:.82rem;color:var(--text-secondary);">
                    {{ number_format($ligne->prix, 0, ',', ' ') }} FCFA
                </span>
                <span style="font-weight:700;color:var(--ci-green);font-size:.9rem;min-width:90px;text-align:right;">
                    {{ number_format($ligne->prix * $ligne->quantite, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- TOTAL --}}
    <div style="display:flex;justify-content:flex-end;margin-top:1rem;padding-top:.75rem;border-top:1px solid var(--border);gap:1rem;align-items:center;">
        <span style="font-size:.85rem;color:var(--text-secondary);">{{ __('Total') }} :</span>
        <span style="font-size:1.2rem;font-weight:800;color:var(--ci-orange);">
            {{ number_format($commande->total, 0, ',', ' ') }} FCFA
        </span>
    </div>
</div>
@empty
<div class="card" style="text-align:center;padding:3rem;">
    <div style="font-size:3rem;opacity:.3;margin-bottom:1rem;">🛍️</div>
    <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary);margin-bottom:.5rem;">{{ __('Aucune commande pour le moment') }}</div>
    <p style="color:var(--text-secondary);margin-bottom:1.5rem;">{{ __('Explorez la boutique et passez votre première commande !') }}</p>
    <a href="{{ route('boutique.clientboutique') }}" class="btn">
        <i class="fa-solid fa-store"></i> {{ __('Découvrir la boutique') }}
    </a>
</div>
@endforelse

@endsection
