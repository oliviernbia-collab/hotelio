<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Inscription') }} | Hôtelio</title>
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
.bg-slide{position:absolute;inset:0;background-size:cover;background-position:center;opacity:0;transition:opacity 1.5s ease-in-out;filter:brightness(.65) saturate(1.1)}
.bg-slide.active{opacity:1}

/* ── Overlay ── */
.bg-overlay{position:fixed;inset:0;background:linear-gradient(135deg,rgba(12,31,61,.58),rgba(2,6,23,.68));z-index:1}

/* ══════════════════════════════════════
   SCROLLBAR HORIZONTAL — bande de fonctionnalités
   ══════════════════════════════════════ */
.features-ticker{
  position:fixed;
  top:0;left:0;right:0;
  z-index:50;
  background:linear-gradient(90deg,rgba(12,31,61,.98),rgba(10,24,50,.98));
  border-bottom:1px solid rgba(247,127,0,.25);
  height:38px;
  overflow:hidden;
  display:flex;
  align-items:center;
}
.ticker-track{
  display:flex;
  gap:0;
  animation:tickerScroll 28s linear infinite;
  white-space:nowrap;
}
.ticker-track:hover{animation-play-state:paused}
.ticker-item{
  display:inline-flex;
  align-items:center;
  gap:.5rem;
  padding:0 28px;
  font-size:.75rem;
  font-weight:500;
  color:rgba(255,255,255,.75);
  border-right:1px solid rgba(247,127,0,.15);
  height:38px;
  flex-shrink:0;
  transition:color .2s;
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

/* ── Scrollbar personnalisée (verticale) ── */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(247,127,0,.4);border-radius:10px}
::-webkit-scrollbar-thumb:hover{background:var(--ci-orange)}

