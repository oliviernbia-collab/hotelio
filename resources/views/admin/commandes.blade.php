@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __('Dashboard Commandes') }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Suivi des ventes et commandes boutique.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="cards-row" style="margin-bottom:1.5rem;">
    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:52px;height:52px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:var(--ci-orange);">
            <i class="fa-solid fa-coins"></i>
        </div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);" data-count="{{ $total }}">0</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">FCFA — {{ __('Total ventes') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:52px;height:52px;border-radius:14px;background:var(--ci-green-dim);border:1px solid var(--ci-green-border);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:var(--ci-green);">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);" data-count="{{ $nb }}">0</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Nombre de commandes') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:52px;height:52px;border-radius:14px;background:rgba(247,127,0,.12);border:1px solid rgba(247,127,0,.25);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:#fbbf24;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);">{{ $commandes->where('statut', 'en attente')->count() }}</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('En attente') }}</div>
    </div>

    <div class="card" style="text-align:center;padding:1.5rem;">
        <div style="width:52px;height:52px;border-radius:14px;background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.25);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.2rem;color:var(--ci-green);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);">{{ $commandes->where('statut', 'payée')->count() }}</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ __('Payées') }}</div>
    </div>
</div>

{{-- LISTE DES COMMANDES --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-receipt"></i> {{ __('Liste des commandes') }}</span>
        <span class="badge badge-secondary">{{ $commandes->count() }}</span>
    </div>

    @forelse($commandes as $c)
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;transition:border-color .2s;"
         onmouseover="this.style.borderColor='var(--ci-orange-border)'"
         onmouseout="this.style.borderColor='var(--border)'">

        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:40px;height:40px;border-radius:12px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;color:var(--ci-orange);font-size:.9rem;flex-shrink:0;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div style="font-weight:700;color:var(--text-primary);">Commande #{{ $c->id }}</div>
                <div style="font-size:.8rem;color:var(--text-muted);">{{ $c->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:1.5rem;">
            <div style="font-weight:800;font-size:1.05rem;color:var(--ci-green);">
                {{ number_format($c->total, 0, ',', ' ') }} FCFA
            </div>
            @if($c->statut === 'payée')
                <span class="badge badge-success"><i class="fa-solid fa-check"></i> {{ __('Payée') }}</span>
            @elseif($c->statut === 'en attente')
                <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> {{ __('En attente') }}</span>
            @else
                <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> {{ __('Annulée') }}</span>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3rem;color:var(--text-muted);">
        <i class="fa-solid fa-receipt" style="font-size:2.5rem;opacity:.4;margin-bottom:.75rem;display:block;"></i>
        <p>Aucune commande enregistrée.</p>
    </div>
    @endforelse
</div>

@endsection
