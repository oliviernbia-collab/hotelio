@extends('layouts.app')

@section('content')
    <div class="card card-title" style="margin-bottom:1.5rem;">
        <div class="card-title">
            <div>
                <div style="font-size:1.4rem;font-weight:700;">{{ __('Clients') }}</div>
                <p style="margin:.5rem 0 0;color:#94a3b8;">{{ __('Suivi des profils des clients et de leurs dernières visites.') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Nom') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Statut') }}</th>
                    <th>{{ __('Dernière visite') }}</th>
                    <th>{{ __('Dépenses') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->code }}</td>
                        <td>{{ $client->first_name }} {{ $client->last_name }}</td>
                        <td>{{ $client->email }}</td>
                        <td><span class="tag">{{ $client->status }}</span></td>
                        <td>{{ $client->last_visit?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ number_format($client->total_spend,0,',',' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr><td colspan="6">{{ __('Aucun client pour le moment.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div style="margin-top:1rem;">{{ $clients->links() }}</div>
@endsection
