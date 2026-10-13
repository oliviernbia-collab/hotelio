<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Connexion') }} | Hôtelio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--ci-orange:#F77F00;--ci-green:#009A44;--ocean:#0C1F3D}
body{
  font-family:'DM Sans',sans-serif;
  min-height:100vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  background:#020617;
  color:#e2e8f0;
  overflow-x:hidden;
  overflow-y:auto;
}

/* ── Background slider ── */
.bg-slider{position:fixed;inset:0;z-index:0}
.bg-slide{
  position:absolute;inset:0;
  background-size:cover;background-position:center;
  opacity:0;transition:opacity 1.5s ease-in-out;
  filter:brightness(.65) saturate(1.1);
}
.bg-slide.active{opacity:1}

/* ── Overlay ── */
.bg-overlay{position:fixed;inset:0;background:linear-gradient(135deg,rgba(12,31,61,.6),rgba(2,6,23,.7));z-index:1}

/* ════════════════════════════════════════════
   SCROLLBAR HORIZONTAL — bande défilante
   ════════════════════════════════════════════ */
.features-ticker{
  position:fixed;top:0;left:0;right:0;z-index:50;
  background:linear-gradient(90deg,rgba(12,31,61,.98),rgba(10,24,50,.98));
  border-bottom:1px solid rgba(247,127,0,.25);
  height:38px;overflow:hidden;
  display:flex;align-items:center;
}
.ticker-track{
  display:flex;gap:0;
  animation:tickerScroll 28s linear infinite;
  white-space:nowrap;
}
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
.ticker-item .ticker-badge{
  background:var(--ci-orange);color:white;
  font-size:.65rem;font-weight:700;
  padding:1px 6px;border-radius:10px;
  text-transform:uppercase;letter-spacing:.05em;
}
@keyframes tickerScroll{
  0%  {transform:translateX(0)}
  100%{transform:translateX(-50%)}
}

/* ── Scrollbar CSS ── */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(247,127,0,.4);border-radius:10px}
::-webkit-scrollbar-thumb:hover{background:var(--ci-orange)}

/* ── Card ── */
.auth-wrap{
  position:relative;z-index:2;
  width:min(100%,480px);padding:20px;
  margin-top:58px;margin-bottom:20px;
}
.auth-card{
  background:rgba(10,18,36,.95);
  border:1px solid rgba(247,127,0,.25);
  border-radius:24px;padding:2.5rem;
  box-shadow:0 40px 100px rgba(0,0,0,.5);
  backdrop-filter:blur(20px);
}

/* ── Logo ── */
.logo{display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:2rem;text-decoration:none}
.logo-icon{width:44px;height:44px;background:var(--ci-orange);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;color:white}
.logo-text{font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:400;color:white;letter-spacing:-.02em}
.logo-text span{color:var(--ci-orange)}

