@extends('layouts.app')

@section('content')
    <h1>{{ __('Gestion des plats et boissons') }}</h1>

    <div class="card">
        <div class="card-title"><span>{{ __('Ajouter un plat ou une boisson') }}</span></div>
        <form method="POST" action="{{ route('menu.store') }}">
            @csrf
            <div class="grid">
                <div class="form-group">
                    <label>{{ __('Hôtel') }}</label>
                    <select name="hotel_id" required>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>{{ __('Nom') }}</label><input type="text" name="name" required></div>
                <div class="form-group"><label>{{ __('Type') }}</label><select name="item_type"><option value="plat">{{ __('Plat') }}</option><option value="boisson">{{ __('Boisson') }}</option></select></div>
                <div class="form-group"><label>{{ __('Catégorie') }}</label><input type="text" name="category"></div>
                <div class="form-group"><label>{{ __('Prix') }}</label><input type="number" name="price" value="0" required></div>
                <div class="form-group"><label>{{ __('Stock') }}</label><input type="number" name="stock" value="0" required></div>
                <div class="form-group"><label>{{ __('Emoji') }}</label><input type="text" name="emoji"></div>
                <div class="form-group"><label>{{ __('Description') }}</label><textarea name="description"></textarea></div>
                <div class="form-group"><label>{{ __('Disponible') }}</label><select name="available"><option value="1">{{ __('Oui') }}</option><option value="0">{{ __('Non') }}</option></select></div>
                <div class="form-group"><button class="btn" type="submit">{{ __('Ajouter') }}</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-title"><span>{{ __('Articles du menu') }}</span></div>
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>{{ __('Hôtel') }}</th><th>{{ __('Nom') }}</th><th>{{ __('Type') }}</th><th>{{ __('Catégorie') }}</th><th>{{ __('Prix') }}</th><th>{{ __('Stock') }}</th><th>{{ __('Disponible') }}</th></tr>
            </thead>
            <tbody>
                @foreach($menuItems as $item)
                    <tr>
                        <td>{{ $item->hotel->name }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ ucfirst($item->item_type) }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $item->stock }}</td>
                        <td>{{ $item->available ? __('Oui') : __('Non') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        {{ $menuItems->links() ?? '' }}
    </div>
@endsection
