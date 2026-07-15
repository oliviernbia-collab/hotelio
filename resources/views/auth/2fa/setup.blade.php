@extends('layouts.app')

@section('content')
<div style="max-width:500px;margin:2rem auto;">
    <div class="card" style="padding:2rem;">
        <h2 style="margin:0 0 .4rem;font-size:1.2rem;color:#f1f5f9;">
            <i class="fa-solid fa-shield-halved" style="color:#F77F00;"></i> {{ __("Activer l'authentification 2FA") }}
        </h2>
        <p style="margin:0 0 1.5rem;font-size:.85rem;color:#64748b;">{{ __('Scannez ce QR code avec Google Authenticator, Authy ou toute application TOTP compatible.') }}</p>

        {{-- QR Code --}}
        <div style="background:#fff;border-radius:14px;padding:1.25rem;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code 2FA" style="width:180px;height:180px;">
        </div>

        <div style="background:rgba(247,127,0,.08);border:1px solid rgba(247,127,0,.2);border-radius:10px;padding:.85rem 1rem;margin-bottom:1.5rem;font-size:.82rem;color:#fed7aa;">
            <i class="fa-solid fa-circle-info" style="margin-right:.4rem;"></i>
            {{ __("Après avoir scanné, entrez le code à 6 chiffres affiché dans votre application pour confirmer l'activation.") }}
        </div>

        @if($errors->any())
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:.8rem 1rem;margin-bottom:1rem;color:#fca5a5;font-size:.85rem;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('2fa.enable') }}">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.4rem;">{{ __('Code de confirmation') }}</label>
                <input type="text" name="otp" maxlength="6" inputmode="numeric" autofocus
                    placeholder="000000"
                    style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:.85rem 1rem;color:#f1f5f9;font-size:1.5rem;font-family:monospace;text-align:center;letter-spacing:.3em;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#F77F00'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>
            <button type="submit" style="width:100%;padding:.9rem;border-radius:12px;border:none;background:#F77F00;color:#fff;font-size:.95rem;font-weight:700;cursor:pointer;">
                <i class="fa-solid fa-lock"></i> {{ __('Activer la 2FA') }}
            </button>
        </form>

        <a href="{{ route('profile.index') }}" style="display:block;text-align:center;margin-top:1rem;font-size:.82rem;color:#475569;text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Annuler') }}
        </a>
    </div>
</div>
@endsection
