@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-title">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:48px;height:48px;border-radius:14px;background:var(--ci-orange-dim);border:1px solid var(--ci-orange-border);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--ci-orange);">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);">{{ __("Journal d'activité") }}</div>
                <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __("Toutes les actions enregistrées dans l'application.") }}</p>
            </div>
        </div>
        {{-- Vider le journal --}}
        <form method="POST" action="{{ route('admin.journal.clear') }}"
              onsubmit="return confirm('{{ __('Vider tout le journal ?') }}')" style="display:flex;gap:.5rem;align-items:center;">
            @csrf @method('DELETE')
            <button type="submit" style="display:flex;align-items:center;gap:.4rem;padding:.55rem 1rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;cursor:pointer;font-size:.82rem;font-weight:600;transition:background .2s;"
                    onmouseover="this.style.background='rgba(230,57,70,.22)'"
                    onmouseout="this.style.background='rgba(230,57,70,.1)'">
                <i class="fa-solid fa-trash"></i> {{ __('Vider le journal') }}
            </button>
        </form>
    </div>
</div>

{{-- STATS --}}
<div class="grid grid-cols-4" style="gap:1rem;margin-bottom:1.5rem;">
    <div class="card" style="text-align:center;padding:1.25rem;">
        <div style="font-size:1.8rem;font-weight:800;color:var(--text-primary);">{{ number_format($total) }}</div>
        <div style="font-size:.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;letter-spacing:.06em;">{{ __('Total actions') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem;">
        <div style="font-size:1.8rem;font-weight:800;color:var(--ci-orange);">{{ $today }}</div>
        <div style="font-size:.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;letter-spacing:.06em;">{{ __("Aujourd'hui") }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem;">
        <div style="font-size:1.8rem;font-weight:800;color:var(--ci-green);">
            {{ \App\Models\ActivityLog::whereDate('created_at', today())->where('action','login')->count() }}
        </div>
        <div style="font-size:.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;letter-spacing:.06em;">{{ __('Connexions aujourd\'hui') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:1.25rem;">
        <div style="font-size:1.8rem;font-weight:800;color:#e63946;">
            {{ \App\Models\ActivityLog::whereDate('created_at', today())->where('action','deleted')->count() }}
        </div>
        <div style="font-size:.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;letter-spacing:.06em;">{{ __('Suppressions aujourd\'hui') }}</div>
    </div>
</div>

{{-- FILTRES --}}
<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('admin.journal') }}">
        <div style="display:flex;align-items:flex-end;gap:.75rem;flex-wrap:wrap;">
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-calendar" style="color:var(--ci-orange)"></i> {{ __('Date') }}</label>
                <input type="date" name="date" value="{{ request('date') }}" style="padding:.6rem .9rem;font-size:.88rem;">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-bolt" style="color:var(--ci-orange)"></i> {{ __('Action') }}</label>
                <select name="action" class="form-select" style="padding:.6rem .9rem;font-size:.88rem;">
                    <option value="">{{ __('Toutes les actions') }}</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ \App\Models\ActivityLog::actionLabel($action) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-cube" style="color:var(--ci-orange)"></i> {{ __('Module') }}</label>
                <select name="model" class="form-select" style="padding:.6rem .9rem;font-size:.88rem;">
                    <option value="">{{ __('Tous les modules') }}</option>
                    @foreach($models as $model)
                    <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>
                        {{ $model }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                <label style="font-size:.8rem;"><i class="fa-solid fa-user" style="color:var(--ci-orange)"></i> {{ __('Utilisateur') }}</label>
                <input type="text" name="user" value="{{ request('user') }}" placeholder="{{ __('Nom...') }}" style="padding:.6rem .9rem;font-size:.88rem;">
            </div>
            <button type="submit" class="btn" style="padding:.65rem 1.25rem;flex-shrink:0;">
                <i class="fa-solid fa-magnifying-glass"></i> {{ __('Filtrer') }}
            </button>
            @if(request()->hasAny(['date','action','model','user']))
            <a href="{{ route('admin.journal') }}" class="btn-secondary" style="padding:.65rem 1.1rem;flex-shrink:0;">
                <i class="fa-solid fa-xmark"></i> {{ __('Réinitialiser') }}
            </a>
            @endif
        </div>
    </form>
</div>

{{-- JOURNAL --}}
<div class="card">
    <div class="card-title" style="margin-bottom:1.25rem;">
        <span><i class="fa-solid fa-list-ul"></i> {{ __('Activités') }}</span>
        <span class="badge badge-secondary">{{ $logs->total() }} {{ __('entrée(s)') }}</span>
    </div>

    @php $currentDate = null; @endphp

    @forelse($logs as $log)

    {{-- SÉPARATEUR PAR JOUR --}}
    @if($currentDate !== $log->created_at->toDateString())
        @php $currentDate = $log->created_at->toDateString(); @endphp
        <div style="display:flex;align-items:center;gap:.75rem;margin:1.25rem 0 .75rem;">
            <div style="height:1px;flex:1;background:var(--border);"></div>
            <span style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);white-space:nowrap;padding:.25rem .75rem;background:var(--bg-input);border-radius:999px;border:1px solid var(--border);">
                <i class="fa-solid fa-calendar-day"></i>
                @if($log->created_at->isToday())
                    {{ __("Aujourd'hui") }} — {{ $log->created_at->format('d/m/Y') }}
                @elseif($log->created_at->isYesterday())
                    {{ __('Hier') }} — {{ $log->created_at->format('d/m/Y') }}
                @else
                    {{ $log->created_at->translatedFormat('l d F Y') }}
                @endif
            </span>
            <div style="height:1px;flex:1;background:var(--border);"></div>
        </div>
    @endif

    {{-- LIGNE DE LOG --}}
    <div style="display:flex;align-items:flex-start;gap:.85rem;padding:.75rem .5rem;border-bottom:1px solid var(--border);transition:background .15s;"
         onmouseover="this.style.background='var(--bg-input)'"
         onmouseout="this.style.background='transparent'">

        {{-- ICÔNE ACTION --}}
        <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.88rem;
                    background:{{ \App\Models\ActivityLog::actionColor($log->action) }}18;
                    color:{{ \App\Models\ActivityLog::actionColor($log->action) }};">
            <i class="fa-solid {{ \App\Models\ActivityLog::actionIcon($log->action) }}"></i>
        </div>

        {{-- CONTENU --}}
        <div style="flex:1;min-width:0;">
            <div style="font-size:.9rem;color:var(--text-primary);line-height:1.4;">
                {{ $log->description }}
            </div>
            <div style="display:flex;align-items:center;gap:.75rem;margin-top:.3rem;flex-wrap:wrap;">
                @if($log->user_name)
                <span style="font-size:.75rem;color:var(--text-secondary);display:flex;align-items:center;gap:.3rem;">
                    <i class="fa-solid fa-user" style="opacity:.6"></i>
                    {{ $log->user_name }}
                    @if($log->user_role)
                        <span style="font-size:.68rem;opacity:.7;">({{ $log->user_role }})</span>
                    @endif
                </span>
                @endif
                @if($log->model_type)
                <span style="font-size:.72rem;padding:.15rem .55rem;border-radius:999px;background:var(--bg-input);color:var(--text-muted);border:1px solid var(--border);">
                    {{ $log->model_type }}
                    @if($log->model_id) #{{ $log->model_id }} @endif
                </span>
                @endif
                @if($log->ip_address)
                <span style="font-size:.72rem;color:var(--text-muted);">
                    <i class="fa-solid fa-network-wired" style="opacity:.5"></i>
                    {{ $log->ip_address }}
                </span>
                @endif
            </div>
        </div>

        {{-- BADGE ACTION + HEURE --}}
        <div style="flex-shrink:0;text-align:right;">
            <span style="display:inline-block;padding:.2rem .65rem;border-radius:999px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
                         background:{{ \App\Models\ActivityLog::actionColor($log->action) }}18;
                         color:{{ \App\Models\ActivityLog::actionColor($log->action) }};">
                {{ \App\Models\ActivityLog::actionLabel($log->action) }}
            </span>
            <div style="font-size:.72rem;color:var(--text-muted);margin-top:.25rem;">
                {{ $log->created_at->format('H:i:s') }}
            </div>
        </div>
    </div>

    @empty
    <div style="text-align:center;padding:3rem;color:var(--text-muted);">
        <i class="fa-solid fa-clock-rotate-left" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
        <p>{{ __('Aucune activité enregistrée.') }}</p>
    </div>
    @endforelse

    {{-- PAGINATION --}}
    @if($logs->hasPages())
    <div style="margin-top:1.25rem;">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
