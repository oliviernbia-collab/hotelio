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
                <tr><th>{{ __('Hôtel') }}</th><th>{{ __('Nom') }}</th><th>{{ __('Type') }}</th><th>{{ __('Catégorie') }}</th><th>{{ __('Prix') }}</th><th>{{ __('Stock') }}</th><th>{{ __('Disponible') }}</th><th>{{ __('Actions') }}</th></tr>
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
                        <td style="white-space:nowrap;">
                            <button type="button" class="action-link" title="{{ __('Modifier') }}"
                                onclick="openMenuEdit({{ $item->id }}, {{ $item->hotel_id }}, '{{ addslashes($item->name) }}', '{{ $item->item_type }}', '{{ addslashes($item->category ?? '') }}', {{ $item->price }}, {{ $item->stock }}, '{{ addslashes($item->emoji ?? '') }}', '{{ addslashes($item->description ?? '') }}', {{ $item->available ? 1 : 0 }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form method="POST" action="{{ route('menu.destroy', $item->id) }}" style="display:inline;"
                                  onsubmit="return confirm('{{ __('Supprimer cet article ?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-link red" title="{{ __('Supprimer') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        {{ $menuItems->links() ?? '' }}
    </div>

    {{-- MODAL ÉDITION --}}
    <div id="menu-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
        <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                <div style="font-weight:700;font-size:1.1rem;"><i class="fa-solid fa-pen"></i> {{ __('Modifier l\'article') }}</div>
                <button type="button" onclick="closeMenuEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="menu-edit-form" method="POST" action="">
                @csrf @method('PUT')
                <input type="hidden" name="hotel_id" id="me-hotel">
                <div class="form-group">
                    <label>{{ __('Nom') }} <span style="color:#e63946;">*</span></label>
                    <input type="text" name="name" id="me-name" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Type') }}</label>
                        <select name="item_type" id="me-type">
                            <option value="plat">{{ __('Plat') }}</option>
                            <option value="boisson">{{ __('Boisson') }}</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Catégorie') }}</label>
                        <input type="text" name="category" id="me-category">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Prix') }}</label>
                        <input type="number" name="price" id="me-price" min="0" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Stock') }}</label>
                        <input type="number" name="stock" id="me-stock" min="0" required>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:70px 1fr;gap:1rem;margin-top:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Emoji') }}</label>
                        <input type="text" name="emoji" id="me-emoji" maxlength="4" style="text-align:center;">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Disponible') }}</label>
                        <select name="available" id="me-available">
                            <option value="1">{{ __('Oui') }}</option>
                            <option value="0">{{ __('Non') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-top:1rem;">
                    <label>{{ __('Description') }}</label>
                    <textarea name="description" id="me-desc" rows="2" style="resize:vertical;"></textarea>
                </div>
                <div style="display:flex;gap:.75rem;">
                    <button type="button" onclick="closeMenuEdit()" class="btn-secondary" style="flex:1;justify-content:center;">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn" style="flex:1;justify-content:center;">
                        <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const modal = document.getElementById('menu-modal');
        const form  = document.getElementById('menu-edit-form');
        window.openMenuEdit = function (id, hotelId, name, type, category, price, stock, emoji, desc, available) {
            form.action = '/menu/' + id;
            document.getElementById('me-hotel').value     = hotelId;
            document.getElementById('me-name').value      = name;
            document.getElementById('me-type').value      = type;
            document.getElementById('me-category').value  = category;
            document.getElementById('me-price').value     = price;
            document.getElementById('me-stock').value     = stock;
            document.getElementById('me-emoji').value     = emoji;
            document.getElementById('me-desc').value      = desc;
            document.getElementById('me-available').value = available;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };
        window.closeMenuEdit = function () { modal.style.display = 'none'; document.body.style.overflow = ''; };
        modal.addEventListener('click', e => { if (e.target === modal) closeMenuEdit(); });
    })();
    </script>
@endsection
