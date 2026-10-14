<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Mot de passe oublié') }} | Hôtelio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--ci-orange:#F77F00;--ci-green:#009A44;--ocean:#0C1F3D}
body{
  font-family:'DM Sans',sans-serif;
  min-height:100vh;
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  background:#020617;color:#e2e8f0;
  overflow-x:hidden;overflow-y:auto;
}

.bg-slider{position:fixed;inset:0;z-index:0}
.bg-slide{
  position:absolute;inset:0;
  background-size:cover;background-position:center;
  opacity:0;transition:opacity 1.5s ease-in-out;
  filter:brightness(.55) saturate(1.1);
}
.bg-slide.active{opacity:1}
.bg-overlay{position:fixed;inset:0;background:linear-gradient(135deg,rgba(12,31,61,.65),rgba(2,6,23,.75));z-index:1}

.features-ticker{
  position:fixed;top:0;left:0;right:0;z-index:50;
  background:linear-gradient(90deg,rgba(12,31,61,.98),rgba(10,24,50,.98));
  border-bottom:1px solid rgba(247,127,0,.25);
  height:38px;overflow:hidden;display:flex;align-items:center;
}
.ticker-track{display:flex;gap:0;animation:tickerScroll 28s linear infinite;white-space:nowrap}
.ticker-track:hover{animation-play-state:paused}
.ticker-item{
  display:inline-flex;align-items:center;gap:.5rem;
  padding:0 28px;font-size:.75rem;font-weight:500;
  color:rgba(255,255,255,.75);
  border-right:1px solid rgba(247,127,0,.15);
  height:38px;flex-shrink:0;transition:color .2s;
}
.ticker-item:hover{color:white}
.ticker-item i{color:var(--ci-orange);font-size:.8rem}
@keyframes tickerScroll{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(247,127,0,.4);border-radius:10px}
::-webkit-scrollbar-thumb:hover{background:var(--ci-orange)}

.auth-wrap{
  position:relative;z-index:2;
  width:min(100%,460px);padding:20px;
  margin-top:58px;margin-bottom:20px;
}
.auth-card{
  background:rgba(10,18,36,.95);
  border:1px solid rgba(247,127,0,.25);
  border-radius:24px;padding:2.5rem;
  box-shadow:0 40px 100px rgba(0,0,0,.5);
  backdrop-filter:blur(20px);
}

.logo{display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:2rem;text-decoration:none}
.logo-icon{width:44px;height:44px;background:var(--ci-orange);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;color:white}
.logo-text{font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:400;color:white;letter-spacing:-.02em}
.logo-text span{color:var(--ci-orange)}

