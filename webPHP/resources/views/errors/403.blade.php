@extends('layouts.app')

@section('content')
    <div class="card" style="text-align: center; padding: 3rem;">
        <h1 style="font-size: 4rem; margin-bottom: 1rem;">403</h1>
        <h2>Accès non autorisé</h2>
        <p style="color: #94a3b8; margin: 2rem 0;">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
        <a href="{{ route('dashboard') }}" class="btn">Retour au tableau de bord</a>
    </div>
@endsection