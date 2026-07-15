@extends('layouts.app')

@section('content')
<div class="card" style="margin-bottom:1.5rem">
  <div style="display:flex;align-items:center;gap:1rem">
    <div style="width:48px;height:48px;background:var(--ci-orange);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:white">
      <i class="fa-solid fa-gear"></i>
    </div>
    <div>
      <div style="font-size:1.2rem;font-weight:700">{{ __('Paramètres du système') }}</div>
      <p style="margin:.25rem 0 0;color:#94a3b8">{{ __('Configuration globale de la plateforme Hôtelio.') }}</p>
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">

  <!-- Infos plateforme -->
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-circle-info" style="color:var(--ci-orange)"></i> {{ __('Informations plateforme') }}</span></div>
    <div class="meta-list">
      <div class="meta-line"><span>{{ __('Nom de la plateforme') }}</span><strong>Hôtelio</strong></div>
      <div class="meta-line"><span>{{ __('Version Laravel') }}</span><strong>{{ app()->version() }}</strong></div>
      <div class="meta-line"><span>{{ __('PHP Version') }}</span><strong>{{ phpversion() }}</strong></div>
      <div class="meta-line"><span>{{ __('Environnement') }}</span>
        <span class="badge badge-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
          {{ ucfirst(app()->environment()) }}
        </span>
      </div>
      <div class="meta-line"><span>{{ __('Debug') }}</span>
        <span class="badge badge-{{ config('app.debug') ? 'danger' : 'success' }}">
          {{ config('app.debug') ? __('Activé') : __('Désactivé') }}
        </span>
      </div>
    </div>
  </div>

  <!-- Préférences -->
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-sliders" style="color:var(--ci-green)"></i> {{ __('Préférences globales') }}</span></div>
    <div class="meta-list">
      <div class="meta-line"><span>{{ __('Langue par défaut') }}</span><strong>{{ __('Français (fr)') }}</strong></div>
      <div class="meta-line"><span>{{ __('Fuseau horaire') }}</span><strong>Africa/Abidjan (GMT+0)</strong></div>
      <div class="meta-line"><span>{{ __('Devise') }}</span><strong>FCFA (XOF)</strong></div>
      <div class="meta-line"><span>{{ __('Pays') }}</span><strong>Côte d'Ivoire 🇨🇮</strong></div>
      <div class="meta-line"><span>{{ __('Format de date') }}</span><strong>dd/mm/yyyy</strong></div>
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">

  <!-- Base de données -->
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-database" style="color:#3b82f6"></i> {{ __('Base de données') }}</span></div>
    <div class="meta-list">
      <div class="meta-line"><span>{{ __('Connexion') }}</span><strong>{{ config('database.default') }}</strong></div>
      <div class="meta-line">
        <span>{{ __('Hôtels') }}</span>
        <strong>{{ \App\Models\Hotel::count() }} {{ __('enregistrements') }}</strong>
      </div>
      <div class="meta-line">
        <span>{{ __('Réservations') }}</span>
        <strong>{{ \App\Models\Reservation::count() }} {{ __('enregistrements') }}</strong>
      </div>
      <div class="meta-line">
        <span>{{ __('Utilisateurs') }}</span>
        <strong>{{ \App\Models\User::count() }} {{ __('enregistrements') }}</strong>
      </div>
      <div class="meta-line">
        <span>{{ __('Avis clients') }}</span>
        <strong>{{ \App\Models\Review::count() }} {{ __('enregistrements') }}</strong>
      </div>
    </div>
  </div>

  <!-- Email -->
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-envelope" style="color:#a855f7"></i> {{ __('Configuration Email') }}</span></div>
    <div class="meta-list">
      <div class="meta-line"><span>{{ __('Mailer') }}</span><strong>{{ config('mail.default') }}</strong></div>
      <div class="meta-line"><span>{{ __('From Address') }}</span><strong>{{ config('mail.from.address') }}</strong></div>
      <div class="meta-line"><span>{{ __('From Name') }}</span><strong>{{ config('mail.from.name') }}</strong></div>
      <div class="meta-line">
        <span>{{ __('Statut') }}</span>
        <span class="badge badge-{{ config('mail.default') === 'log' ? 'warning' : 'success' }}">
          {{ config('mail.default') === 'log' ? __('Mode log (dev)') : __('Configuré') }}
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Sécurité -->
<div class="card" style="margin-bottom:1.25rem">
  <div class="card-title"><span><i class="fa-solid fa-shield-halved" style="color:var(--ci-green)"></i> {{ __('Sécurité') }}</span></div>
  <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:1rem">
    <div style="background:rgba(0,154,68,.08);border:1px solid rgba(0,154,68,.2);border-radius:12px;padding:1rem;text-align:center">
      <div style="font-size:1.6rem;margin-bottom:.5rem">🔒</div>
      <div style="font-weight:700;font-size:.9rem">{{ __('Chiffrement HTTPS') }}</div>
      <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">{{ __('Recommandé en production') }}</div>
    </div>
    <div style="background:rgba(0,154,68,.08);border:1px solid rgba(0,154,68,.2);border-radius:12px;padding:1rem;text-align:center">
      <div style="font-size:1.6rem;margin-bottom:.5rem">🛡️</div>
      <div style="font-weight:700;font-size:.9rem">{{ __('CSRF Protection') }}</div>
      <div style="font-size:.75rem;color:#a7f3d0;margin-top:.25rem">{{ __('Activée sur tous les formulaires') }}</div>
    </div>
    <div style="background:rgba(0,154,68,.08);border:1px solid rgba(0,154,68,.2);border-radius:12px;padding:1rem;text-align:center">
      <div style="font-size:1.6rem;margin-bottom:.5rem">🔑</div>
      <div style="font-weight:700;font-size:.9rem">{{ __('Hachage Bcrypt') }}</div>
      <div style="font-size:.75rem;color:#a7f3d0;margin-top:.25rem">{{ __('Mots de passe chiffrés') }}</div>
    </div>
  </div>