.page-icon{
  width:72px;height:72px;border-radius:50%;
  background:linear-gradient(135deg,rgba(247,127,0,.15),rgba(247,127,0,.05));
  border:2px solid rgba(247,127,0,.3);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.5rem;font-size:1.8rem;color:var(--ci-orange);
}
h2{text-align:center;font-size:1.4rem;font-weight:600;margin-bottom:.4rem}
.subtitle{text-align:center;color:#64748b;font-size:.88rem;margin-bottom:2rem;line-height:1.6}

.form-group{margin-bottom:1rem}
.form-group label{
  display:flex;align-items:center;gap:.4rem;
  font-size:.82rem;font-weight:500;color:#94a3b8;margin-bottom:.4rem;
}
.form-group label i{color:var(--ci-orange);font-size:.75rem;width:14px;text-align:center}
.form-group input{
  width:100%;padding:.85rem 1rem;border-radius:12px;
  background:rgba(148,163,184,.07);border:1.5px solid rgba(148,163,184,.18);
  color:#e2e8f0;font-family:'DM Sans',sans-serif;font-size:.92rem;
  transition:border-color .2s,box-shadow .2s;outline:none;
}
.form-group input:focus{border-color:var(--ci-orange);box-shadow:0 0 0 3px rgba(247,127,0,.12)}
.form-group input::placeholder{color:#334155}

.btn-submit{
  width:100%;padding:1rem;border:none;border-radius:12px;
  background:var(--ci-orange);color:white;font-size:1rem;font-weight:700;
  cursor:pointer;transition:all .25s;margin-top:.5rem;
  font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
}
.btn-submit:hover{background:#e06e00;transform:translateY(-1px);box-shadow:0 8px 24px rgba(247,127,0,.4)}
.btn-submit:active{transform:translateY(0)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}

.error-box{
  background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.3);
  border-radius:10px;padding:.9rem 1rem;margin-bottom:1rem;
  color:#fca5a5;font-size:.83rem;display:flex;gap:.6rem;align-items:flex-start;
}
.error-box i{color:#e63946;margin-top:2px;flex-shrink:0}

.success-box{
  background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.3);
  border-radius:10px;padding:1.1rem 1rem;margin-bottom:1rem;
  color:#86efac;font-size:.85rem;display:flex;gap:.6rem;align-items:flex-start;
}
.success-box i{color:var(--ci-green);margin-top:3px;flex-shrink:0}
.success-box div{line-height:1.6}

.divider-hint{
  text-align:center;font-size:.8rem;color:#334155;
  margin:1.25rem 0 .5rem;
  display:flex;align-items:center;gap:.75rem;
}
.divider-hint::before,.divider-hint::after{content:'';flex:1;height:1px;background:rgba(148,163,184,.1)}

.auth-footer{text-align:center;margin-top:1.25rem;color:#475569;font-size:.84rem;line-height:2}
.auth-footer a{color:var(--ci-orange);text-decoration:none;font-weight:600}
.auth-footer a:hover{color:#e06e00;text-decoration:underline}

.ci-stripe{height:3px;background:linear-gradient(to right,var(--ci-orange) 33%,white 33% 66%,var(--ci-green) 66%);border-radius:2px 2px 0 0}

@media(max-width:520px){.auth-card{padding:1.75rem 1.25rem}}
</style>
</head>
<body>

<div class="bg-slider">
  <div class="bg-slide active" style="background-image:url('{{ asset('images/h10.jpg') }}')"></div>
  <div class="bg-slide"        style="background-image:url('{{ asset('images/h13.jpg') }}')"></div>
  <div class="bg-slide"        style="background-image:url('{{ asset('images/h14.jpg') }}')"></div>
  <div class="bg-slide"        style="background-image:url('{{ asset('images/h15.jpg') }}')"></div>
</div>
<div class="bg-overlay"></div>

<div class="features-ticker" role="marquee" aria-label="Fonctionnalités de la plateforme">
  <div class="ticker-track">
    @php $items = [
      ['icon'=>'fa-hotel',               'text'=> '1 200+ hôtels disponibles'],
      ['icon'=>'fa-mobile-screen-button','text'=> 'Orange Money &amp; MTN Mobile Money'],
      ['icon'=>'fa-shield-halved',       'text'=> 'Paiement 100 % sécurisé'],
      ['icon'=>'fa-rotate-left',         'text'=> 'Remboursement jusqu\'à 95%'],
      ['icon'=>'fa-headset',             'text'=> 'Support 24 h / 7 j'],
      ['icon'=>'fa-lock',                'text'=> 'Récupération sécurisée'],
      ['icon'=>'fa-star',                'text'=> 'Note moyenne 4,9 / 5'],
      ['icon'=>'fa-tag',                 'text'=> 'Meilleur prix garanti'],
    ]; @endphp
    @foreach(array_merge($items, $items) as $item)
    <span class="ticker-item">
      <i class="{{ str_starts_with($item['icon'],'fa-brands') ? $item['icon'] : 'fa-solid '.$item['icon'] }}"></i>
      {!! $item['text'] !!}
    </span>
    @endforeach
  </div>
</div>

<div class="auth-wrap">
  <div class="ci-stripe"></div>
  <div class="auth-card">

    <a href="{{ route('premiere') }}" class="logo">
      <span class="logo-icon"><i class="fa-solid fa-hotel"></i></span>
      <span class="logo-text">
        Hôte<span>Lio</span>
      </span>
    </a>

    <div class="page-icon">
      <i class="fa-solid fa-key"></i>
    </div>

    <h2>{{ __('Mot de passe oublié ?') }}</h2>
    <p class="subtitle">
      {{ __('Renseignez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.') }}
    </p>

    @if(session('status'))
    <div class="success-box">
      <i class="fa-solid fa-circle-check"></i>
      <div>
        <strong>{{ __('Lien envoyé !') }}</strong><br>
        {{ __('Un e-mail de réinitialisation a été envoyé à votre adresse. Vérifiez également votre dossier spam.') }}
      </div>
    </div>
    @endif

    @if($errors->any())
    <div class="error-box">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
    </div>
    @endif

    @if(!session('status'))
    <form method="POST" action="{{ route('password.email') }}" id="forgot-form">
      @csrf
      <div class="form-group">
        <label for="email">
          <i class="fa-solid fa-envelope"></i> {{ __('Adresse e-mail') }}
        </label>
        <input id="email" type="email" name="email"
               value="{{ old('email') }}" required autofocus
               placeholder="votre@email.com">
      </div>

      <button type="submit" class="btn-submit" id="submit-btn">
        <i class="fa-solid fa-paper-plane"></i>
        <span id="btn-text">{{ __('Envoyer le lien de réinitialisation') }}</span>
      </button>
    </form>
    @endif

    <div class="divider-hint">ou</div>

    <div class="auth-footer">
      <a href="{{ route('login') }}">
        <i class="fa-solid fa-arrow-left"></i> {{ __('Retour à la connexion') }}
      </a>
      &nbsp;·&nbsp;
      <a href="{{ route('register') }}">
        <i class="fa-solid fa-user-plus"></i> {{ __("S'inscrire") }}
      </a>
    </div>

  </div>
</div>

<script>
const slides = document.querySelectorAll('.bg-slide');
let cur = 0;
setInterval(() => {
  slides[cur].classList.remove('active');
  cur = (cur + 1) % slides.length;
  slides[cur].classList.add('active');
}, 4500);

const form = document.getElementById('forgot-form');
if (form) {
  form.addEventListener('submit', () => {
    const btn  = document.getElementById('submit-btn');
    const text = document.getElementById('btn-text');
    btn.disabled = true;
    text.textContent = 'Envoi en cours…';
  });
}
</script>
</body>
</html>
