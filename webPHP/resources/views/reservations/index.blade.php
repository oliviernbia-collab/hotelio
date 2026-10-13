@extends('layouts.app')

@section('content')

    {{-- En-tête --}}
    <div class="card hero" style="margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
            <div>
                <h2 style="margin:0 0 .4rem;font-size:1.4rem;">{{ __('Réservations') }}</h2>
                <p style="margin:0;color:#94a3b8;font-size:.9rem;">{{ __('Suivi des arrivées, séjours et départs — cycle check-in / check-out.') }}</p>
            </div>
            <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
                <span style="background:rgba(247,127,0,.15);border:1px solid rgba(247,127,0,.3);color:#fed7aa;border-radius:999px;padding:.4rem .9rem;font-size:.8rem;">
                    {{ $reservations->total() }} {{ __('réservation(s)') }}
                </span>
            </div>
        </div>
    </div>

    {{-- FILTRE PAR DATE --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <form method="GET" action="{{ url()->current() }}">
            <div style="display:flex;align-items:flex-end;gap:.75rem;flex-wrap:wrap;">
                <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                    <label style="font-size:.8rem;"><i class="fa-solid fa-calendar-day" style="color:#F77F00"></i> {{ __("Arrivée à partir du") }}</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                           style="padding:.6rem .9rem;font-size:.88rem;">
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:140px;">
                    <label style="font-size:.8rem;"><i class="fa-solid fa-calendar-check" style="color:#009A44"></i> {{ __("Arrivée jusqu'au") }}</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                           style="padding:.6rem .9rem;font-size:.88rem;">
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:160px;">
                    <label style="font-size:.8rem;"><i class="fa-solid fa-filter" style="color:#F77F00"></i> {{ __('Statut') }}</label>
                    <select name="statut" class="form-select" style="padding:.6rem .9rem;font-size:.88rem;">
                        <option value="">{{ __('Tous') }}</option>
                        <option value="confirmée"  {{ request('statut') === 'confirmée'  ? 'selected' : '' }}>{{ __('Confirmée') }}</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                        <option value="arrivée"    {{ request('statut') === 'arrivée'    ? 'selected' : '' }}>{{ __('Arrivée') }}</option>
                        <option value="départie"   {{ request('statut') === 'départie'   ? 'selected' : '' }}>{{ __('Départie') }}</option>
                        <option value="annulée"    {{ request('statut') === 'annulée'    ? 'selected' : '' }}>{{ __('Annulée') }}</option>
                    </select>
                </div>
                <button type="submit" class="btn" style="padding:.65rem 1.25rem;flex-shrink:0;">
                    <i class="fa-solid fa-magnifying-glass"></i> {{ __('Filtrer') }}
                </button>
                @if(request()->hasAny(['date_debut','date_fin','statut']))
                <a href="{{ url()->current() }}" class="btn-secondary" style="padding:.65rem 1.1rem;flex-shrink:0;">
                    <i class="fa-solid fa-xmark"></i> {{ __('Réinitialiser') }}
                </a>
                @endif
            </div>

            @if(request()->hasAny(['date_debut','date_fin','statut']))
            <div style="margin-top:.85rem;padding:.6rem 1rem;background:rgba(247,127,0,.1);border:1px solid rgba(247,127,0,.3);border-radius:10px;font-size:.82rem;color:#fed7aa;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                <i class="fa-solid fa-circle-info"></i>
                {{ __('Filtre actif') }} —
                @if(request('date_debut')) {{ __('Du') }} <strong>{{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}</strong> @endif
                @if(request('date_fin')) {{ __('au') }} <strong>{{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}</strong> @endif
                @if(request('statut')) · {{ __('Statut :') }} <strong>{{ ucfirst(str_replace('_',' ', request('statut'))) }}</strong> @endif
                · <strong>{{ $reservations->total() }}</strong> {{ __('résultat(s)') }}
            </div>
            @endif
        </form>
    </div>

    {{-- Légende des statuts chambre --}}
    <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1.25rem;font-size:.78rem;">
        @foreach(['disponible'=>['#009A44',__('Libre')],'réservée'=>['#F77F00',__('Réservée')],'occupée'=>['#3b82f6',__('Occupée')],'en_nettoyage'=>['#a855f7',__('En nettoyage')],'hors_service'=>['#ef4444',__('Hors service')]] as $k=>[$c,$l])
        <span style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(255,255,255,.05);border-radius:999px;padding:.3rem .75rem;border:1px solid rgba(255,255,255,.1);">
            <span style="width:8px;height:8px;border-radius:50%;background:{{ $c }};display:inline-block;"></span>{{ $l }}
        </span>
        @endforeach
    </div>

    <div class="card" style="overflow:auto;">
        <table class="table" style="min-width:900px;">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Hôtel · Chambre') }}</th>
                    <th>{{ __('Arrivée') }}</th>
                    <th>{{ __('Départ') }}</th>
                    <th>{{ __('Statut réservation') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                @php
                    $statusColors = [
                        'confirmée'  => '#009A44',
                        'en_attente' => '#F77F00',
                        'arrivée'    => '#3b82f6',
                        'départie'   => '#94a3b8',
                        'annulée'    => '#ef4444',
                    ];
                    $color = $statusColors[$res->status] ?? '#94a3b8';
                @endphp
                <tr>
                    <td style="font-family:monospace;font-size:.8rem;color:#94a3b8;">{{ $res->code }}</td>
                    <td>
                        <div style="font-weight:600;color:#e2e8f0;">{{ optional($res->client)->first_name }} {{ optional($res->client)->last_name }}</div>
                        <div style="font-size:.78rem;color:#64748b;">{{ optional($res->client)->nationality }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:#e2e8f0;">{{ optional($res->hotel)->short_name }}</div>
                        <div style="font-size:.78rem;color:#94a3b8;">{{ __('Chambre') }} {{ $res->room }} · {{ $res->nights }}n</div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:#e2e8f0;">{{ $res->checkin->format('d/m/Y') }}</div>
                        @if($res->checkin_at)
                        <div style="font-size:.75rem;color:#009A44;">✓ {{ $res->checkin_at->format('d/m H:i') }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600;color:#e2e8f0;">{{ $res->checkout->format('d/m/Y') }}</div>
                        @if($res->checkout_at)
                        <div style="font-size:.75rem;color:#94a3b8;">✓ {{ $res->checkout_at->format('d/m H:i') }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(255,255,255,.06);border-radius:999px;padding:.3rem .8rem;font-size:.78rem;border:1px solid {{ $color }}40;">
                            <span style="width:7px;height:7px;border-radius:50%;background:{{ $color }};display:inline-block;"></span>
                            {{ ucfirst(str_replace('_',' ', $res->status)) }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:#F77F00;">{{ number_format($res->total, 0, ',', ' ') }} FCFA</td>
                    <td>
                        <div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;">

                            @if(auth()->user()->role === 'prestataire')

                                {{-- Check-in (Réservée → Occupée) --}}
                                @if(in_array($res->status, ['confirmée','en_attente']) && !$res->checkin_at)
                                <form method="POST" action="{{ route('reservations.checkin', $res) }}" onsubmit="return confirm('{{ __('Confirmer le check-in ?') }}')">
                                    @csrf
                                    <button type="submit" style="background:#3b82f6;color:#fff;border:none;border-radius:999px;padding:.35rem .8rem;font-size:.78rem;font-weight:700;cursor:pointer;">
                                        ▶ Check-in
                                    </button>
                                </form>
                                @endif

                                {{-- Check-out (Occupée → En nettoyage) --}}
                                @if($res->checkin_at && !$res->checkout_at)
                                <form method="POST" action="{{ route('reservations.checkout', $res) }}" onsubmit="return confirm('{{ __('Confirmer le check-out ?') }}')">
                                    @csrf
                                    <button type="submit" style="background:#a855f7;color:#fff;border:none;border-radius:999px;padding:.35rem .8rem;font-size:.78rem;font-weight:700;cursor:pointer;">
                                        ■ Check-out
                                    </button>
                                </form>
                                @endif

                                {{-- Sélecteur statut manuel --}}
                                <form method="POST" action="{{ route('reservations.update-status', $res) }}">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-select" style="padding:.3rem .5rem;font-size:.78rem;border-radius:8px;min-width:110px;">
                                        <option value="confirmée"  {{ $res->status==='confirmée'  ? 'selected':'' }}>{{ __('Confirmée') }}</option>
                                        <option value="en_attente" {{ $res->status==='en_attente' ? 'selected':'' }}>{{ __('En attente') }}</option>
                                        <option value="arrivée"    {{ $res->status==='arrivée'    ? 'selected':'' }}>{{ __('Arrivée') }}</option>
                                        <option value="départie"   {{ $res->status==='départie'   ? 'selected':'' }}>{{ __('Départie') }}</option>
                                        <option value="annulée"    {{ $res->status==='annulée'    ? 'selected':'' }}>{{ __('Annulée') }}</option>
                                    </select>
                                </form>

                            @endif

                            {{-- Paiement (client) --}}
                            @if(!$res->paid && auth()->user()->role === 'client')
                            <a href="{{ route('reservations.payment', $res) }}" style="background:#F77F00;color:#fff;border-radius:999px;padding:.35rem .8rem;font-size:.78rem;font-weight:700;display:inline-block;">
                                {{ __('Payer') }}
                            </a>
                            @endif

                            {{-- Facture --}}
                            @if($res->paid)
                            <a href="{{ route('reservations.invoice', $res) }}" style="background:rgba(247,127,0,.15);color:#fed7aa;border:1px solid rgba(247,127,0,.3);border-radius:999px;padding:.35rem .8rem;font-size:.78rem;font-weight:700;display:inline-block;">
                                {{ __('Facture') }}
                            </a>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:#64748b;padding:2rem;">{{ __('Aucune réservation trouvée.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($reservations->total() > 0)
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-top:1.25rem;">

        {{-- Infos résultats --}}
        <span style="font-size:.82rem;color:#94a3b8;">
            {{ __('Affichage de') }} <strong style="color:#e2e8f0;">{{ $reservations->firstItem() }}</strong>
            à <strong style="color:#e2e8f0;">{{ $reservations->lastItem() }}</strong>
            {{ __('sur') }} <strong style="color:#e2e8f0;">{{ $reservations->total() }}</strong> {{ __('résultat(s)') }}
        </span>

        @if($reservations->hasPages())
        <div style="display:flex;align-items:center;gap:.35rem;">

            {{-- Précédent --}}
            @if($reservations->onFirstPage())
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .85rem;font-size:.82rem;border-radius:8px;border:1px solid rgba(255,255,255,.08);color:#475569;cursor:not-allowed;user-select:none;">
                ← {{ __('Précédent') }}
            </span>
            @else
            <a href="{{ $reservations->previousPageUrl() }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .85rem;font-size:.82rem;border-radius:8px;border:1px solid rgba(255,255,255,.12);color:#94a3b8;text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='rgba(247,127,0,.5)';this.style.color='#F77F00';" onmouseout="this.style.borderColor='rgba(255,255,255,.12)';this.style.color='#94a3b8';">
                ← {{ __('Précédent') }}
            </a>
            @endif

            {{-- Pages numérotées --}}
            @php
                $current  = $reservations->currentPage();
                $last     = $reservations->lastPage();
                $window   = 2;
                $pages    = collect(range(1, $last))
                    ->filter(fn($p) => $p === 1 || $p === $last || abs($p - $current) <= $window);
            @endphp

            @php $prev = null; @endphp
            @foreach($pages as $page)
                @if($prev !== null && $page - $prev > 1)
                <span style="color:#475569;padding:0 .2rem;font-size:.85rem;">…</span>
                @endif

                @if($page === $current)
                <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;font-size:.82rem;border-radius:8px;background:#F77F00;color:#fff;font-weight:700;border:1px solid #F77F00;">{{ $page }}</span>
                @else
                <a href="{{ $reservations->url($page) }}" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;font-size:.82rem;border-radius:8px;border:1px solid rgba(255,255,255,.12);color:#94a3b8;text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='rgba(247,127,0,.4)';this.style.color='#F77F00';" onmouseout="this.style.borderColor='rgba(255,255,255,.12)';this.style.color='#94a3b8';">{{ $page }}</a>
                @endif

                @php $prev = $page; @endphp
            @endforeach

            {{-- Suivant --}}
            @if($reservations->hasMorePages())
            <a href="{{ $reservations->nextPageUrl() }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .85rem;font-size:.82rem;border-radius:8px;border:1px solid rgba(255,255,255,.12);color:#94a3b8;text-decoration:none;transition:all .15s;" onmouseover="this.style.borderColor='rgba(247,127,0,.5)';this.style.color='#F77F00';" onmouseout="this.style.borderColor='rgba(255,255,255,.12)';this.style.color='#94a3b8';">
                {{ __('Suivant') }} →
            </a>
            @else
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .85rem;font-size:.82rem;border-radius:8px;border:1px solid rgba(255,255,255,.08);color:#475569;cursor:not-allowed;user-select:none;">
                {{ __('Suivant') }} →
            </span>
            @endif

        </div>
        @endif
    </div>
    @endif

@endsection
