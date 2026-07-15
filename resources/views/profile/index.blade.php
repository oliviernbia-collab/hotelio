@extends('layouts.app')

@section('content')
<div class="card" style="margin-bottom:1.5rem">
  <div style="display:flex;align-items:center;gap:1rem">
    <div style="width:56px;height:56px;border-radius:50%;background:var(--ci-orange);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:white;font-weight:700">
      {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <div>
      <div style="font-size:1.2rem;font-weight:700">{{ $user->name }}</div>
      <div style="font-size:.85rem;color:#94a3b8">{{ $user->email }}</div>
      <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'prestataire' ? 'warning' : 'success') }}" style="margin-top:4px">
        {{ ucfirst($user->role) }}
      </span>
    </div>
  </div>
</div>

@if(session('success'))
  <div style="background:rgba(0,154,68,.15);border:1px solid rgba(0,154,68,.3);border-radius:10px;padding:1rem;margin-bottom:1.25rem;color:#a7f3d0;display:flex;align-items:center;gap:.75rem">
    <i class="fa-solid fa-circle-check" style="color:var(--ci-green)"></i>
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div style="background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.3);border-radius:10px;padding:1rem;margin-bottom:1.25rem;color:#fca5a5">
    <i class="fa-solid fa-circle-exclamation" style="color:#e63946"></i>
    @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
  </div>
@endif

<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">

  <!-- Modifier le profil -->
  <div class="card">
    <div class="card-title" style="margin-bottom:1rem"><span><i class="fa-solid fa-user-pen" style="color:var(--ci-orange)"></i> {{ __('Informations personnelles') }}</span></div>
    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      <div class="form-group">
        <label>{{ __('Nom complet') }}</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
      </div>
      <div class="form-group">
        <label>{{ __('Adresse email') }}</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
      </div>
      <button type="submit" class="btn" style="width:100%;margin-top:.5rem">
        <i class="fa-solid fa-save"></i> {{ __('Sauvegarder les modifications') }}
      </button>
    </form>
  </div>

  <!-- Changer le mot de passe -->
  <div class="card">
    <div class="card-title" style="margin-bottom:1rem"><span><i class="fa-solid fa-lock" style="color:#3b82f6"></i> {{ __('Sécurité') }}</span></div>
    <form method="POST" action="{{ route('profile.password') }}">
      @csrf
      <div class="form-group">
        <label>{{ __('Mot de passe actuel') }}</label>
        <input type="password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="form-group">
        <label>{{ __('Nouveau mot de passe') }}</label>
        <input type="password" name="password" required autocomplete="new-password">
      </div>
      <div class="form-group">
        <label>{{ __('Confirmer le mot de passe') }}</label>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
      </div>
      <button type="submit" class="btn" style="width:100%;margin-top:.5rem;background:#3b82f6;border-color:#3b82f6">
        <i class="fa-solid fa-key"></i> {{ __('Changer le mot de passe') }}
      </button>
    </form>
  </div>
</div>

<!-- Infos complémentaires -->
<div class="grid" style="grid-template-columns:1fr 1fr;gap:1.25rem">
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-circle-info" style="color:var(--ci-green)"></i> {{ __('Détails du compte') }}</span></div>
    <div class="meta-list">
      <div class="meta-line"><span>{{ __('Identifiant') }}</span><strong>#{{ $user->id }}</strong></div>
      <div class="meta-line"><span>{{ __('Rôle') }}</span><strong>{{ ucfirst($user->role) }}</strong></div>
      <div class="meta-line"><span>{{ __('Statut') }}</span><strong>{{ $user->status ?? __('actif') }}</strong></div>
      <div class="meta-line"><span>{{ __('Membre depuis') }}</span><strong>{{ $user->created_at->format('d/m/Y') }}</strong></div>
    </div>
  </div>

  @if($user->role === 'prestataire')
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-hotel" style="color:var(--ci-orange)"></i> {{ __('Mes Hôtels') }}</span></div>
    @forelse($myHotels as $hotel)
      <div class="meta-line">
        <span>{{ $hotel->name }}</span>
        <a href="{{ route('hotels.show', $hotel) }}" class="btn-secondary">{{ __('Voir') }}</a>
      </div>
    @empty
      <p style="color:#94a3b8">{{ __('Aucun hôtel enregistré.') }}</p>
      <a href="{{ route('hotels.index') }}" class="btn" style="margin-top:.75rem">{{ __('Ajouter un hôtel') }}</a>
    @endforelse
  </div>
  @elseif($user->role === 'client' && $client)
  <div class="card">
    <div class="card-title"><span><i class="fa-solid fa-star" style="color:#f4a423"></i> {{ __('Programme Fidélité') }}</span></div>
    <div class="meta-list">
      <div class="meta-line"><span>{{ __('Total séjours') }}</span><strong>{{ $client->total_stays ?? 0 }}</strong></div>
      <div class="meta-line"><span>{{ __('Total dépensé') }}</span><strong>{{ number_format($client->total_spend ?? 0, 0, ',', ' ') }} FCFA</strong></div>
      <div class="meta-line"><span>{{ __('Statut') }}</span><strong>{{ $client->status ?? __('Actif') }}</strong></div>
      @if($client->last_visit)
      <div class="meta-line"><span>{{ __('Dernière visite') }}</span><strong>{{ $client->last_visit->format('d/m/Y') }}</strong></div>
      @endif
    </div>
  </div>
  @endif
</div>
@endsection
