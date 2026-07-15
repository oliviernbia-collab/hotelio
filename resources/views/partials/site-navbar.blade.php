<style>
/* ══════════════════════════════════════════════════
   NAVBAR (reprise de premiere.blade.php)
   ══════════════════════════════════════════════════ */
nav{
  position:fixed;top:4px;left:0;right:0;z-index:200;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 56px;height:72px;
  background:rgba(255,255,255,0.97);
  border-bottom:1px solid rgba(232,224,212,0.6);
  backdrop-filter:blur(12px);
  gap:1rem;
}
.nav-logo{
  display:flex;align-items:center;gap:8px;flex-shrink:0;
  font-family:'Fraunces',serif;font-size:24px;font-weight:400;
  color:var(--ocean);text-decoration:none;letter-spacing:-0.02em;
}
.nav-logo-img{height:44px;width:44px;object-fit:contain;filter:drop-shadow(0 1px 4px rgba(0,0,0,.18))}
.nav-links{display:flex;align-items:center;gap:6px;list-style:none}
.nav-links a{font-size:14px;font-weight:400;color:var(--text-2);text-decoration:none;padding:8px 14px;border-radius:8px;transition:all 0.2s}
.nav-links a:hover{color:var(--ocean);background:var(--sand)}
.nav-links a.active{color:var(--coral);font-weight:500}
.nav-right{display:flex;align-items:center;gap:10px}

/* ── Bouton thème ── */
.theme-btn{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:var(--text-2);padding:7px 12px;border:1px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;transition:all .2s;font-family:inherit}
.theme-btn:hover{border-color:var(--ocean);color:var(--ocean)}
.theme-btn i{font-size:13px}

/* ── Sélecteur de langue (desktop) ── */
.lang-nav-wrap{position:relative}
.lang-nav-btn{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:400;color:var(--text-2);padding:7px 12px;border:1px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;transition:all 0.2s;font-family:inherit}
.lang-nav-btn:hover,.lang-nav-btn.open{border-color:var(--ocean);color:var(--ocean)}
.lang-nav-btn .fa-chevron-down{font-size:10px;transition:transform .2s}
.lang-nav-btn.open .fa-chevron-down{transform:rotate(180deg)}
.lang-nav-dropdown{position:absolute;top:calc(100% + 8px);right:0;background:white;border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 32px rgba(12,35,64,.12);z-index:300;overflow:hidden;display:none;flex-direction:column;min-width:155px}
.lang-nav-dropdown.open{display:flex}
.lang-nav-opt{display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;font-weight:500;color:var(--text);text-decoration:none;transition:background .15s}
.lang-nav-opt:hover{background:var(--sand)}
.lang-nav-opt.lang-cur{color:var(--primary);font-weight:700}