/* ── Card ── */
.auth-wrap{
  position:relative;z-index:2;
  width:min(100%,560px);
  padding:20px;
  margin-top:58px; /* espace pour le ticker */
  margin-bottom:20px;
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
.subtitle{text-align:center;color:#64748b;font-size:.9rem;margin-bottom:1.75rem}

/* ── Role selector ── */
.role-section-label{
  font-size:.78rem;font-weight:600;color:#64748b;
  text-transform:uppercase;letter-spacing:.1em;
  margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;
}
.role-section-label i{color:var(--ci-orange)}

.role-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem}
.role-card{
  border:2px solid rgba(148,163,184,.15);border-radius:14px;padding:1.1rem 1rem;
  cursor:pointer;text-align:center;transition:all .22s;
  background:rgba(148,163,184,.04);
}
.role-card:hover{border-color:rgba(247,127,0,.4);background:rgba(247,127,0,.06);transform:translateY(-2px)}
.role-card.selected{border-color:var(--ci-orange);background:rgba(247,127,0,.12);transform:translateY(-2px)}
.role-card input{display:none}

.role-icon-wrap{
  width:52px;height:52px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  margin:0 auto .75rem;font-size:1.4rem;
  transition:all .22s;
}
.role-card:nth-child(1) .role-icon-wrap{background:rgba(247,127,0,.12);color:var(--ci-orange)}
.role-card:nth-child(2) .role-icon-wrap{background:rgba(0,154,68,.12);color:var(--ci-green)}
.role-card.selected:nth-child(1) .role-icon-wrap{background:rgba(247,127,0,.22)}
.role-card.selected:nth-child(2) .role-icon-wrap{background:rgba(0,154,68,.22)}

.role-label{font-weight:700;font-size:.92rem;margin-bottom:.2rem}
.role-sub{font-size:.72rem;color:#64748b;line-height:1.4}
.role-card.selected .role-sub{color:#94a3b8}

.role-check{
  width:18px;height:18px;border-radius:50%;border:2px solid rgba(148,163,184,.3);
  margin:8px auto 0;display:flex;align-items:center;justify-content:center;
  font-size:.6rem;color:transparent;transition:all .2s;
}
.role-card.selected .role-check{
  background:var(--ci-orange);border-color:var(--ci-orange);color:white;
}

/* ── Plan selector ── */
.plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;margin-bottom:1rem}
.plan-card{
  border:2px solid rgba(148,163,184,.15);border-radius:14px;padding:.85rem .6rem;
  cursor:pointer;text-align:center;transition:all .22s;
  background:rgba(148,163,184,.04);
}
.plan-card:hover{border-color:rgba(247,127,0,.4);background:rgba(247,127,0,.06)}
.plan-card.selected{border-color:var(--ci-orange);background:rgba(247,127,0,.12)}
.plan-card input{display:none}
.plan-name{font-weight:700;font-size:.85rem;margin-bottom:.25rem}
.plan-price{font-size:.95rem;font-weight:800;color:var(--ci-orange);margin-bottom:.15rem}
.plan-price small{font-size:.6rem;font-weight:500;color:#64748b}
.plan-check{
  width:16px;height:16px;border-radius:50%;border:2px solid rgba(148,163,184,.3);
  margin:6px auto 0;display:flex;align-items:center;justify-content:center;
  font-size:.55rem;color:transparent;transition:all .2s;
}
.plan-card.selected .plan-check{background:var(--ci-orange);border-color:var(--ci-orange);color:white}

/* ── Form ── */
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
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}

/* ── Divider ── */
.form-divider{
  display:flex;align-items:center;gap:.75rem;
  margin:1.1rem 0;
  font-size:.75rem;color:#334155;
}
.form-divider::before,.form-divider::after{content:'';flex:1;height:1px;background:rgba(148,163,184,.12)}

/* ── Button ── */
.btn-submit{
  width:100%;padding:1rem;border:none;border-radius:12px;
  background:var(--ci-orange);color:white;font-size:1rem;font-weight:700;
  cursor:pointer;transition:all .25s;margin-top:.5rem;
  font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
}
.btn-submit:hover{background:#e06e00;transform:translateY(-1px);box-shadow:0 8px 24px rgba(247,127,0,.4)}
.btn-submit i{font-size:.95rem}

/* ── Error ── */
.error-box{
  background:rgba(230,57,70,.12);border:1px solid rgba(230,57,70,.3);
  border-radius:10px;padding:.9rem 1rem;margin-bottom:1rem;
  color:#fca5a5;font-size:.83rem;display:flex;gap:.6rem;align-items:flex-start;
}
.error-box i{color:#e63946;margin-top:2px;flex-shrink:0}

/* ── Footer ── */
.auth-footer{text-align:center;margin-top:1.5rem;color:#475569;font-size:.84rem;line-height:2}
.auth-footer a{color:var(--ci-orange);text-decoration:none;font-weight:600}
.auth-footer a:hover{color:#e06e00;text-decoration:underline}

/* ── Flag stripe ── */
.ci-stripe{height:3px;background:linear-gradient(to right,var(--ci-orange) 33%,white 33% 66%,var(--ci-green) 66%);border-radius:2px 2px 0 0}

/* ── Trust badges ── */
.trust-row{
  display:flex;justify-content:center;gap:1.5rem;
  margin-top:1.25rem;padding-top:1.25rem;
  border-top:1px solid rgba(148,163,184,.1);
}
.trust-item{
  display:flex;align-items:center;gap:.4rem;
  font-size:.72rem;color:#475569;
}
.trust-item i{font-size:.8rem}

/* ── Responsive ── */
@media(max-width:520px){
  .form-row{grid-template-columns:1fr}
  .auth-card{padding:1.75rem 1.25rem}
  .trust-row{gap:.75rem;flex-wrap:wrap;justify-content:center}
}
</style>
</head>
<body>

<!-- ── Backgrounds — mêmes images que login ── -->
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
    <span class="ticker-item">
      <i class="fa-solid fa-hotel"></i> 1 200+ hôtels disponibles
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-mobile-screen-button"></i> Orange Money &amp; MTN Mobile Money
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-shield-halved"></i> Paiement 100 % sécurisé
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-rotate-left"></i> Remboursement jusqu'à 95%
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-headset"></i> Support 24 h / 7 j
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-location-dot"></i> Abidjan · Yamoussoukro · Bouaké · San-Pédro
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-star"></i> Note moyenne 4,9 / 5
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-file-invoice"></i> Facture PDF instantanée
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-tag"></i> Meilleur prix garanti
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-certificate"></i> Hôtels certifiés Hôtelio
    </span>
    <span class="ticker-item">
      <i class="fa-brands fa-whatsapp" style="color:#25d366"></i> Confirmation WhatsApp
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-percent"></i> Programmes fidélité <span class="ticker-badge">New</span>
    </span>

    <!-- Lot 2 — copie exacte pour boucle seamless -->
    <span class="ticker-item">
      <i class="fa-solid fa-hotel"></i> 1 200+ hôtels disponibles
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-mobile-screen-button"></i> Orange Money &amp; MTN Mobile Money
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-shield-halved"></i> Paiement 100 % sécurisé
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-rotate-left"></i> Remboursement jusqu'à 95%
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-headset"></i> Support 24 h / 7 j
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-location-dot"></i> Abidjan · Yamoussoukro · Bouaké · San-Pédro
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-star"></i> Note moyenne 4,9 / 5
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-file-invoice"></i> Facture PDF instantanée
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-tag"></i> Meilleur prix garanti
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-certificate"></i> Hôtels certifiés Hôtelio
    </span>
    <span class="ticker-item">
      <i class="fa-brands fa-whatsapp" style="color:#25d366"></i> Confirmation WhatsApp
    </span>
    <span class="ticker-item">
      <i class="fa-solid fa-percent"></i> Programmes fidélité <span class="ticker-badge">New</span>
    </span>

  </div>
</div>

<!-- ── Formulaire ── -->
<div class="auth-wrap">
  <div class="ci-stripe"></div>
  <div class="auth-card">

    <a href="{{ route('premiere') }}" class="logo">
      <span class="logo-icon"><i class="fa-solid fa-hotel"></i></span>
      <span class="logo-text">
        Hôte<span>Lio</span>
      </span>
    </a>

    <h2>{{ __('Créer un compte') }}</h2>
    <p class="subtitle">{{ __('Rejoignez la plateforme N°1 des hôtels en Côte d\'Ivoire 🇨🇮') }}</p>

    @if($errors->any())
    <div class="error-box">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
    </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
      @csrf

      <!-- ── Choix du rôle ── -->
      <div class="role-section-label">
        <i class="fa-solid fa-circle-question"></i> {{ __("Je m'inscris en tant que…") }}
      </div>

      <div class="role-grid">

        <!-- Voyageur / Client -->
        <label class="role-card {{ old('role', request('role', 'client')) === 'client' ? 'selected' : '' }}"
               onclick="selectRole('client')">
          <input type="radio" name="role" value="client"
                 {{ old('role', request('role', 'client')) === 'client' ? 'checked' : '' }} required>
          <div class="role-icon-wrap">
            <i class="fa-solid fa-suitcase-rolling"></i>
          </div>
          <div class="role-label">{{ __('Voyageur') }}</div>
          <div class="role-sub">{{ __('Je souhaite réserver des hôtels et des séjours') }}</div>
          <div class="role-check"><i class="fa-solid fa-check"></i></div>
        </label>

        <!-- Hôtelier / Prestataire -->
        <label class="role-card {{ old('role', request('role', 'client')) === 'prestataire' ? 'selected' : '' }}"
               onclick="selectRole('prestataire')">
          <input type="radio" name="role" value="prestataire"
                 {{ old('role', request('role', 'client')) === 'prestataire' ? 'checked' : '' }}>
          <div class="role-icon-wrap">
            <i class="fa-solid fa-building-user"></i>
          </div>
          <div class="role-label">{{ __('Hôtelier') }}</div>
          <div class="role-sub">{{ __('Je gère des hôtels et des propriétés touristiques') }}</div>
          <div class="role-check"><i class="fa-solid fa-check"></i></div>
        </label>

      </div>

      <div id="proofSection" class="form-group" style="display:none;">
        <label for="proof_document">
          <i class="fa-solid fa-file-shield"></i> {{ __('Justificatif de propriété (établissement, registre de commerce...)') }}
        </label>
        <input id="proof_document" type="file" name="proof_document" accept=".pdf,.jpg,.jpeg,.png">
        <p style="font-size:.72rem;color:var(--text-muted,#94a3b8);margin-top:.35rem;">
          <i class="fa-solid fa-circle-info"></i> {{ __('PDF, JPG ou PNG — 5 Mo max. Requis pour les comptes hôteliers, votre compte sera validé par un administrateur sous 24–48 h.') }}
        </p>
      </div>

      <div id="planSection" style="display:none;">
        <div class="role-section-label">
          <i class="fa-solid fa-layer-group"></i> {{ __('Palier d\'abonnement') }}
        </div>
        <div class="plan-grid">
          @foreach(($plans ?? []) as $plan)
          <label class="plan-card {{ old('plan', request('plan')) === $plan->slug ? 'selected' : '' }}"
                 onclick="selectPlan('{{ $plan->slug }}')">
            <input type="radio" name="plan" value="{{ $plan->slug }}"
                   {{ old('plan', request('plan')) === $plan->slug ? 'checked' : '' }}>
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-price">{{ number_format($plan->price_monthly, 0, ',', ' ') }} <small>FCFA/mois</small></div>
            <div class="plan-check"><i class="fa-solid fa-check"></i></div>
          </label>
          @endforeach
        </div>
      </div>

      <div class="form-divider">
        <i class="fa-solid fa-user-pen" style="color:var(--ci-orange)"></i>
        {{ __('Vos informations') }}
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="name">
            <i class="fa-solid fa-user"></i> {{ __('Nom complet') }}
          </label>
          <input id="name" type="text" name="name"
                 value="{{ old('name') }}" required autofocus
                 placeholder="Jean Kouassi">
        </div>
        <div class="form-group">
          <label for="email">
            <i class="fa-solid fa-envelope"></i> {{ __('Adresse email') }}
          </label>
          <input id="email" type="email" name="email"
                 value="{{ old('email') }}" required
                 placeholder="jean@example.com">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="password">
            <i class="fa-solid fa-lock"></i> {{ __('Mot de passe') }}
          </label>
          <input id="password" type="password" name="password"
                 required placeholder="8 {{ __('caractères min.') }}">
        </div>
        <div class="form-group">
          <label for="password_confirmation">
            <i class="fa-solid fa-lock-open"></i> {{ __('Confirmer') }}
          </label>
          <input id="password_confirmation" type="password"
                 name="password_confirmation" required
                 placeholder="{{ __('Confirmer le mot de passe') }}">
        </div>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-user-plus"></i>
        {{ __('Créer mon compte gratuitement') }}
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
      {{ __('Déjà inscrit ?') }}
      <a href="{{ route('login') }}">
        <i class="fa-solid fa-right-to-bracket"></i> {{ __('Se connecter') }}
      </a>
      &nbsp;·&nbsp;
      <a href="{{ route('premiere') }}">
        <i class="fa-solid fa-house"></i> {{ __('Accueil') }}
      </a>
    </div>

  </div>
</div>

<!-- ── Backgrounds (images) ── -->
<script>
function selectRole(role) {
  document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
  const card = document.querySelector(`input[value="${role}"]`).closest('.role-card');
  card.classList.add('selected');
  document.querySelector(`input[value="${role}"]`).checked = true;

  const proofSection  = document.getElementById('proofSection');
  const proofInput    = document.getElementById('proof_document');
  const planSection   = document.getElementById('planSection');
  const isPrestataire = role === 'prestataire';
  proofSection.style.display = isPrestataire ? 'block' : 'none';
  proofInput.required = isPrestataire;
  if (!isPrestataire) proofInput.value = '';
  planSection.style.display = isPrestataire ? 'block' : 'none';
}
function selectPlan(plan) {
  document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
  const card = document.querySelector(`input[name="plan"][value="${plan}"]`).closest('.plan-card');
  card.classList.add('selected');
  document.querySelector(`input[name="plan"][value="${plan}"]`).checked = true;
}
document.addEventListener('DOMContentLoaded', () => {
  selectRole(document.querySelector('input[name="role"]:checked')?.value || 'client');
});

// Background slideshow
const slides = document.querySelectorAll('.bg-slide');
let cur = 0;
setInterval(() => {
  slides[cur].classList.remove('active');
  cur = (cur + 1) % slides.length;
  slides[cur].classList.add('active');
}, 4500);
</script>
@include('partials.cookie-banner')
</body>
</html>
