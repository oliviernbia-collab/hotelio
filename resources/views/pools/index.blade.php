@extends('layouts.app')

@section('content')
    <h1>{{ __('Gestion des piscines') }}</h1>

    <div class="card">
        <div class="card-title"><span>{{ __('Ajouter une piscine') }}</span></div>
        <form method="POST" action="{{ route('pools.store') }}">
            @csrf
            <div class="grid">
                <div class="form-group"><label>{{ __('Hôtel') }}</label><select name="hotel_id" required>@foreach($hotels as $hotel)<option value="{{ $hotel->id }}">{{ $hotel->name }}</option>@endforeach</select></div>
                <div class="form-group"><label>{{ __('Nom') }}</label><input type="text" name="name" required></div>
                <div class="form-group"><label>{{ __('Statut') }}</label><input type="text" name="status" value="ouverte" required></div>
                <div class="form-group"><label>{{ __('Surface (m²)') }}</label><input type="number" name="area"></div>
                <div class="form-group"><label>{{ __('Profondeur') }}</label><input type="number" step="0.1" name="depth"></div>
                <div class="form-group"><label>{{ __('Température') }}</label><input type="number" step="0.1" name="temperature"></div>
                <div class="form-group"><label>{{ __('Description') }}</label><textarea name="description"></textarea></div>
                <div class="form-group"><button class="btn" type="submit">{{ __('Ajouter') }}</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-title"><span>{{ __('Liste des piscines') }}</span></div>
        <div class="table-responsive">
        <table class="table">
            <thead><tr><th>{{ __('Hôtel') }}</th><th>{{ __('Nom') }}</th><th>{{ __('Statut') }}</th><th>{{ __('Surface') }}</th><th>{{ __('Profondeur') }}</th><th>{{ __('Température') }}</th></tr></thead>
            <tbody>@foreach($pools as $pool)<tr><td>{{ $pool->hotel->name }}</td><td>{{ $pool->name }}</td><td>{{ $pool->status }}</td><td>{{ $pool->area }}</td><td>{{ $pool->depth }}</td><td>{{ $pool->temperature }}</td></tr>@endforeach</tbody>
        </table>
        </div>
        {{ $pools->links() ?? '' }}
    </div>
@endsection