</div>

<!-- Statistiques système -->
<div class="card">
  <div class="card-title"><span><i class="fa-solid fa-chart-line" style="color:var(--ci-orange)"></i> {{ __('Statistiques en temps réel') }}</span></div>
  <div class="grid" style="grid-template-columns:repeat(4,1fr);gap:1rem">
    @php
      $stats = [
        ['label' => __('Hôtels actifs'), 'value' => \App\Models\Hotel::where('status','Ouvert')->count(), 'icon' => 'fa-hotel', 'color' => 'var(--ci-orange)'],
        ['label' => __('Réservations confirmées'), 'value' => \App\Models\Reservation::where('status','confirmée')->count(), 'icon' => 'fa-calendar-check', 'color' => 'var(--ci-green)'],
        ['label' => __('Chambres disponibles'), 'value' => \App\Models\Room::where('status','disponible')->count(), 'icon' => 'fa-bed', 'color' => '#3b82f6'],
        ['label' => __('Clients VIP'), 'value' => \App\Models\Client::where('status','VIP')->count(), 'icon' => 'fa-crown', 'color' => '#a855f7'],
      ];
    @endphp
    @foreach($stats as $s)
    <div style="text-align:center;padding:1.25rem;background:rgba(148,163,184,.06);border-radius:12px;border:1px solid rgba(148,163,184,.12)">
      <i class="fa-solid {{ $s['icon'] }}" style="font-size:1.8rem;color:{{ $s['color'] }};margin-bottom:.5rem"></i>
      <div style="font-size:1.8rem;font-weight:700">{{ $s['value'] }}</div>
      <div style="font-size:.75rem;color:#94a3b8">{{ $s['label'] }}</div>
    </div>
    @endforeach
  </div>
</div>
@endsection
