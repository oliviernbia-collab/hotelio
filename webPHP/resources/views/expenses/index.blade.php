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
            <thead><tr><th>{{ __('Hôtel') }}</th><th>{{ __('Description') }}</th><th>{{ __('Type') }}</th><th>{{ __('Montant') }}</th><th>{{ __('Date') }}</th><th>{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->hotel->name }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->expense_type }}</td>
                    <td>{{ number_format($expense->amount,0,',',' ') }} FCFA</td>
                    <td>{{ optional($expense->occurred_at)->format('Y-m-d') }}</td>
                    <td style="white-space:nowrap;">
                        <button type="button" class="action-link" title="{{ __('Modifier') }}"
                            onclick="openExpenseEdit({{ $expense->id }}, {{ $expense->hotel_id }}, '{{ addslashes($expense->description) }}', {{ $expense->amount }}, '{{ addslashes($expense->expense_type ?? '') }}', '{{ optional($expense->occurred_at)->format('Y-m-d') }}', '{{ addslashes($expense->notes ?? '') }}')">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" style="display:inline;"
                              onsubmit="return confirm('{{ __('Supprimer cette charge ?') }}')">
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
        {{ $expenses->links() ?? '' }}
    </div>

    {{-- MODAL ÉDITION --}}
    <div id="expense-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(2,6,23,.72);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
        <div style="background:var(--bg-card);border:1px solid var(--card-border);border-radius:20px;padding:1.75rem;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                <div style="font-weight:700;font-size:1.1rem;"><i class="fa-solid fa-pen"></i> {{ __('Modifier la charge') }}</div>
                <button type="button" onclick="closeExpenseEdit()" style="width:34px;height:34px;border-radius:10px;border:1px solid var(--border);background:var(--bg-input);color:var(--text-secondary);cursor:pointer;font-size:.9rem;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="expense-edit-form" method="POST" action="">
                @csrf @method('PUT')
                <input type="hidden" name="hotel_id" id="ee-hotel">
                <div class="form-group">
                    <label>{{ __('Description') }} <span style="color:#e63946;">*</span></label>
                    <input type="text" name="description" id="ee-desc" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Montant') }}</label>
                        <input type="number" name="amount" id="ee-amount" min="0" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>{{ __('Type') }}</label>
                        <input type="text" name="expense_type" id="ee-type">
                    </div>
                </div>
                <div class="form-group" style="margin-top:1rem;">
                    <label>{{ __('Date') }}</label>
                    <input type="date" name="occurred_at" id="ee-date">
                </div>
                <div class="form-group" style="margin-top:1rem;">
                    <label>{{ __('Notes') }}</label>
                    <textarea name="notes" id="ee-notes" rows="2" style="resize:vertical;"></textarea>
                </div>
                <div style="display:flex;gap:.75rem;">
                    <button type="button" onclick="closeExpenseEdit()" class="btn-secondary" style="flex:1;justify-content:center;">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn" style="flex:1;justify-content:center;">
                        <i class="fa-solid fa-check"></i> {{ __('Enregistrer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const modal = document.getElementById('expense-modal');
        const form  = document.getElementById('expense-edit-form');
        window.openExpenseEdit = function (id, hotelId, desc, amount, type, date, notes) {
            form.action = '/expenses/' + id;
            document.getElementById('ee-hotel').value  = hotelId;
            document.getElementById('ee-desc').value   = desc;
            document.getElementById('ee-amount').value = amount;
            document.getElementById('ee-type').value   = type;
            document.getElementById('ee-date').value   = date;
            document.getElementById('ee-notes').value  = notes;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };
        window.closeExpenseEdit = function () { modal.style.display = 'none'; document.body.style.overflow = ''; };
        modal.addEventListener('click', e => { if (e.target === modal) closeExpenseEdit(); });
    })();
    </script>
@endsection