h2{text-align:center;font-size:1.5rem;font-weight:600;margin-bottom:.4rem}
.subtitle{text-align:center;color:#64748b;font-size:.9rem;margin-bottom:2rem}

/* ── Comptes rapides ── */
.quick-section-label{
  font-size:.78rem;font-weight:600;color:#64748b;
  text-transform:uppercase;letter-spacing:.1em;
  margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;
}
.quick-section-label i{color:var(--ci-orange)}

.quick-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;margin-bottom:1.5rem}
.quick-card{
  border:1.5px solid rgba(148,163,184,.15);border-radius:12px;padding:.75rem .6rem;
  cursor:pointer;text-align:center;transition:all .2s;
  background:rgba(148,163,184,.04);
}
.quick-card:hover{border-color:rgba(247,127,0,.4);background:rgba(247,127,0,.06);transform:translateY(-2px)}
.quick-card.active-role{border-color:var(--ci-orange);background:rgba(247,127,0,.1)}
.quick-icon{font-size:1.2rem;margin-bottom:.3rem}
.quick-icon i{color:var(--ci-orange)}
.quick-label{font-size:.72rem;font-weight:600;margin-bottom:.15rem}
.quick-email{font-size:.65rem;color:#475569;font-family:monospace}

/* ── Divider ── */
.form-divider{
  display:flex;align-items:center;gap:.75rem;
  margin:1.1rem 0;font-size:.75rem;color:#334155;
}
.form-divider::before,.form-divider::after{content:'';flex:1;height:1px;background:rgba(148,163,184,.12)}

/* ── Form ── */
.form-group{margin-bottom:1rem;position:relative}
.form-group label{
  display:flex;align-items:center;gap:.4rem;
  font-size:.82rem;font-weight:500;color:#94a3b8;margin-bottom:.4rem;
}
.form-group label i{color:var(--ci-orange);font-size:.75rem;width:14px;text-align:center}
.input-wrap{position:relative}
.form-group input{
  width:100%;padding:.85rem 1rem;border-radius:12px;
  background:rgba(148,163,184,.07);border:1.5px solid rgba(148,163,184,.18);
  color:#e2e8f0;font-family:'DM Sans',sans-serif;font-size:.92rem;
  transition:border-color .2s,box-shadow .2s;outline:none;
}
.form-group input:focus{border-color:var(--ci-orange);box-shadow:0 0 0 3px rgba(247,127,0,.12)}
.toggle-pw{
  position:absolute;right:14px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#475569;cursor:pointer;font-size:.9rem;padding:0;
  transition:color .2s;
}
.toggle-pw:hover{color:var(--ci-orange)}

/* ── Remember + Forgot ── */
.remember-row{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:.5rem;font-size:.82rem;
}
.remember-row label{display:flex;align-items:center;gap:.5rem;color:#94a3b8;cursor:pointer}
.remember-row input[type=checkbox]{accent-color:var(--ci-orange);width:15px;height:15px}
.forgot-link{color:#475569;text-decoration:none;font-size:.8rem;transition:color .2s}
.forgot-link:hover{color:var(--ci-orange)}

/* ── Button ── */
.btn-submit{
  width:100%;padding:1rem;border:none;border-radius:12px;
  background:var(--ci-orange);color:white;font-size:1rem;font-weight:700;
  cursor:pointer;transition:all .25s;margin-top:.75rem;
  font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
}
.btn-submit:hover{background:#e06e00;transform:translateY(-1px);box-shadow:0 8px 24px rgba(247,127,0,.4)}
.btn-submit:active{transform:translateY(0)}

/* ── Error ── */
.error-box{
  background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.3);
  border-radius:10px;padding:.9rem 1rem;margin-bottom:1rem;
  color:#fca5a5;font-size:.83rem;display:flex;gap:.6rem;align-items:flex-start;
}
.error-box i{color:#e63946;margin-top:2px;flex-shrink:0}

.success-box{
  background:rgba(0,154,68,.12);border:1px solid rgba(0,154,68,.3);
  border-radius:10px;padding:.9rem 1rem;margin-bottom:1rem;
  color:#86efac;font-size:.83rem;display:flex;gap:.6rem;align-items:flex-start;
}
.success-box i{color:var(--ci-green);margin-top:2px;flex-shrink:0}

/* ── Trust badges ── */
.trust-row{
  display:flex;justify-content:center;gap:1.25rem;flex-wrap:wrap;
  margin-top:1.25rem;padding-top:1.25rem;
  border-top:1px solid rgba(148,163,184,.1);
}
.trust-item{display:flex;align-items:center;gap:.35rem;font-size:.72rem;color:#475569}

/* ── Footer ── */
.auth-footer{text-align:center;margin-top:1.25rem;color:#475569;font-size:.84rem;line-height:2}
.auth-footer a{color:var(--ci-orange);text-decoration:none;font-weight:600}
.auth-footer a:hover{color:#e06e00;text-decoration:underline}

/* ── Flag stripe ── */
.ci-stripe{height:3px;background:linear-gradient(to right,var(--ci-orange) 33%,white 33% 66%,var(--ci-green) 66%);border-radius:2px 2px 0 0}

/* ── Responsive ── */
@media(max-width:520px){
  .auth-card{padding:1.75rem 1.25rem}
  .quick-grid{grid-template-columns:1fr 1fr 1fr}
  .trust-row{gap:.75rem}
}
</style>
</head>
<body>

<!-- ── Backgrounds — mêmes images que register ── -->
<div class="bg-slider">
  <div class="bg-slide active" style="background-image:url('{{ asset('images/h10.jpg') }}')"></div>
  <div class="bg-slide"        style="background-image:url('{{ asset('images/h13.jpg') }}')"></div>
  <div class="bg-slide"        style="background-image:url('{{ asset('images/h14.jpg') }}')"></div>
  <div class="bg-slide"        style="background-image:url('{{ asset('images/h15.jpg') }}')"></div>
</div>
<div class="bg-overlay"></div>

<!-- ════════════════════════════════════════════
     SCROLLBAR HORIZONTAL — bande défilante
     ════════════════════════════════════════════ -->
<div class="features-ticker" role="marquee" aria-label="Fonctionnalités de la plateforme">
  <div class="ticker-track">
    <!-- Lot 1 -->
    @php $items = [
      ['icon'=>'fa-hotel',               'text'=> __('1 200+ hôtels disponibles')],
      ['icon'=>'fa-mobile-screen-button','text'=> 'Orange Money &amp; MTN Mobile Money'],
      ['icon'=>'fa-shield-halved',       'text'=> __('Paiement 100 % sécurisé')],
      ['icon'=>'fa-rotate-left',         'text'=> __('Annulation flexible'), 'badge'=> __('Gratuit')],
      ['icon'=>'fa-headset',             'text'=> __('Support 24 h / 7 j')],
      ['icon'=>'fa-location-dot',        'text'=> 'Abidjan · Yamoussoukro · Bouaké · San-Pédro'],
      ['icon'=>'fa-star',                'text'=> __('Note moyenne 4,9 / 5')],
      ['icon'=>'fa-file-invoice',        'text'=> __('Facture PDF instantanée')],
      ['icon'=>'fa-tag',                 'text'=> __('Meilleur prix garanti')],
      ['icon'=>'fa-certificate',         'text'=> __('Hôtels certifiés Hôtelio')],
      ['icon'=>'fa-brands fa-whatsapp',  'text'=> __('Confirmation WhatsApp'), 'color'=>'#25d366'],
      ['icon'=>'fa-percent',             'text'=> __('Programmes fidélité'), 'badge'=> __('New')],
    ]; @endphp
    @foreach(array_merge($items, $items) as $item)
    <span class="ticker-item">
      <i class="{{ str_starts_with($item['icon'],'fa-brands') ? $item['icon'] : 'fa-solid '.$item['icon'] }}"
         {{ isset($item['color']) ? 'style=color:'.$item['color'] : '' }}></i>
      {!! $item['text'] !!}
      @if(isset($item['badge'])) <span class="ticker-badge">{{ $item['badge'] }}</span>@endif
    </span>
    @endforeach
  </div>
</div>

<!-- ── Formulaire ── -->
<div class="auth-wrap">
  <div class="ci-stripe"></div>
  <div class="auth-card">

    <!-- Logo -->
    <a href="{{ route('premiere') }}" class="logo">
      <span class="logo-icon"><i class="fa-solid fa-hotel"></i></span>
      <span class="logo-text">
        Hôte<span>Lio</span>
      </span>
    </a>

    <h2>{{ __('Bienvenue') }} <i class="fa-solid fa-hand-sparkles" style="color:var(--ci-orange);"></i></h2>
    <p class="subtitle">{{ __('Connectez-vous pour accéder à votre espace Hôtelio') }}</p>

    <!-- ── Succès ── -->
    @if(session('success'))
    <div class="success-box">
      <i class="fa-solid fa-circle-check"></i>
      <div>{{ session('success') }}</div>
    </div>
    @endif

    <!-- ── Erreurs ── -->
    @if($errors->any())
    <div class="error-box">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
    </div>
    @endif

    <!-- ── Formulaire de connexion ── -->
    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      <div class="form-group">
        <label for="email">
          <i class="fa-solid fa-envelope"></i> {{ __('Adresse email') }}
        </label>
        <div class="input-wrap">
          <input id="email" type="email" name="email"
                 value="{{ old('email') }}" required autofocus
                 placeholder="votre@email.com">
        </div>
      </div>

      <div class="form-group">
        <label for="password">
          <i class="fa-solid fa-lock"></i> {{ __('Mot de passe') }}
        </label>
        <div class="input-wrap">
          <input id="password" type="password" name="password"
                 required placeholder="••••••••">
          <button type="button" class="toggle-pw" onclick="togglePassword()" title="{{ __('Afficher/masquer') }}">
            <i class="fa-regular fa-eye" id="pw-eye"></i>
          </button>
        </div>
      </div>

      <div class="remember-row">
        <label>
          <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
          {{ __('Se souvenir de moi') }}
        </label>
        <a href="{{ route('password.request') }}" class="forgot-link">
          <i class="fa-solid fa-circle-question"></i> {{ __('Mot de passe oublié ?') }}
        </a>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-right-to-bracket"></i>
        {{ __('Se connecter') }}
      </button>
    </form>

    <!-- ── Badges de confiance ── -->
    <div class="trust-row">
      <span class="trust-item"><i class="fa-solid fa-shield-halved" style="color:var(--ci-green)"></i> {{ __('Sécurisé SSL') }}</span>
      <span class="trust-item"><i class="fa-solid fa-lock" style="color:var(--ci-orange)"></i> {{ __('Données privées') }}</span>
      <span class="trust-item"><i class="fa-regular fa-credit-card" style="color:#3b82f6"></i> {{ __('Paiement protégé') }}</span>
      <span class="trust-item"><i class="fa-solid fa-headset" style="color:#a855f7"></i> {{ __('Support 24/7') }}</span>
    </div>

    <div class="auth-footer">
      {{ __('Pas encore de compte ?') }}
      <a href="{{ route('register') }}">
        <i class="fa-solid fa-user-plus"></i> {{ __("S'inscrire gratuitement") }}
      </a>
      &nbsp;·&nbsp;
      <a href="{{ route('premiere') }}">
        <i class="fa-solid fa-house"></i> {{ __('Accueil') }}
      </a>
    </div>

  </div>
</div>

<script>
/* ── Remplissage rapide des champs ── */
function fillLogin(email, pwd) {
  document.getElementById('email').value    = email;
  document.getElementById('password').value = pwd;
  document.querySelectorAll('.quick-card').forEach(c => c.classList.remove('active-role'));
  event.currentTarget.classList.add('active-role');
}

/* ── Afficher / masquer le mot de passe ── */
function togglePassword() {
  const input = document.getElementById('password');
  const icon  = document.getElementById('pw-eye');
  const show  = input.type === 'password';
  input.type  = show ? 'text' : 'password';
  icon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
}

/* ── Background slideshow ── */
const slides = document.querySelectorAll('.bg-slide');
let cur = 0;
setInterval(() => {
  slides[cur].classList.remove('active');
  cur = (cur + 1) % slides.length;
  slides[cur].classList.add('active');
}, 4500);
</script>
</body>
</html>
