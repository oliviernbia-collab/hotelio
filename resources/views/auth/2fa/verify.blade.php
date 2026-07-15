@extends('layouts.app')

@section('content')
<div style="max-width:400px;margin:4rem auto;">
    <div class="card" style="padding:2rem;">
        <div style="text-align:center;margin-bottom:1.75rem;">
            <div style="width:64px;height:64px;border-radius:50%;background:rgba(247,127,0,.15);border:2px solid rgba(247,127,0,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.6rem;">
                🔐
            </div>
            <h2 style="margin:0 0 .4rem;font-size:1.2rem;color:#f1f5f9;">{{ __('Vérification en 2 étapes') }}</h2>
            <p style="margin:0;font-size:.85rem;color:#64748b;">{{ __("Saisissez le code à 6 chiffres généré par votre application d'authentification.") }}</p>
        </div>

        @if($errors->any())
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:.8rem 1rem;margin-bottom:1rem;color:#fca5a5;font-size:.85rem;">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify.post') }}">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:.75rem;text-transform:uppercase;letter-spacing:.07em;color:#64748b;font-weight:600;margin-bottom:.4rem;">{{ __('Code OTP') }}</label>
                <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autofocus autocomplete="one-time-code"
                    placeholder="000000"
                    style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:.85rem 1rem;color:#f1f5f9;font-size:1.5rem;font-family:monospace;text-align:center;letter-spacing:.3em;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#F77F00'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>
            <button type="submit" style="width:100%;padding:.9rem;border-radius:12px;border:none;background:#F77F00;color:#fff;font-size:.95rem;font-weight:700;cursor:pointer;">
                <i class="fa-solid fa-shield-check"></i> {{ __('Vérifier') }}
            </button>
        </form>

        <div style="margin-top:1.25rem;text-align:center;font-size:.78rem;color:#475569;">
            <i class="fa-brands fa-google"></i> {{ __('Utilisez Google Authenticator, Authy ou une app compatible TOTP.') }}
        </div>
    </div>
</div>
@endsection
