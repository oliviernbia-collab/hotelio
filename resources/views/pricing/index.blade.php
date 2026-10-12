<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Tarifs') }} | Hôtelio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--ci-orange:#F77F00;--ci-green:#009A44;--ocean:#0C1F3D}
body{
  font-family:'DM Sans',sans-serif;
  min-height:100vh;
  background:#020617;
  color:#e2e8f0;
}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.logo-icon{width:40px;height:40px;background:var(--ci-orange);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;color:white}
.logo-text{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:400;color:white;letter-spacing:-.02em}
.logo-text span{color:var(--ci-orange)}

.topbar{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 2rem;max-width:1200px;margin:0 auto}
.topbar-links{display:flex;gap:1.5rem;align-items:center}
.topbar-links a{color:#94a3b8;text-decoration:none;font-size:.9rem;font-weight:500;transition:color .2s}
.topbar-links a:hover{color:white}
.topbar-links a.btn-login{background:var(--ci-orange);color:white;padding:.55rem 1.3rem;border-radius:10px}

.hero{text-align:center;padding:3rem 1.5rem 2rem;max-width:720px;margin:0 auto}
.hero h1{font-family:'Cormorant Garamond',serif;font-size:2.6rem;font-weight:600;color:white;margin-bottom:1rem}
.hero h1 span{color:var(--ci-orange)}
.hero p{color:#94a3b8;font-size:1.05rem;line-height:1.6}

.pricing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;max-width:1100px;margin:2.5rem auto;padding:0 1.5rem}
@media (max-width:860px){.pricing-grid{grid-template-columns:1fr;max-width:420px}}

.plan-card{
  background:rgba(10,18,36,.95);
  border:1px solid rgba(247,127,0,.2);
  border-radius:20px;
  padding:2rem 1.75rem;
  display:flex;flex-direction:column;
  transition:transform .25s,border-color .25s;
}
.plan-card:hover{transform:translateY(-6px);border-color:rgba(247,127,0,.5)}
.plan-card.featured{border-color:var(--ci-orange);background:linear-gradient(160deg,rgba(247,127,0,.08),rgba(10,18,36,.95))}

.plan-badge{align-self:flex-start;background:var(--ci-orange);color:white;font-size:.7rem;font-weight:700;padding:.25rem .7rem;border-radius:999px;margin-bottom:1rem;text-transform:uppercase;letter-spacing:.05em}
.plan-name{font-size:1.3rem;font-weight:700;color:white;margin-bottom:.3rem}
.plan-sub{color:#64748b;font-size:.85rem;margin-bottom:1.25rem}
.plan-price{display:flex;align-items:baseline;gap:.3rem;margin-bottom:1.5rem}
.plan-price .amount{font-size:2.3rem;font-weight:800;color:var(--ci-orange)}
.plan-price .unit{color:#64748b;font-size:.85rem}

.plan-features{list-style:none;margin-bottom:1.75rem;flex:1}
.plan-features li{display:flex;align-items:flex-start;gap:.6rem;font-size:.88rem;color:#cbd5e1;margin-bottom:.75rem;line-height:1.4}
.plan-features li i{color:var(--ci-green);margin-top:.2rem;flex-shrink:0}

.plan-cta{display:block;text-align:center;padding:.85rem;border-radius:12px;background:rgba(247,127,0,.12);border:1px solid rgba(247,127,0,.35);color:var(--ci-orange);text-decoration:none;font-weight:700;font-size:.92rem;transition:background .2s}
.plan-cta:hover{background:var(--ci-orange);color:white}
.plan-card.featured .plan-cta{background:var(--ci-orange);color:white}

.commission-note{max-width:720px;margin:0 auto 3rem;text-align:center;padding:1.25rem 1.5rem;background:rgba(0,154,68,.08);border:1px solid rgba(0,154,68,.25);border-radius:16px;color:#a7f3d0;font-size:.88rem;line-height:1.6}

.footer{text-align:center;padding:2rem;color:#475569;font-size:.8rem}
.footer a{color:var(--ci-orange);text-decoration:none}
</style>
</head>
<body>

<div class="topbar">
  <a href="{{ route('premiere') }}" class="logo">
    <span class="logo-icon"><i class="fa-solid fa-hotel"></i></span>
    <span class="logo-text">Hôte<span>Lio</span></span>
  </a>
  <div class="topbar-links">
    <a href="{{ route('premiere') }}">{{ __('Accueil') }}</a>
    <a href="{{ route('login') }}">{{ __('Connexion') }}</a>
    <a href="{{ route('register') }}" class="btn-login">{{ __('Devenir partenaire') }}</a>
  </div>
</div>

<div class="hero">
  <h1>{{ __('Des tarifs simples pour') }} <span>{{ __('votre hôtel') }}</span></h1>
  <p>{{ __('Un abonnement mensuel pour gérer votre établissement, complété par une commission uniquement sur les réservations que nous vous apportons.') }}</p>
</div>

<div class="pricing-grid">
  @foreach($plans as $plan)
  @php
    $isFeatured = $plan->slug === 'business';
    $subLabels = ['starter' => __('Petites auberges et petits hôtels'), 'business' => __('Hôtels de taille moyenne'), 'premium' => __('Établissements avec besoins avancés')];
  @endphp
  <div class="plan-card {{ $isFeatured ? 'featured' : '' }}">
    @if($isFeatured)
    <span class="plan-badge">{{ __('Le plus populaire') }}</span>
    @endif
    <div class="plan-name">{{ $plan->name }}</div>
    <div class="plan-sub">{{ $subLabels[$plan->slug] ?? '' }}</div>
    <div class="plan-price">
      <span class="amount">{{ number_format($plan->price_monthly, 0, ',', ' ') }}</span>
      <span class="unit">FCFA / {{ __('mois') }}</span>
    </div>
    <ul class="plan-features">
      @foreach($plan->features ?? [] as $feature)
      <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
      @endforeach
      <li><i class="fa-solid fa-circle-check"></i> {{ __('Commission de') }} {{ number_format($plan->commission_rate, 0) }}% {{ __('sur les réservations apportées par Hôtelio') }}</li>
    </ul>
    <a href="{{ route('register', ['role' => 'prestataire', 'plan' => $plan->slug]) }}" class="plan-cta">
      {{ __('Choisir') }} {{ $plan->name }}
    </a>
  </div>
  @endforeach
</div>

<div class="commission-note">
  <i class="fa-solid fa-circle-info"></i>
  {{ __("La commission n'est prélevée que sur les réservations apportées par la plateforme Hôtelio (site web et application mobile) — aucune commission sur vos réservations directes.") }}
</div>

<div class="footer">
  &copy; {{ date('Y') }} Hôtelio — <a href="{{ route('premiere') }}">{{ __("Retour à l'accueil") }}</a>
</div>

</body>
</html>
