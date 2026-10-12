@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-title">
        <div>
            <div style="font-size:1.2rem;font-weight:700"><i class="fa-solid fa-credit-card" style="color:var(--ci-orange)"></i> {{ __('Abonnements prestataires') }}</div>
            <p style="margin:.3rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ $subscriptions->total() }} {{ __('abonnement(s) enregistré(s).') }}</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            @foreach(['' => 'Tous', 'active' => 'Actifs', 'past_due' => 'En retard', 'pending_payment' => 'En attente'] as $value => $label)
            <a href="{{ route('subscriptions.index', $value ? ['status' => $value] : []) }}"
               class="{{ request('status', '') === $value ? 'btn' : 'btn-secondary' }}"
               style="padding:.5rem 1rem;font-size:.82rem;">{{ __($label) }}</a>
            @endforeach
        </div>
    </div>
</div>

@if(session('success'))
<div class="card" style="margin-bottom:1.5rem;border-color:rgba(0,154,68,.35);background:rgba(0,154,68,.08)">
    <i class="fa-solid fa-circle-check" style="color:var(--ci-green)"></i> {{ session('success') }}
</div>
@endif

{{-- TABLEAU --}}
<div class="card">
    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Prestataire') }}</th>
                <th>{{ __('Palier') }}</th>
                <th>{{ __('Statut') }}</th>
                <th>{{ __('Début') }}</th>
                <th>{{ __('Échéance') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $subscription)
            <tr>
                <td style="color:var(--text-muted);font-size:.8rem">#{{ $subscription->id }}</td>
                <td>
                    <div style="font-weight:600">{{ $subscription->user->name ?? '—' }}</div>
                    <div style="font-size:.78rem;color:var(--text-muted)">{{ $subscription->user->email ?? '' }}</div>
                </td>
                <td>
                    <span class="badge badge-primary">{{ $subscription->plan->name ?? '—' }}</span>
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:.2rem">{{ number_format($subscription->plan->price_monthly ?? 0, 0, ',', ' ') }} FCFA/mois</div>
                </td>
                <td>
                    <span class="badge badge-{{ match($subscription->status) { 'active' => 'success', 'past_due' => 'danger', 'pending_payment' => 'warning', default => 'secondary' } }}">
                        {{ match($subscription->status) { 'active' => __('Actif'), 'past_due' => __('En retard'), 'pending_payment' => __('En attente'), 'cancelled' => __('Annulé'), default => $subscription->status } }}
                    </span>
                </td>
                <td style="font-size:.85rem">{{ $subscription->started_at?->format('d/m/Y') ?? '—' }}</td>
                <td style="font-size:.85rem">{{ $subscription->current_period_end?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    <form method="POST" action="{{ route('subscriptions.payment', $subscription) }}" style="display:inline" onsubmit="return confirm('{{ __('Confirmer la réception du paiement mensuel ?') }}')">
                        @csrf
                        <button type="submit"
                                style="padding:.35rem .7rem;font-size:.78rem;display:flex;align-items:center;gap:.25rem;border-radius:10px;border:1px solid rgba(0,154,68,.3);background:rgba(0,154,68,.1);color:#a7f3d0;cursor:pointer;"
                                title="{{ __('Enregistrer le paiement du mois') }}">
                            <i class="fa-solid fa-money-check-dollar"></i> {{ __('Paiement reçu') }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem">{{ __('Aucun abonnement trouvé.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">
        {{ $subscriptions->links() }}
    </div>
</div>

@endsection
