@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-title">
        <div>
            <div style="font-size:1.2rem;font-weight:700"><i class="fa-solid fa-hand-holding-dollar" style="color:var(--ci-orange)"></i> {{ __('Reversements aux propriétaires') }}</div>
            <p style="margin:.3rem 0 0;color:var(--text-secondary);font-size:.88rem;">
                {{ __('Montant dû à chaque propriétaire (total encaissé − commission Hôtelio) sur les réservations apportées par la plateforme. Les paiements aux propriétaires se font manuellement (virement, mobile money) — enregistrez-les ici une fois effectués.') }}
            </p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="card" style="margin-bottom:1.5rem;border-color:rgba(0,154,68,.35);background:rgba(0,154,68,.08)">
    <i class="fa-solid fa-circle-check" style="color:var(--ci-green)"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="card" style="margin-bottom:1.5rem;border-color:rgba(230,57,70,.35);background:rgba(230,57,70,.08)">
    <i class="fa-solid fa-circle-exclamation" style="color:#E63946"></i> {{ session('error') }}
</div>
@endif

@if($failedPayouts->isNotEmpty())
<div class="card" style="margin-bottom:1.5rem;border-color:rgba(230,57,70,.35)">
    <div class="card-title"><span><i class="fa-solid fa-triangle-exclamation" style="color:#E63946"></i> {{ __('Virements automatiques en échec') }}</span></div>
    <p style="color:#94a3b8;font-size:.85rem;margin-bottom:1rem">{{ __('Le virement Jèko automatique a échoué pour ces réservations — vérifiez les coordonnées de paiement du propriétaire ou le solde du magasin, puis relancez.') }}</p>
    <div class="table-responsive">
    <table class="table">
        <thead><tr><th>{{ __('Réservation') }}</th><th>{{ __('Hôtel') }}</th><th>{{ __('Montant net') }}</th><th>{{ __('Raison') }}</th><th>{{ __('Actions') }}</th></tr></thead>
        <tbody>
        @foreach($failedPayouts as $r)
            <tr>
                <td style="font-size:.85rem">{{ $r->code }}</td>
                <td style="font-size:.85rem">{{ $r->hotel->short_name ?? $r->hotel->name ?? '—' }}</td>
                <td style="font-size:.85rem">{{ number_format($r->total - ($r->commission_amount ?? 0), 0, ',', ' ') }} FCFA</td>
                <td style="font-size:.78rem;color:#fca5a5">{{ $r->payout_failure_reason ?? '—' }}</td>
                <td>
                    <form method="POST" action="{{ route('payouts.retry', $r) }}">
                        @csrf
                        <button type="submit" style="padding:.35rem .7rem;font-size:.78rem;border-radius:10px;border:1px solid rgba(0,154,68,.3);background:rgba(0,154,68,.1);color:#a7f3d0;cursor:pointer;">
                            <i class="fa-solid fa-rotate-right"></i> {{ __('Relancer') }}
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

{{-- TABLEAU --}}
<div class="card">
    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Propriétaire') }}</th>
                <th>{{ __('Total dû') }}</th>
                <th>{{ __('Déjà versé') }}</th>
                <th>{{ __('Solde restant') }}</th>
                <th>{{ __('Dernier versement') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prestataires as $p)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $p->name }}</div>
                    <div style="font-size:.78rem;color:var(--text-muted)">{{ $p->email }}</div>
                </td>
                <td style="font-size:.85rem">{{ number_format($p->owed, 0, ',', ' ') }} FCFA</td>
                <td style="font-size:.85rem">{{ number_format($p->alreadyPaid, 0, ',', ' ') }} FCFA</td>
                <td>
                    <span class="badge badge-{{ $p->balance > 0 ? 'warning' : 'success' }}" style="font-size:.85rem">
                        {{ number_format($p->balance, 0, ',', ' ') }} FCFA
                    </span>
                </td>
                <td style="font-size:.8rem;color:var(--text-muted)">
                    {{ $p->lastPayout?->paid_at?->format('d/m/Y') ?? '—' }}
                </td>
                <td>
                    @if($p->balance > 0)
                    <button type="button" onclick="document.getElementById('payoutForm{{ $p->id }}').classList.toggle('hidden')"
                            style="padding:.35rem .7rem;font-size:.78rem;display:flex;align-items:center;gap:.25rem;border-radius:10px;border:1px solid rgba(0,154,68,.3);background:rgba(0,154,68,.1);color:#a7f3d0;cursor:pointer;">
                        <i class="fa-solid fa-money-check-dollar"></i> {{ __('Enregistrer un versement') }}
                    </button>
                    <form id="payoutForm{{ $p->id }}" method="POST" action="{{ route('payouts.record', $p) }}" class="hidden" style="margin-top:.6rem;display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
                        @csrf
                        <input type="number" name="amount" value="{{ $p->balance }}" min="1" max="{{ $p->balance }}" required
                               style="width:140px;padding:.4rem .6rem;border-radius:8px;border:1px solid rgba(148,163,184,.25);background:rgba(148,163,184,.06);color:inherit;">
                        <input type="text" name="notes" placeholder="{{ __('Note (optionnel)') }}"
                               style="width:180px;padding:.4rem .6rem;border-radius:8px;border:1px solid rgba(148,163,184,.25);background:rgba(148,163,184,.06);color:inherit;">
                        <button type="submit" class="btn" style="padding:.4rem .9rem;font-size:.78rem;">{{ __('Confirmer') }}</button>
                    </form>
                    @else
                    <span style="font-size:.78rem;color:var(--text-muted)">{{ __('À jour') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:2rem">{{ __('Aucun propriétaire trouvé.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection
