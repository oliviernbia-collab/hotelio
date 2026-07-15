@extends('layouts.app')

@section('content')
    <h1>{{ __('Gestion des charges') }}</h1>

    <div class="card">
        <div class="card-title"><span>{{ __('Ajouter une charge') }}</span></div>
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="grid">
                <div class="form-group"><label>{{ __('Hôtel') }}</label><select name="hotel_id" required>@foreach($hotels as $hotel)<option value="{{ $hotel->id }}">{{ $hotel->name }}</option>@endforeach</select></div>
                <div class="form-group"><label>{{ __('Description') }}</label><input type="text" name="description" required></div>
                <div class="form-group"><label>{{ __('Montant') }}</label><input type="number" name="amount" value="0" required></div>
                <div class="form-group"><label>{{ __('Type') }}</label><input type="text" name="expense_type"></div>
                <div class="form-group"><label>{{ __('Date') }}</label><input type="date" name="occurred_at"></div>
                <div class="form-group"><label>{{ __('Notes') }}</label><textarea name="notes"></textarea></div>
                <div class="form-group"><button class="btn" type="submit">{{ __('Ajouter') }}</button></div>
            </div>

        </form>
    </div>

    <div class="card">
        <div class="card-title"><span>{{ __('Liste des charges') }}</span></div>
        <div class="table-responsive">
        <table class="table">
            <thead><tr><th>{{ __('Hôtel') }}</th><th>{{ __('Description') }}</th><th>{{ __('Type') }}</th><th>{{ __('Montant') }}</th><th>{{ __('Date') }}</th></tr></thead>
            <tbody>@foreach($expenses as $expense)<tr><td>{{ $expense->hotel->name }}</td><td>{{ $expense->description }}</td><td>{{ $expense->expense_type }}</td><td>{{ number_format($expense->amount,0,',',' ') }} FCFA</td><td>{{ optional($expense->occurred_at)->format('Y-m-d') }}</td></tr>@endforeach</tbody>
        </table>
        </div>
        {{ $expenses->links() ?? '' }}
    </div>
@endsection