/* ── Bouton Se connecter / S'inscrire ── */
.btn-login{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 22px;border-radius:10px;background:#e63946;color:#ffffff;font-size:14px;font-weight:600;text-decoration:none;border:1.5px solid #e63946;transition:all 0.25s ease;box-shadow:0 6px 18px rgba(230,57,70,0.25)}
.btn-login:hover{background:#ff4d5a;border-color:#ff4d5a;transform:translateY(-2px);box-shadow:0 10px 24px rgba(230,57,70,0.35)}
.btn-reserve{font-size:13px;font-weight:500;color:white;padding:9px 20px;border:1.5px solid var(--coral);border-radius:10px;background:var(--coral);cursor:pointer;transition:all 0.2s;text-decoration:none}
.btn-reserve:hover{background:var(--coral-light);border-color:var(--coral-light);transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,99,74,0.3)}

/* ── Profil connecté (desktop) ── */
.profile-nav-wrap{position:relative}
.profile-nav-btn{display:flex;align-items:center;gap:8px;padding:6px 12px 6px 6px;border:1.5px solid var(--border);border-radius:40px;background:transparent;cursor:pointer;transition:border-color .2s;font-family:inherit}
.profile-nav-btn:hover,.profile-nav-btn.open{border-color:var(--primary)}
.profile-nav-avatar{width:32px;height:32px;border-radius:50%;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;text-transform:uppercase;flex-shrink:0}
.profile-nav-name{font-size:13px;font-weight:600;color:var(--ocean);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.profile-nav-btn .fa-chevron-down{font-size:10px;color:var(--text-3);transition:transform .2s}
.profile-nav-btn.open .fa-chevron-down{transform:rotate(180deg)}
.profile-nav-dropdown{position:absolute;top:calc(100% + 10px);right:0;background:white;border:1px solid var(--border);border-radius:14px;box-shadow:0 12px 40px rgba(12,35,64,.14);z-index:300;overflow:hidden;display:none;flex-direction:column;min-width:195px}
.profile-nav-dropdown.open{display:flex}
.profile-nav-opt{display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:14px;font-weight:500;color:var(--text);text-decoration:none;transition:background .15s;border:none;background:transparent;cursor:pointer;width:100%;font-family:inherit;text-align:left}
.profile-nav-opt:hover{background:var(--sand)}
.profile-nav-opt i{width:16px;color:var(--text-3)}
.profile-nav-sep{height:1px;background:var(--border);margin:4px 0}
.profile-nav-opt.logout{color:#e63946}.profile-nav-opt.logout i{color:#e63946}
.profile-nav-opt.logout:hover{background:#fff0f0}

/* ── Hamburger + menu mobile ── */
.hamburger{display:none;flex-direction:column;justify-content:center;align-items:center;width:42px;height:42px;border:1.5px solid var(--border);border-radius:10px;background:transparent;cursor:pointer;gap:5px;transition:all 0.2s;flex-shrink:0}
.hamburger:hover{border-color:var(--ocean)}
.ham-line{display:block;width:18px;height:1.8px;background:var(--ocean);border-radius:2px;transition:transform 0.35s cubic-bezier(.77,0,.18,1),opacity 0.25s,width 0.25s;transform-origin:center}
.hamburger.open .ham-line:nth-child(1){transform:translateY(6.8px) rotate(45deg)}
.hamburger.open .ham-line:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open .ham-line:nth-child(3){transform:translateY(-6.8px) rotate(-45deg)}

.mob-overlay{position:fixed;inset:0;z-index:190;background:rgba(6,20,34,0.55);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:opacity 0.35s ease}
.mob-overlay.open{opacity:1;pointer-events:all}

.mob-menu{position:fixed;top:0;right:0;bottom:0;z-index:195;width:min(360px,93vw);background:white;transform:translateX(110%);transition:transform .42s cubic-bezier(.77,0,.18,1);display:flex;flex-direction:column;overflow:hidden;box-shadow:-8px 0 48px rgba(12,35,64,.18)}
.mob-menu.open{transform:translateX(0)}
.mob-ci-stripe{height:4px;flex-shrink:0;background:linear-gradient(to right,#F77F00 33%,#FFFFFF 33% 66%,#009E60 66%)}
.mob-header{display:flex;align-items:center;justify-content:space-between;padding:13px 16px;border-bottom:1px solid var(--border);flex-shrink:0}
.mob-brand{display:flex;align-items:center;gap:10px;text-decoration:none}
.mob-brand-icon{width:38px;height:38px;background:var(--coral);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mob-brand-icon svg{width:20px;height:20px;fill:white}
.mob-brand-name{font-family:'Fraunces',serif;font-size:19px;font-weight:400;color:var(--ocean);letter-spacing:-.02em;line-height:1}
.mob-brand-tag{font-size:10px;color:var(--text-3);font-weight:500;margin-top:2px}
.mob-close-btn{width:36px;height:36px;border-radius:10px;border:1.5px solid var(--border);background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;font-size:14px;color:var(--text-2)}
.mob-close-btn:hover{border-color:var(--coral);color:var(--coral);background:rgba(247,127,0,.06)}

.mob-profile-card{margin:14px 14px 0;padding:14px;border-radius:16px;background:linear-gradient(135deg,rgba(247,127,0,.1),rgba(247,127,0,.04));border:1px solid rgba(247,127,0,.2);display:flex;align-items:center;gap:12px;flex-shrink:0}
.mob-profile-ava{width:46px;height:46px;border-radius:50%;background:var(--coral);color:white;display:flex;align-items:center;justify-content:center;font-size:1.15rem;font-weight:800;text-transform:uppercase;flex-shrink:0;border:3px solid rgba(255,255,255,.8)}
.mob-profile-info{flex:1;min-width:0}
.mob-profile-name{font-weight:700;font-size:.88rem;color:var(--ocean);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mob-profile-email{font-size:.69rem;color:var(--text-3);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mob-role-badge{display:inline-flex;align-items:center;gap:3px;font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--coral);background:rgba(247,127,0,.12);padding:2px 8px;border-radius:20px;margin-top:4px}

.mob-auth-section{margin:14px 14px 0;flex-shrink:0}
.mob-welcome-text{font-size:.78rem;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px}
.mob-auth-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.mob-btn-connexion{display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border:1.5px solid var(--ocean);border-radius:12px;font-size:13px;font-weight:600;color:var(--ocean);text-decoration:none;transition:all .2s}
.mob-btn-connexion:hover{background:var(--ocean);color:white}
.mob-btn-inscription{display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border:1.5px solid var(--coral);border-radius:12px;background:var(--coral);font-size:13px;font-weight:600;color:white;text-decoration:none;transition:all .2s}
.mob-btn-inscription:hover{background:var(--coral-light)}

.mob-scroll{flex:1;overflow-y:auto;padding:12px 10px 6px;overscroll-behavior:contain}
.mob-scroll::-webkit-scrollbar{width:6px}
.mob-scroll::-webkit-scrollbar-thumb{background:rgba(247,127,0,.5);border-radius:10px}
.mob-scroll::-webkit-scrollbar-track{background:rgba(247,127,0,.06);border-radius:10px}
.mob-section-label{font-size:10px;font-weight:800;color:var(--text-3);text-transform:uppercase;letter-spacing:.14em;padding:8px 10px 4px;margin-top:6px}
.mob-nav-list{display:flex;flex-direction:column;gap:1px;margin-bottom:2px}
.mob-nav-item{display:flex;align-items:center;gap:12px;padding:10px 8px;border-radius:12px;text-decoration:none;transition:background .18s;cursor:pointer}
.mob-nav-item:hover{background:rgba(12,35,64,.04)}
.mob-nav-item.mob-active{background:rgba(247,127,0,.08)}
.mob-nav-ico{width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mob-nav-ico i{font-size:16px}
.mob-nav-text{flex:1;font-size:14px;font-weight:500;color:var(--text)}
.mob-nav-item.mob-active .mob-nav-text{color:var(--coral);font-weight:600}
.mob-chev{font-size:10px;color:var(--text-3);opacity:.45}

.mob-setting-item{display:flex;align-items:center;gap:12px;padding:10px 8px;border-radius:12px}
.mob-setting-info{flex:1;min-width:0}
.mob-setting-label{font-size:14px;font-weight:500;color:var(--text)}
.mob-setting-sub{font-size:11px;color:var(--text-3);margin-top:1px}
.mob-theme-toggle{border:none;background:none;cursor:pointer;padding:4px;flex-shrink:0}
.mob-toggle-track{display:block;width:46px;height:26px;border-radius:13px;background:#e2e8f0;position:relative;transition:background .3s}
.mob-toggle-track.on{background:var(--coral)}
.mob-toggle-thumb{display:block;width:20px;height:20px;border-radius:50%;background:white;position:absolute;top:3px;left:3px;transition:transform .3s;box-shadow:0 1px 4px rgba(0,0,0,.2)}
.mob-toggle-track.on .mob-toggle-thumb{transform:translateX(20px)}
.mob-lang-flags{display:flex;gap:6px;flex-shrink:0}
.mob-flag{font-size:22px;text-decoration:none;padding:2px;border-radius:6px;transition:transform .2s;opacity:.45;line-height:1}
.mob-flag:hover{opacity:.85;transform:scale(1.12)}
.mob-flag-cur{opacity:1;transform:scale(1.12)}

.mob-drawer-footer{border-top:1px solid var(--border);padding:12px 14px 16px;flex-shrink:0}
.mob-logout-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;border-radius:12px;border:1.5px solid rgba(230,57,70,.3);background:rgba(230,57,70,.06);color:#e63946;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;font-family:inherit;margin-bottom:10px}
.mob-logout-btn:hover{background:rgba(230,57,70,.12);border-color:rgba(230,57,70,.5)}
.mob-social-row{display:flex;align-items:center;justify-content:space-between}
.mob-social-label{font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;font-weight:600}
.mob-social-links{display:flex;gap:7px}
.mob-soc{width:34px;height:34px;border-radius:9px;border:1px solid var(--border);background:white;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:14px;color:var(--text-2);transition:all .2s}
.mob-soc:hover{border-color:var(--coral);color:var(--coral);background:rgba(247,127,0,.06)}
.mob-soc-ig:hover{border-color:#e1306c;color:#e1306c;background:rgba(225,48,108,.06)}
.mob-soc-wa:hover{border-color:#25d366;color:#25d366;background:rgba(37,211,102,.06)}
.mob-footer-copy{font-size:11px;color:var(--text-3);margin-top:8px;text-align:center}

@media(max-width:768px){
  nav{padding:0 16px}
  .nav-links,.nav-right{display:none}
  .hamburger{display:flex}
}
@media(min-width:769px){.hamburger{display:none}.mob-menu{display:none}.mob-overlay{display:none}}

/* ── Mode sombre ── */
[data-theme="dark"] nav{background:rgba(15,23,42,.97);border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .nav-logo{color:#e2e8f0}
[data-theme="dark"] .nav-links a{color:#94a3b8}
[data-theme="dark"] .nav-links a:hover{color:#e2e8f0;background:rgba(148,163,184,.06)}
[data-theme="dark"] .nav-links a.active{color:var(--primary)}
[data-theme="dark"] .hamburger{border-color:rgba(255,255,255,.35);background:rgba(255,255,255,.08)}
[data-theme="dark"] .hamburger:hover{border-color:rgba(255,255,255,.65);background:rgba(255,255,255,.15)}
[data-theme="dark"] .ham-line{background:#ffffff}
[data-theme="dark"] .lang-nav-btn,[data-theme="dark"] .theme-btn{color:#94a3b8;border-color:rgba(148,163,184,.2)}
[data-theme="dark"] .lang-nav-btn:hover,[data-theme="dark"] .theme-btn:hover{color:#e2e8f0;border-color:rgba(148,163,184,.5)}
[data-theme="dark"] .lang-nav-dropdown,[data-theme="dark"] .profile-nav-dropdown{background:#1e293b;border-color:rgba(148,163,184,.15)}
[data-theme="dark"] .lang-nav-opt,[data-theme="dark"] .profile-nav-opt{color:#cbd5e1}
[data-theme="dark"] .lang-nav-opt:hover,[data-theme="dark"] .profile-nav-opt:hover{background:rgba(148,163,184,.08)}
[data-theme="dark"] .profile-nav-btn{border-color:rgba(148,163,184,.2)}
[data-theme="dark"] .profile-nav-name{color:#e2e8f0}
[data-theme="dark"] .profile-nav-sep{background:rgba(148,163,184,.1)}
[data-theme="dark"] .mob-menu{background:#1e293b}
[data-theme="dark"] .mob-header{border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .mob-brand-name{color:#e2e8f0}
[data-theme="dark"] .mob-brand-tag{color:#64748b}
[data-theme="dark"] .mob-close-btn{border-color:rgba(148,163,184,.2);color:#94a3b8}
[data-theme="dark"] .mob-close-btn:hover{border-color:var(--coral);color:var(--coral)}
[data-theme="dark"] .mob-profile-card{background:linear-gradient(135deg,rgba(247,127,0,.12),rgba(247,127,0,.05));border-color:rgba(247,127,0,.25)}
[data-theme="dark"] .mob-profile-name{color:#e2e8f0}
[data-theme="dark"] .mob-profile-email{color:#64748b}
[data-theme="dark"] .mob-welcome-text{color:#64748b}
[data-theme="dark"] .mob-btn-connexion{border-color:rgba(148,163,184,.3);color:#94a3b8}
[data-theme="dark"] .mob-btn-connexion:hover{background:#1e293b;color:#e2e8f0}
[data-theme="dark"] .mob-section-label{color:#475569}
[data-theme="dark"] .mob-nav-item:hover{background:rgba(148,163,184,.07)}
[data-theme="dark"] .mob-nav-item.mob-active{background:rgba(247,127,0,.1)}
[data-theme="dark"] .mob-nav-text{color:#cbd5e1}
[data-theme="dark"] .mob-nav-item.mob-active .mob-nav-text{color:var(--coral)}
[data-theme="dark"] .mob-chev{color:#64748b}
[data-theme="dark"] .mob-setting-item:hover{background:rgba(148,163,184,.05)}
[data-theme="dark"] .mob-setting-label{color:#cbd5e1}
[data-theme="dark"] .mob-setting-sub{color:#64748b}
[data-theme="dark"] .mob-toggle-track{background:#334155}
[data-theme="dark"] .mob-drawer-footer{border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .mob-soc{border-color:rgba(148,163,184,.2);background:rgba(148,163,184,.06);color:#94a3b8}
[data-theme="dark"] .mob-footer-copy{color:#475569}
</style>

<nav>
  <a href="{{ route('premiere') }}" class="nav-logo">
    <img src="{{ asset('images/logo.png') }}" alt="Hôtelio" class="nav-logo-img">
    Hôtelio
  </a>

  <ul class="nav-links">
    <li><a href="{{ route('premiere') }}">{{ __('Accueil') }}</a></li>
    <li><a href="{{ route('hotels.public') }}">{{ __('Hôtels') }}</a></li>
  </ul>

  <div class="nav-right">
    <button class="theme-btn" id="themeToggle">
      <i class="fa-solid fa-moon" id="themeIcon"></i>
      <span id="themeLabel">{{ __('Mode sombre') }}</span>
    </button>

    <div class="lang-nav-wrap" id="langNavWrap">
      <button class="lang-nav-btn" id="langNavBtn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
        {{ ['fr'=>'Français','en'=>'English','es'=>'Español'][app()->getLocale()] ?? 'Français' }}
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="lang-nav-dropdown" id="langNavDropdown">
        <a href="{{ route('locale.switch', 'fr') }}" class="lang-nav-opt {{ app()->getLocale()==='fr' ? 'lang-cur' : '' }}">🇫🇷 Français</a>
        <a href="{{ route('locale.switch', 'en') }}" class="lang-nav-opt {{ app()->getLocale()==='en' ? 'lang-cur' : '' }}">🇬🇧 English</a>
        <a href="{{ route('locale.switch', 'es') }}" class="lang-nav-opt {{ app()->getLocale()==='es' ? 'lang-cur' : '' }}">🇪🇸 Español</a>
      </div>
    </div>

    @auth
      <div class="profile-nav-wrap" id="profileNavWrap">
        <button class="profile-nav-btn" id="profileNavBtn">
          <div class="profile-nav-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
          <span class="profile-nav-name">{{ auth()->user()->name }}</span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>
        <div class="profile-nav-dropdown" id="profileNavDropdown">
          @if(auth()->user()->role==='admin')
            <a href="{{ route('admin.index') }}" class="profile-nav-opt"><i class="fa-solid fa-crown"></i> {{ __('Panel Admin') }}</a>
          @elseif(auth()->user()->role==='prestataire')
            <a href="{{ route('prestataire.index') }}" class="profile-nav-opt"><i class="fa-solid fa-gauge-high"></i> {{ __('Mon Dashboard') }}</a>
          @else
            <a href="{{ route('client.index') }}" class="profile-nav-opt"><i class="fa-solid fa-gauge-high"></i> {{ __('Mon Dashboard') }}</a>
          @endif
          <a href="{{ route('profile.index') }}" class="profile-nav-opt"><i class="fa-solid fa-id-card"></i> {{ __('Mon Profil') }}</a>
          <div class="profile-nav-sep"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="profile-nav-opt logout"><i class="fa-solid fa-right-from-bracket"></i> {{ __('Déconnexion') }}</button>
          </form>
        </div>
      </div>
    @else
      <a href="{{ route('login') }}" class="btn-login">{{ __('Se connecter') }}</a>
      <a href="{{ route('register') }}" class="btn-reserve">{{ __("S'inscrire") }}</a>
    @endauth
  </div>

  <button class="hamburger" id="hamburger" aria-label="Menu">
    <span class="ham-line"></span><span class="ham-line"></span><span class="ham-line"></span>
  </button>
</nav>

<!-- ═══ MOBILE OVERLAY ═══ -->
<div class="mob-overlay" id="mobOverlay"></div>

<!-- ═══ MOBILE MENU ═══ -->
<div class="mob-menu" id="mobMenu">
  <div class="mob-ci-stripe"></div>

  <div class="mob-header">
    <a href="{{ route('premiere') }}" class="mob-brand">
      <div class="mob-brand-icon">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
      </div>
      <div>
        <div class="mob-brand-name">Hôtelio</div>
        <div class="mob-brand-tag">{{ __('La plateforme N°1 des hébergements en Côte d\'Ivoire') }}</div>
      </div>
    </a>
    <button class="mob-close-btn" id="mobClose" aria-label="{{ __('Fermer') }}">
      <i class="fas fa-xmark"></i>
    </button>
  </div>

  @auth
    <div class="mob-profile-card">
      <div class="mob-profile-ava">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
      <div class="mob-profile-info">
        <div class="mob-profile-name">{{ auth()->user()->name }}</div>
        <div class="mob-profile-email">{{ auth()->user()->email }}</div>
        <div class="mob-role-badge">
          @if(auth()->user()->role==='admin') 👑 Admin
          @elseif(auth()->user()->role==='prestataire') 🏨 {{ __('Hôtelier') }}
          @else ✈️ {{ __('Voyageur') }}
          @endif
        </div>
      </div>
    </div>
  @else
    <div class="mob-auth-section">
      <p class="mob-welcome-text">{{ __('Bienvenue sur Hôtelio') }}</p>
      <div class="mob-auth-grid">
        <a href="{{ route('login') }}" class="mob-btn-connexion">
          <i class="fas fa-right-to-bracket"></i> {{ __('Se connecter') }}
        </a>
        <a href="{{ route('register') }}" class="mob-btn-inscription">
          <i class="fas fa-user-plus"></i> {{ __("S'inscrire") }}
        </a>
      </div>
    </div>
  @endauth

  <div class="mob-scroll">
    <div class="mob-section-label">{{ __('Navigation') }}</div>
    <div class="mob-nav-list">
      <a href="{{ route('premiere') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(247,127,0,.12)">
          <i class="fas fa-house" style="color:var(--coral)"></i>
        </div>
        <span class="mob-nav-text">{{ __('Accueil') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
      <a href="{{ route('hotels.public') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(59,130,246,.12)">
          <i class="fas fa-hotel" style="color:#3b82f6"></i>
        </div>
        <span class="mob-nav-text">{{ __('Hôtels') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
    </div>

    @auth
    <div class="mob-section-label">{{ __('Mon compte') }}</div>
    <div class="mob-nav-list">
      @if(auth()->user()->role==='admin')
      <a href="{{ route('admin.index') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(247,127,0,.12)">
          <i class="fas fa-crown" style="color:var(--coral)"></i>
        </div>
        <span class="mob-nav-text">{{ __('Panel Admin') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
      @elseif(auth()->user()->role==='prestataire')
      <a href="{{ route('prestataire.index') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(247,127,0,.12)">
          <i class="fas fa-gauge-high" style="color:var(--coral)"></i>
        </div>
        <span class="mob-nav-text">{{ __('Mon Dashboard') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
      @else
      <a href="{{ route('client.index') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(247,127,0,.12)">
          <i class="fas fa-gauge-high" style="color:var(--coral)"></i>
        </div>
        <span class="mob-nav-text">{{ __('Mon Dashboard') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
      @endif
      <a href="{{ route('profile.index') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(99,102,241,.12)">
          <i class="fas fa-id-card" style="color:#6366f1"></i>
        </div>
        <span class="mob-nav-text">{{ __('Mon Profil') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
    </div>
    @endauth

    <div class="mob-section-label">{{ __('Paramètres') }}</div>

    <div class="mob-setting-item">
      <div class="mob-nav-ico" style="background:rgba(245,158,11,.12)">
        <i class="fas fa-moon" id="mobThemeIcon" style="color:#f59e0b"></i>
      </div>
      <div class="mob-setting-info">
        <div class="mob-setting-label" id="mobThemeLabel">{{ __('Mode sombre') }}</div>
        <div class="mob-setting-sub">{{ __('Changer le thème') }}</div>
      </div>
      <button class="mob-theme-toggle" id="mobThemeToggle" aria-label="Toggle theme">
        <span class="mob-toggle-track" id="mobToggleTrack">
          <span class="mob-toggle-thumb"></span>
        </span>
      </button>
    </div>

    <div class="mob-setting-item">
      <div class="mob-nav-ico" style="background:rgba(14,165,233,.12)">
        <i class="fas fa-globe" style="color:#0ea5e9"></i>
      </div>
      <div class="mob-setting-info">
        <div class="mob-setting-label">{{ __('Langue') }}</div>
        <div class="mob-setting-sub">{{ ['fr'=>'Français','en'=>'English','es'=>'Español'][app()->getLocale()] ?? 'Français' }}</div>
      </div>
      <div class="mob-lang-flags">
        <a href="{{ route('locale.switch', 'fr') }}" class="mob-flag {{ app()->getLocale()==='fr' ? 'mob-flag-cur' : '' }}" title="Français">🇫🇷</a>
        <a href="{{ route('locale.switch', 'en') }}" class="mob-flag {{ app()->getLocale()==='en' ? 'mob-flag-cur' : '' }}" title="English">🇬🇧</a>
        <a href="{{ route('locale.switch', 'es') }}" class="mob-flag {{ app()->getLocale()==='es' ? 'mob-flag-cur' : '' }}" title="Español">🇪🇸</a>
      </div>
    </div>
  </div>

  <div class="mob-drawer-footer">
    @auth
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="mob-logout-btn">
        <i class="fas fa-right-from-bracket"></i> {{ __('Déconnexion') }}
      </button>
    </form>
    @endauth
    <div class="mob-social-row">
      <span class="mob-social-label">{{ __('Suivez-nous') }}</span>
      <div class="mob-social-links">
        <a href="#" class="mob-soc" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="mob-soc mob-soc-ig" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" class="mob-soc mob-soc-wa" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      </div>
    </div>
    <p class="mob-footer-copy">© 2026 Hôtelio · Côte d'Ivoire</p>
  </div>
</div>

<script>
(function(){
  const hamburger  = document.getElementById('hamburger');
  const mobMenu    = document.getElementById('mobMenu');
  const mobOverlay = document.getElementById('mobOverlay');
  const mobClose   = document.getElementById('mobClose');
  function openMenu(){mobMenu.classList.add('open');mobOverlay.classList.add('open');hamburger.classList.add('open');document.body.style.overflow='hidden'}
  function closeMenu(){mobMenu.classList.remove('open');mobOverlay.classList.remove('open');hamburger.classList.remove('open');document.body.style.overflow=''}
  hamburger?.addEventListener('click', () => mobMenu.classList.contains('open') ? closeMenu() : openMenu());
  mobClose?.addEventListener('click', closeMenu);
  mobOverlay?.addEventListener('click', closeMenu);
  let touchStartX = 0;
  mobMenu?.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, {passive:true});
  mobMenu?.addEventListener('touchend', e => { if(e.changedTouches[0].clientX - touchStartX > 60) closeMenu(); });

  // ── Mode clair / sombre ──
  const htmlEl         = document.documentElement;
  const themeToggle    = document.getElementById('themeToggle');
  const themeIcon      = document.getElementById('themeIcon');
  const themeLabel     = document.getElementById('themeLabel');
  const mobThemeToggle = document.getElementById('mobThemeToggle');
  const mobThemeIcon   = document.getElementById('mobThemeIcon');
  const mobThemeLabel  = document.getElementById('mobThemeLabel');
  const mobToggleTrack = document.getElementById('mobToggleTrack');
  const LABEL_DARK     = @json(__('Mode sombre'));
  const LABEL_LIGHT    = @json(__('Mode clair'));

  function applyTheme(theme) {
    htmlEl.setAttribute('data-theme', theme);
    localStorage.setItem('hotelio-pub-theme', theme);
    const isDark = theme === 'dark';
    if (themeIcon)  themeIcon.className   = isDark ? 'fa-solid fa-sun'  : 'fa-solid fa-moon';
    if (themeLabel) themeLabel.textContent = isDark ? LABEL_LIGHT       : LABEL_DARK;
    if (mobThemeIcon)   mobThemeIcon.className    = isDark ? 'fas fa-sun' : 'fas fa-moon';
    if (mobThemeLabel)  mobThemeLabel.textContent  = isDark ? LABEL_LIGHT : LABEL_DARK;
    if (mobToggleTrack) mobToggleTrack.classList.toggle('on', isDark);
  }
  applyTheme(localStorage.getItem('hotelio-pub-theme') || 'dark');
  themeToggle?.addEventListener('click',    () => applyTheme(htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));
  mobThemeToggle?.addEventListener('click', () => applyTheme(htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));

  // ── Dropdown langue ──
  const langNavBtn      = document.getElementById('langNavBtn');
  const langNavDropdown = document.getElementById('langNavDropdown');
  const langNavWrap     = document.getElementById('langNavWrap');
  langNavBtn?.addEventListener('click', e => {
    e.stopPropagation();
    const open = langNavDropdown.classList.contains('open');
    langNavDropdown.classList.toggle('open', !open);
    langNavBtn.classList.toggle('open', !open);
  });

  // ── Dropdown profil ──
  const profileNavBtn      = document.getElementById('profileNavBtn');
  const profileNavDropdown = document.getElementById('profileNavDropdown');
  const profileNavWrap     = document.getElementById('profileNavWrap');
  profileNavBtn?.addEventListener('click', e => {
    e.stopPropagation();
    const open = profileNavDropdown.classList.contains('open');
    profileNavDropdown.classList.toggle('open', !open);
    profileNavBtn.classList.toggle('open', !open);
  });

  document.addEventListener('click', e => {
    if(langNavWrap && !langNavWrap.contains(e.target)){
      langNavDropdown?.classList.remove('open');
      langNavBtn?.classList.remove('open');
    }
    if(profileNavWrap && !profileNavWrap.contains(e.target)){
      profileNavDropdown?.classList.remove('open');
      profileNavBtn?.classList.remove('open');
    }
  });
})();
</script>
