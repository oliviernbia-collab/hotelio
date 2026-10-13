@extends('layouts.app')
@section('title', __('Réservation confirmée'))

@section('content')
<div style="max-width:700px;margin:0 auto;text-align:center">

  <div class="card" style="background:linear-gradient(135deg,rgba(0,154,68,.15),rgba(247,127,0,.08));border-color:rgba(0,154,68,.35);margin-bottom:1.5rem;padding:2.5rem">
    <div style="width:80px;height:80px;background:var(--ci-green);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem;color:white;box-shadow:0 0 0 16px rgba(0,154,68,.15)">
      <i class="fa-solid fa-check"></i>
    </div>
    <div style="font-size:1.8rem;font-weight:700;color:#a7f3d0;margin-bottom:.5rem">{{ __('Réservation Confirmée !') }}</div>
    <p style="color:#94a3b8;font-size:.95rem">{{ __('Votre séjour à') }} <strong style="color:white">{{ $reservation->hotel->name }}</strong> {{ __('est confirmé.') }}<br>{{ __('Un email de confirmation a été envoyé avec votre facture PDF.') }}</p>
    <div style="background:rgba(0,0,0,.2);border-radius:10px;padding:.75rem 1.5rem;display:inline-block;margin-top:1rem">
      <span style="font-size:.8rem;color:#94a3b8">{{ __('Code de réservation') }}</span>
      <div style="font-size:1.4rem;font-weight:700;color:var(--ci-green);font-family:monospace">{{ $reservation->code }}</div>
    </div>
  </div>

  <div class="card" style="margin-bottom:1.5rem;text-align:left">
    <div class="card-title"><span><i class="fa-solid fa-calendar-check" style="color:var(--ci-green)"></i> {{ __('Détails de votre séjour') }}</span></div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div style="background:rgba(148,163,184,.06);border-radius:10px;padding:1rem">
        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:.35rem">{{ __('Hôtel') }}</div>
        <div style="font-weight:700">{{ $reservation->hotel->name }}</div>
        <div style="font-size:.8rem;color:#94a3b8"><i class="fa-solid fa-location-dot"></i> {{ $reservation->hotel->city }}</div>
      </div>
      <div style="background:rgba(148,163,184,.06);border-radius:10px;padding:1rem">
        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:.35rem">{{ __('Chambre') }}</div>
        <div style="font-weight:700">N° {{ $reservation->room }}</div>
        <div style="font-size:.8rem;color:#94a3b8">{{ $reservation->nights }} {{ __('nuit(s)') }}</div>
      </div>
      <div style="background:rgba(148,163,184,.06);border-radius:10px;padding:1rem">
        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:.35rem">{{ __('Arrivée') }}</div>
        <div style="font-weight:700;font-size:1.1rem">{{ $reservation->checkin->format('d/m/Y') }}</div>
        <div style="font-size:.8rem;color:#94a3b8">{{ __('Check-in à partir de 14h00') }}</div>
      </div>
      <div style="background:rgba(148,163,184,.06);border-radius:10px;padding:1rem">
        <div style="font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:.35rem">{{ __('Départ') }}</div>
        <div style="font-weight:700;font-size:1.1rem">{{ $reservation->checkout->format('d/m/Y') }}</div>
        <div style="font-size:.8rem;color:#94a3b8">{{ __('Check-out avant 12h00') }}</div>
      </div>
    </div>

    <div style="background:rgba(0,154,68,.08);border:1px solid rgba(0,154,68,.25);border-radius:10px;padding:1rem;display:flex;justify-content:space-between;align-items:center">
      <div>
        <div style="font-size:.8rem;color:#94a3b8">{{ __('Montant payé') }}</div>
        <div style="font-size:1.5rem;font-weight:700;color:var(--ci-green)">{{ number_format($reservation->total, 0, ',', ' ') }} FCFA</div>
      </div>
      <div style="text-align:right">
        <div style="font-size:.8rem;color:#94a3b8">{{ __('Méthode') }}</div>
        <div style="font-weight:600">{{ $reservation->payment_method }}</div>
      </div>
    </div>
  </div>

  {{-- Localisation Google Maps --}}
  @php
    $hotel = $reservation->hotel;
    if ($hotel->latitude && $hotel->longitude) {
        $mapSrc   = 'https://maps.google.com/maps?q=' . $hotel->latitude . ',' . $hotel->longitude . '&output=embed&hl=fr&z=16';
        $mapsLink = 'https://www.google.com/maps?q=' . $hotel->latitude . ',' . $hotel->longitude;
    } else {
        $mapQuery = $hotel->name . ' ' . $hotel->quartier . ' ' . $hotel->city . ' Côte d\'Ivoire';
        $mapSrc   = 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed&hl=fr&z=15';
        $mapsLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapQuery);
    }
  @endphp
  <div class="card" style="margin-bottom:1.5rem;text-align:left">
    <div class="card-title" style="margin-bottom:1rem">
      <span><i class="fa-solid fa-location-dot" style="color:var(--ci-orange)"></i> {{ __("Localisation de l'hôtel") }}</span>
      <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer"
         class="btn-secondary" style="font-size:.8rem;padding:.4rem .8rem">
        <i class="fa-solid fa-up-right-from-square"></i> {{ __('Ouvrir dans Maps') }}
      </a>
    </div>
    <div style="border-radius:10px;overflow:hidden;border:1px solid var(--border)">
      <iframe src="{{ $mapSrc }}"
        width="100%" height="280" frameborder="0" style="border:0;display:block"
        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="{{ __('Localisation') }} {{ $reservation->hotel->name }}">
      </iframe>
    </div>
    <div style="margin-top:.75rem;display:flex;align-items:center;gap:.5rem;color:#94a3b8;font-size:.85rem">
      <i class="fa-solid fa-location-dot" style="color:var(--ci-orange)"></i>
      {{ $hotel->quartier }}, {{ $hotel->city }}
      @if($hotel->latitude && $hotel->longitude)
        <span style="margin-left:.5rem;font-family:monospace;font-size:.75rem;opacity:.6">
          {{ $hotel->latitude }}, {{ $hotel->longitude }}
        </span>
      @endif
    </div>
  </div>

  <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
    <a href="{{ route('reservations.invoice', $reservation) }}" class="btn" style="min-width:180px;justify-content:center">
      <i class="fa-solid fa-file-pdf"></i> {{ __('Télécharger la facture') }}
    </a>
    @if(auth()->user()->role === 'client')
      <a href="{{ route('client.index') }}" class="btn-secondary" style="min-width:180px;justify-content:center">
        <i class="fa-solid fa-house"></i> {{ __('Mon espace') }}
      </a>
    @else
      <a href="{{ route('reservations.index') }}" class="btn-secondary" style="min-width:180px;justify-content:center">
        <i class="fa-solid fa-list"></i> {{ __('Toutes les réservations') }}
      </a>
    @endif
    <a href="{{ route('hotels.public') }}" class="btn-secondary" style="min-width:180px;justify-content:center">
      <i class="fa-solid fa-magnifying-glass"></i> {{ __("Explorer d'autres hôtels") }}
    </a>
  </div>
</div>
@endsection
