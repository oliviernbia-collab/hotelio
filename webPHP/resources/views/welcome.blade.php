@extends('layouts.app')

@section('title', __('Accueil'))

@section('content')

{{-- ══════════════ HERO ══════════════ --}}
<section style="background:linear-gradient(135deg,#1A3328 0%,#0D1F18 50%,#1A1008 100%);padding:90px 28px 72px;position:relative;overflow:hidden;">
    {{-- Pattern --}}
    <div style="position:absolute;inset:0;opacity:.035;background-image:repeating-linear-gradient(45deg,#C9933A 0,#C9933A 1px,transparent 0,transparent 50%);background-size:18px 18px;"></div>
    <div style="position:absolute;top:-120px;right:-100px;width:560px;height:560px;background:radial-gradient(circle,rgba(201,147,58,.13) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="max-width:1280px;margin:0 auto;position:relative;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(201,147,58,.12);border:1px solid rgba(201,147,58,.28);padding:6px 16px;border-radius:100px;margin-bottom:28px;">
            <span style="font-size:12px;font-weight:600;color:#E4B05C;letter-spacing:.06em;text-transform:uppercase;">🌍 Côte d'Ivoire</span>
        </div>

        <h1 style="font-size:clamp(38px,5.5vw,70px);color:white;line-height:1.05;margin-bottom:20px;font-weight:600;">
            {{ __('Découvrez les plus') }}<br>{{ __('beaux hôtels') }}<br><em style="color:#E4B05C;">{{ __("d'Abidjan & au-delà") }}</em>
        </h1>
        <p style="font-size:17px;color:rgba(255,255,255,.62);max-width:520px;margin-bottom:44px;line-height:1.65;font-weight:300;">
            {{ __("Réservez en ligne en quelques clics. Les meilleures adresses de Côte d'Ivoire vous attendent.") }}
        </p>

        {{-- SEARCH BOX --}}
        <div style="background:white;border-radius:22px;padding:26px 28px;max-width:880px;box-shadow:0 12px 56px rgba(26,18,8,.18);">
            <form action="{{ route('hotels.index') }}" method="GET">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:16px;align-items:end;">
                    <div>
                        <label class="form-label">{{ __('Destination') }}</label>
                        <select name="city" class="form-control">
                            <option value="">{{ __('Toutes les villes') }}</option>
                            @foreach(\App\Models\Hotel::CITIES as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ __('Arrivée') }}</label>
                        <input type="date" name="checkin" class="form-control" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}">
                    </div>
                    <div>
                        <label class="form-label">{{ __('Départ') }}</label>
                        <input type="date" name="checkout" class="form-control" value="{{ now()->addDays(2)->toDateString() }}" min="{{ now()->addDays(1)->toDateString() }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="white-space:nowrap;">
                        🔍 {{ __('Rechercher') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div style="display:flex;gap:48px;margin-top:48px;flex-wrap:wrap;">
            <div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#E4B05C;">{{ $stats['hotels'] }}+</div>
                <div style="font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;">{{ __('Hôtels partenaires') }}</div>
            </div>
            <div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#E4B05C;">{{ $stats['cities'] }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;">{{ __('Villes couvertes') }}</div>
            </div>
            <div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#E4B05C;">{{ number_format($stats['bookings']) }}+</div>
                <div style="font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;">{{ __('Voyageurs satisfaits') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════ HÔTELS EN VEDETTE ══════════════ --}}
<div class="section">
    <div class="section-header">
        <div>
            <h2 class="section-title">{{ __('Hôtels en vedette') }}</h2>
            <p class="section-sub">{{ __('Nos adresses les mieux notées par nos voyageurs') }}</p>
        </div>
        {{-- <p>{{ $myHotels->count() }} établissement(s) enregistré(s)</p> --}}
        <a href="{{ route('hotels.index') }}" class="btn btn-outline">{{ __('Voir tous') }} →</a>
    </div>

    
</div>

<div class="section">

    <div class="hotels-grid">
        @foreach($featured as $hotel)
            @include('components.hotel-card', ['hotel' => $hotel])
        @endforeach
    

            {{-- <div class="section-header">
                <h2>Mes hôtels</h2> --}}
            {{-- </div> --}}

            {{-- <div class="hotels-grid"> --}}
                @forelse($myHotels as $hotel)
                    @include('components.hotel-card', ['hotel' => $hotel])
                @empty
                    <p>{{ __('Aucun hôtel pour le moment') }}</p>
                @endforelse
            {{-- </div> --}}
    </div>
    <div style="margin-top:20px;">
        <a href="{{ route('owner.hotels.create') }}" class="btn btn-primary">
            + {{ __('Ajouter un hôtel') }}
        </a>
    </div>
</div>

{{-- ══════════════ EXPLORER PAR VILLE ══════════════ --}}
<div style="background:white;">
    <div class="section">
        <div class="section-header">
            <div>
                <h2 class="section-title">{{ __('Explorer par ville') }}</h2>
                <p class="section-sub">{{ __("Des hôtels dans toute la Côte d'Ivoire") }}</p>
            </div>
        </div>

        @php
        $cityData = [
            'Abidjan'       => ['icon'=>'🌆','count'=>148],
            'Yamoussoukro'  => ['icon'=>'🕌','count'=>32],
            'Grand-Bassam'  => ['icon'=>'🏖️','count'=>24],
            'Assinie'       => ['icon'=>'🌴','count'=>18],
            'San-Pédro'     => ['icon'=>'⛵','count'=>21],
            'Bouaké'        => ['icon'=>'🏙️','count'=>45],
            'Man'           => ['icon'=>'⛰️','count'=>14],
            'Korhogo'       => ['icon'=>'🌾','count'=>11],
        ];
        @endphp

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;">
            @foreach($cityData as $name => $info)
                <a href="{{ route('hotels.city', $name) }}" style="background:var(--cream);border-radius:18px;padding:24px 16px;border:1.5px solid var(--border);display:flex;flex-direction:column;align-items:center;gap:10px;transition:all .22s;text-decoration:none;" onmouseover="this.style.borderColor='var(--gold)';this.style.background='var(--gold-pale)';this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--cream)';this.style.transform=''">
                    <div style="width:52px;height:52px;background:white;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;box-shadow:0 2px 12px rgba(0,0,0,.06);">{{ $info['icon'] }}</div>
                    <div style="font-size:15px;font-weight:600;color:var(--dark);font-family:'Cormorant Garamond',serif;">{{ $name }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $info['count'] }} {{ __('hôtels') }}</div>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ══════════════ CTA PROPRIÉTAIRE ══════════════ --}}
<div style="padding:0 28px 72px;max-width:1280px;margin:0 auto;">
    <div style="background:linear-gradient(135deg,#1A3328 0%,#120E08 100%);border-radius:24px;padding:60px 56px;display:flex;align-items:center;justify-content:space-between;gap:32px;position:relative;overflow:hidden;flex-wrap:wrap;">
        <div style="position:absolute;inset:0;opacity:.03;background-image:repeating-linear-gradient(45deg,#C9933A 0,#C9933A 1px,transparent 0,transparent 50%);background-size:20px 20px;"></div>
        <div style="position:relative;">
            <h2 style="font-size:36px;color:white;margin-bottom:10px;font-weight:600;">{{ __("Vous êtes propriétaire d'hôtel ?") }}</h2>
            <p style="font-size:15px;color:rgba(255,255,255,.55);font-weight:300;">{{ __("Rejoignez notre réseau et boostez vos réservations dès aujourd'hui.") }}</p>
        </div>
        <a href="{{ route('register') }}?role=owner" class="btn btn-primary btn-lg" style="position:relative;white-space:nowrap;">
            {{ __('Inscrire mon hôtel') }} →
        </a>
    </div>
</div>


@endsection
