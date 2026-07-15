@extends('layouts.app')

@section('content')
    <div class="hero card">
        <div class="hero-badge">{{ __('Résumé hôtelier') }}</div>
        <h2>{{ __('Bienvenue sur Hôtelio') }}</h2>
        <p>{{ __('Gérez les hôtels, les réservations, les clients et la boutique depuis une seule interface.') }}</p>
    </div>

    <div class="cards-row" style="margin-top:1.5rem;">
        <div class="card card-sm">
            <div class="card-title"><span>{{ __('Hôtels actifs') }}</span><span class="badge badge-success">{{ $hotels->count() }}</span></div>
            <div style="font-size:2rem;font-weight:700;">{{ $hotels->count() }}</div>
            <p style="margin: .75rem 0 0; color:#94a3b8;">{{ __("Nombre total d'hôtels enregistrés.") }}</p>
        </div>
        <div class="card card-sm">
            <div class="card-title"><span>{{ __('Clients') }}</span><span class="badge badge-warning">{{ $clients->count() }}</span></div>
            <div style="font-size:2rem;font-weight:700;">{{ $clients->count() }}</div>
            <p style="margin: .75rem 0 0; color:#94a3b8;">{{ __('Clients récents avec réservations.') }}</p>
        </div>
        <div class="card card-sm">
            <div class="card-title"><span>{{ __('Revenu total') }}</span><span class="badge badge-success">FCFA</span></div>
            <div style="font-size:2rem;font-weight:700;">{{ number_format($hotels->sum('revenue'), 0, ',', ' ') }}</div>
            <p style="margin: .75rem 0 0; color:#94a3b8;">{{ __('Revenu combiné des hôtels disponibles.') }}</p>
        </div>
        <div class="card card-sm">
            <div class="card-title"><span>{{ __('Réservations') }}</span><span class="badge badge-danger">{{ $reservations->count() }}</span></div>
            <div style="font-size:2rem;font-weight:700;">{{ $reservations->count() }}</div>
            <p style="margin: .75rem 0 0; color:#94a3b8;">{{ __('Réservations récentes dans le système.') }}</p>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 2fr 1fr; margin-top:1.5rem; gap:1.25rem;">
        <div class="card">
            <div class="card-title"><span>{{ __('Dernières réservations') }}</span></div>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Hôtel') }}</th>
                        <th>{{ __('Statut') }}</th>
                        <th>{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->code }}</td>
                            <td>{{ $reservation->client->first_name }} {{ $reservation->client->last_name }}</td>
                            <td>{{ $reservation->hotel->short_name }}</td>
                            <td><span class="tag">{{ ucfirst($reservation->status) }}</span></td>
                            <td>{{ number_format($reservation->total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">{{ __('Aucune réservation récente.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="card">
            <div class="card-title"><span>{{ __('Clients récents') }}</span></div>
            <div class="meta-list">
                @forelse($clients as $client)
                    <div class="meta-line"><span>{{ $client->first_name }} {{ $client->last_name }}</span><span>{{ $client->status }}</span></div>
                @empty
                    <div>{{ __('Aucun client trouvé.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
