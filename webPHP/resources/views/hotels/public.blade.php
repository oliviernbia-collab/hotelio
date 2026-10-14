<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __('Recherche Hôtels') }} — Hôtelio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --ci-orange:#F77F00;--ci-orange2:#e06e00;--ci-green:#009A44;
  --ocean:#0C2340;--sand:#F8F0E3;--sand2:#F2E8D6;
  --coral:#F77F00;--primary:#F77F00;--primary-light:#e06e00;
  --white:#FFFFFF;--text:#1A1A1A;--text-2:#4A4A4A;--text-3:#7A7A7A;
  --border:#E8E0D4;
  --bg-page:#F8F5F0;
}
html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg-page);color:var(--text);padding-top:76px}
.page-wrap{overflow-x:hidden}
a{text-decoration:none;color:inherit}

/* ══ BANDE DRAPEAU CI ══ */
body::before{content:"";position:fixed;top:0;left:0;width:100%;height:4px;z-index:1000;background:linear-gradient(to right,#F77F00 33%,#FFFFFF 33% 66%,#009E60 66%)}

/* ══════════════════════════════════════════════════
   NAVBAR (même que premiere.blade.php)
   ══════════════════════════════════════════════════ */
.site-nav{
  position:fixed;top:4px;left:0;right:0;z-index:500;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 56px;height:68px;gap:1rem;
  background:#ffffff;
  border-bottom:2px solid rgba(232,224,212,.8);
  box-shadow:0 4px 24px rgba(12,35,64,.10);
  opacity:1;
}
.nav-logo{display:flex;align-items:center;gap:8px;flex-shrink:0;font-family:'Fraunces',serif;font-size:24px;font-weight:400;color:var(--ocean);text-decoration:none;letter-spacing:-.02em}
.logo-icon{width:36px;height:36px;background:var(--coral);border-radius:10px;display:flex;align-items:center;justify-content:center}
.logo-icon svg{width:20px;height:20px;fill:white}
.nav-links{display:flex;align-items:center;gap:6px;list-style:none;flex:1}
.nav-links a{font-size:14px;font-weight:400;color:var(--text-2);text-decoration:none;padding:8px 14px;border-radius:8px;transition:all .2s}
.nav-links a:hover{color:var(--ocean);background:var(--sand)}
.nav-links a.active{color:var(--coral);font-weight:600}
.nav-right{display:flex;align-items:center;gap:10px;flex-shrink:0}

/* Bouton thème */
.theme-btn{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:var(--text-2);padding:7px 12px;border:1px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;transition:all .2s;font-family:inherit}
.theme-btn:hover{border-color:var(--ocean);color:var(--ocean)}

/* Sélecteur de langue */
.lang-nav-wrap{position:relative}
.lang-nav-btn{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:400;color:var(--text-2);padding:7px 12px;border:1px solid var(--border);border-radius:8px;background:transparent;cursor:pointer;transition:all .2s;font-family:inherit}
.lang-nav-btn:hover,.lang-nav-btn.open{border-color:var(--ocean);color:var(--ocean)}
.lang-nav-btn .fa-chevron-down{font-size:10px;transition:transform .2s}
.lang-nav-btn.open .fa-chevron-down{transform:rotate(180deg)}
.lang-nav-dropdown{position:absolute;top:calc(100% + 8px);right:0;background:white;border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 32px rgba(12,35,64,.12);z-index:300;overflow:hidden;display:none;flex-direction:column;min-width:155px}
.lang-nav-dropdown.open{display:flex}
.lang-nav-opt{display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;font-weight:500;color:var(--text);text-decoration:none;transition:background .15s}
.lang-nav-opt:hover{background:var(--sand)}
.lang-nav-opt.lang-cur{color:var(--primary);font-weight:700}

/* Boutons auth */
.btn-login{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 22px;border-radius:10px;background:#e63946;color:#fff;font-size:14px;font-weight:600;text-decoration:none;border:1.5px solid #e63946;transition:all .25s;box-shadow:0 6px 18px rgba(230,57,70,.25);font-family:inherit}
.btn-login:hover{background:#ff4d5a;border-color:#ff4d5a;transform:translateY(-2px);box-shadow:0 10px 24px rgba(230,57,70,.35)}
.btn-login:active{transform:scale(.97)}
.btn-reserve{font-size:13px;font-weight:500;color:white;padding:9px 20px;border:1.5px solid var(--coral);border-radius:10px;background:var(--coral);cursor:pointer;transition:all .2s;text-decoration:none;font-family:inherit}
.btn-reserve:hover{background:var(--primary-light);border-color:var(--primary-light);transform:translateY(-1px);box-shadow:0 6px 20px rgba(247,127,0,.3)}

/* Profil connecté */
.profile-nav-wrap{position:relative}
.profile-nav-btn{display:flex;align-items:center;gap:8px;padding:6px 12px 6px 6px;border:1.5px solid var(--border);border-radius:40px;background:transparent;cursor:pointer;transition:border-color .2s;font-family:inherit}
.profile-nav-btn:hover,.profile-nav-btn.open{border-color:var(--coral)}
.profile-nav-avatar{width:32px;height:32px;border-radius:50%;background:var(--coral);color:white;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;text-transform:uppercase;flex-shrink:0}
.profile-nav-name{font-size:13px;font-weight:600;color:var(--ocean);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.profile-nav-btn .fa-chevron-down{font-size:10px;color:var(--text-3);transition:transform .2s}
.profile-nav-btn.open .fa-chevron-down{transform:rotate(180deg)}
.profile-nav-dropdown{position:absolute;top:calc(100% + 10px);right:0;background:white;border:1.5px solid var(--border);border-radius:16px;box-shadow:0 16px 48px rgba(12,35,64,.14);z-index:300;overflow:hidden;display:none;flex-direction:column;min-width:195px}
.profile-nav-dropdown.open{display:flex}
.profile-nav-opt{display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:14px;font-weight:500;color:var(--text);text-decoration:none;transition:background .15s;border:none;background:transparent;cursor:pointer;width:100%;font-family:inherit;text-align:left}
.profile-nav-opt:hover{background:var(--sand)}
.profile-nav-opt i{width:16px;color:var(--text-3)}
.profile-nav-sep{height:1px;background:var(--border);margin:4px 0}
.profile-nav-opt.logout{color:#e63946}.profile-nav-opt.logout i{color:#e63946}.profile-nav-opt.logout:hover{background:#fff0f0}

/* ══ HAMBURGER ══ */
.hamburger{display:none;flex-direction:column;justify-content:center;align-items:center;width:42px;height:42px;border:1.5px solid var(--border);border-radius:10px;background:transparent;cursor:pointer;gap:5px;transition:all .2s;flex-shrink:0}
.hamburger:hover{border-color:var(--ocean)}
.ham-line{display:block;width:18px;height:1.8px;background:var(--ocean);border-radius:2px;transition:transform .35s cubic-bezier(.77,0,.18,1),opacity .25s,width .25s;transform-origin:center}
[data-theme="dark"] .hamburger{border-color:rgba(255,255,255,.35);background:rgba(255,255,255,.08)}
[data-theme="dark"] .hamburger:hover{border-color:rgba(255,255,255,.65);background:rgba(255,255,255,.15)}
[data-theme="dark"] .ham-line{background:#ffffff}
.hamburger.open .ham-line:nth-child(1){transform:translateY(6.8px) rotate(45deg)}
.hamburger.open .ham-line:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open .ham-line:nth-child(3){transform:translateY(-6.8px) rotate(-45deg)}

/* ══ MOBILE OVERLAY ══ */
.mob-overlay{position:fixed;inset:0;z-index:190;background:rgba(6,20,34,.55);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:opacity .35s ease}
.mob-overlay.open{opacity:1;pointer-events:all}

/* ══ MOBILE MENU PRO ══ */
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
.mob-btn-inscription:hover{background:var(--primary-light)}
.mob-scroll{flex:1;overflow-y:auto;padding:12px 10px 6px;overscroll-behavior:contain}
.mob-scroll::-webkit-scrollbar{width:6px}
.mob-scroll::-webkit-scrollbar-thumb{background:rgba(247,127,0,.5);border-radius:10px}
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
.mob-soc:hover{border-color:var(--coral);color:var(--coral)}
.mob-soc-ig:hover{border-color:#e1306c;color:#e1306c}
.mob-soc-wa:hover{border-color:#25d366;color:#25d366}
.mob-footer-copy{font-size:11px;color:var(--text-3);margin-top:8px;text-align:center}
.mob-menu.open .mob-nav-item{animation:mobItemIn .35s ease both}
.mob-menu.open .mob-nav-item:nth-child(1){animation-delay:.04s}
.mob-menu.open .mob-nav-item:nth-child(2){animation-delay:.08s}
.mob-menu.open .mob-nav-item:nth-child(3){animation-delay:.12s}
.mob-menu.open .mob-nav-item:nth-child(4){animation-delay:.16s}
.mob-menu.open .mob-nav-item:nth-child(5){animation-delay:.20s}
@keyframes mobItemIn{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:translateX(0)}}

/* ══ DARK MODE ══ */
[data-theme="dark"] body{background:#0f172a;color:#e2e8f0}
[data-theme="dark"] .site-nav{background:rgba(10,18,36,.98);border-color:rgba(148,163,184,.12);box-shadow:0 4px 24px rgba(0,0,0,.35)}
[data-theme="dark"] .nav-logo{color:#e2e8f0}
[data-theme="dark"] .nav-links a{color:#94a3b8}
[data-theme="dark"] .nav-links a:hover{color:#e2e8f0;background:rgba(148,163,184,.06)}
[data-theme="dark"] .nav-links a.active{color:var(--primary)}
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
[data-theme="dark"] .mob-section-label{color:#475569}
[data-theme="dark"] .mob-nav-item:hover{background:rgba(148,163,184,.07)}
[data-theme="dark"] .mob-nav-item.mob-active{background:rgba(247,127,0,.1)}
[data-theme="dark"] .mob-nav-text{color:#cbd5e1}
[data-theme="dark"] .mob-nav-item.mob-active .mob-nav-text{color:var(--coral)}
[data-theme="dark"] .mob-chev{color:#64748b}
[data-theme="dark"] .mob-setting-label{color:#cbd5e1}
[data-theme="dark"] .mob-setting-sub{color:#64748b}
[data-theme="dark"] .mob-toggle-track{background:#334155}
[data-theme="dark"] .mob-drawer-footer{border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .mob-soc{border-color:rgba(148,163,184,.2);background:rgba(148,163,184,.06);color:#94a3b8}
[data-theme="dark"] .mob-footer-copy{color:#475569}

/* ══════════════════════════════════════════════════
   PAGE CONTENU
   ══════════════════════════════════════════════════ */

/* Hero recherche */
.search-hero{
  background:linear-gradient(135deg,#0C2340 0%,#1a3a6e 60%,#0d1f3c 100%);
  padding:36px 56px 32px;
  position:relative;overflow:hidden;
}
.search-hero::before{content:"";position:absolute;inset:0;background:radial-gradient(ellipse at 70% 50%,rgba(247,127,0,.15),transparent 65%)}
.search-hero-title{font-family:'Fraunces',serif;font-size:clamp(22px,3vw,32px);font-weight:300;color:white;margin-bottom:6px;position:relative}
.search-hero-sub{font-size:14px;color:rgba(255,255,255,.6);margin-bottom:24px;position:relative}
.search-form{display:flex;gap:12px;align-items:flex-end;max-width:1100px;position:relative;flex-wrap:wrap}
.sf{flex:1;min-width:120px;display:flex;flex-direction:column;gap:5px}
.sf-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.6)}
.sf-input{
  padding:11px 14px;border:1.5px solid rgba(255,255,255,.2);border-radius:10px;
  font-size:14px;color:white;font-family:'Plus Jakarta Sans',sans-serif;
  background:rgba(255,255,255,.1);transition:border-color .2s;
}
.sf-input::placeholder{color:rgba(255,255,255,.4)}
.sf-input:focus{outline:none;border-color:var(--coral);background:rgba(255,255,255,.15)}
.sf-input option{color:var(--text);background:white}
.search-btn{
  padding:11px 28px;background:var(--coral);color:white;border:none;
  border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;
  transition:all .25s;white-space:nowrap;display:flex;align-items:center;gap:8px;flex-shrink:0;
}
.search-btn:hover{background:var(--primary-light);transform:translateY(-1px)}

/* Layout principal */
.layout{display:flex;gap:24px;padding:28px 56px;max-width:1400px;margin:0 auto}

/* Filtres */
.filters-panel{width:250px;flex-shrink:0}
.filter-card{background:white;border-radius:16px;padding:18px;box-shadow:0 2px 12px rgba(12,31,61,.06);border:1px solid var(--border);margin-bottom:14px}
.filter-title{font-size:12px;font-weight:700;color:var(--ocean);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px}
.filter-group{display:flex;flex-direction:column;gap:6px}
.filter-option{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;font-size:14px;color:var(--text-2);transition:background .18s;text-decoration:none}
.filter-option:hover{background:var(--sand)}
.filter-option.active{background:rgba(247,127,0,.1);color:var(--coral);font-weight:500}
.price-input{flex:1;padding:9px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Plus Jakarta Sans',sans-serif;background:var(--sand)}
.price-input:focus{outline:none;border-color:var(--coral)}
.apply-filter{width:100%;padding:10px;background:var(--ocean);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;margin-top:12px;transition:background .2s;font-family:'Plus Jakarta Sans',sans-serif}
.apply-filter:hover{background:#1a3a6e}

/* Résultats */
.results{flex:1}
.results-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px}
.results-count{font-size:15px;color:var(--text-2)}
.results-count strong{color:var(--ocean);font-weight:700}
.sort-select{padding:9px 14px;border:1.5px solid var(--border);border-radius:9px;font-size:13px;color:var(--text);font-family:'Plus Jakarta Sans',sans-serif;background:white;cursor:pointer}
.sort-select:focus{outline:none;border-color:var(--coral)}

/* Grille de cartes */
.cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px}

/* ── Carte hôtel ── */
.hotel-card{
  background:white;border-radius:18px;overflow:hidden;
  box-shadow:0 2px 12px rgba(12,31,61,.07);border:1px solid var(--border);
  transition:transform .3s,box-shadow .3s;
  display:flex;flex-direction:column;
}
.hotel-card:hover{transform:translateY(-5px);box-shadow:0 16px 40px rgba(12,31,61,.14)}

/* Image / placeholder */
.card-img{position:relative;height:200px;overflow:hidden;flex-shrink:0}
.card-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s;display:block}
.hotel-card:hover .card-img img{transform:scale(1.07)}
.card-img-placeholder{
  width:100%;height:100%;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:8px;
}
.card-img-placeholder i{font-size:36px;color:rgba(255,255,255,.8)}
.card-img-placeholder span{font-size:13px;font-weight:600;color:rgba(255,255,255,.7)}

/* Lien couvrant l'image (mobile touch) */
.card-img-link{position:absolute;inset:0;z-index:2;display:block}

.card-badge{position:absolute;top:12px;left:12px;z-index:3;background:rgba(255,255,255,.95);border-radius:6px;padding:4px 10px;font-size:11px;font-weight:600;color:var(--ocean)}
.card-badge.premium{background:var(--coral);color:white}
.card-heart{position:absolute;top:12px;right:12px;z-index:3;width:34px;height:34px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;color:#999;transition:all .2s;border:none}
.card-heart:hover{background:white;color:var(--coral)}
.card-heart.liked{color:var(--coral)}

/* Corps de la carte */
.card-body{padding:14px 16px;flex:1;display:flex;flex-direction:column}
.card-name{font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;line-height:1.3}
.card-loc{font-size:12px;color:var(--text-3);margin-bottom:8px;display:flex;align-items:center;gap:5px}
.card-stars{display:flex;gap:2px;margin-bottom:8px}
.card-stars i{font-size:11px;color:#f4a423}
.card-tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px}
.card-tag{font-size:11px;padding:3px 8px;border-radius:5px;background:rgba(247,127,0,.1);color:var(--coral);font-weight:500}
.card-footer{display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid var(--border);margin-top:auto}
.card-price{font-size:15px;font-weight:700;color:var(--ocean)}
.card-price small{font-size:11px;font-weight:400;color:var(--text-3)}
.card-rating{display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:var(--text)}
.card-rating i{color:#f4a423;font-size:12px}

/* Bouton Voir & Réserver */
.btn-book{
  display:flex;align-items:center;justify-content:center;gap:6px;
  width:100%;padding:10px;margin-top:12px;
  background:var(--coral);color:white;border:none;border-radius:10px;
  font-size:13px;font-weight:700;cursor:pointer;text-align:center;
  transition:all .2s;text-decoration:none;font-family:'Plus Jakarta Sans',sans-serif;
}
.btn-book:hover{background:var(--primary-light);transform:translateY(-1px)}

/* État vide */
.empty-state{text-align:center;padding:80px 20px;background:white;border-radius:18px;border:1px solid var(--border)}
.empty-icon{font-size:56px;margin-bottom:16px;opacity:.5}
.empty-title{font-family:'Fraunces',serif;font-size:24px;color:var(--ocean);margin-bottom:8px}
.empty-sub{font-size:15px;color:var(--text-3)}

/* Pagination */
.pagination-wrap{margin-top:32px;display:flex;justify-content:flex-end}
.pagination-wrap .pagination{display:flex;gap:6px;list-style:none}
.pagination-wrap .page-item .page-link{padding:9px 14px;border-radius:9px;border:1.5px solid var(--border);font-size:13px;color:var(--text-2);background:white;cursor:pointer;transition:all .2s;text-decoration:none}
.pagination-wrap .page-item.active .page-link{background:var(--coral);border-color:var(--coral);color:white}
.pagination-wrap .page-item .page-link:hover{border-color:var(--coral);color:var(--coral)}

/* Dark mode page */
[data-theme="dark"] .search-hero{background:linear-gradient(135deg,#0a1628 0%,#122040 60%,#080f20 100%)}
[data-theme="dark"] .filter-card,[data-theme="dark"] .hotel-card,[data-theme="dark"] .empty-state{background:#1e293b;border-color:rgba(148,163,184,.12)}
[data-theme="dark"] .sort-select{background:#1e293b;border-color:rgba(148,163,184,.2);color:#e2e8f0}
[data-theme="dark"] .sort-select option{background:#1e293b;color:#e2e8f0}
[data-theme="dark"] .filter-title,[data-theme="dark"] .card-name,.results-count strong{color:#e2e8f0}
[data-theme="dark"] .results-count{color:#94a3b8}
[data-theme="dark"] .filter-option{color:#94a3b8}
[data-theme="dark"] .filter-option:hover{background:rgba(148,163,184,.07)}
[data-theme="dark"] .card-footer{border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .price-input,.sf-input{border-color:rgba(148,163,184,.15)}
[data-theme="dark"] .card-loc{color:#64748b}
[data-theme="dark"] .card-price{color:#e2e8f0}

/* Responsive */
@media(max-width:900px){
  .site-nav{padding:0 16px}
  .nav-links,.nav-right{display:none}
  .hamburger{display:flex}
  .search-hero{padding:24px 20px 20px}
  .layout{flex-direction:column;padding:20px}
  .filters-panel{width:100%}
  .cards-grid{grid-template-columns:1fr 1fr}
}
@media(min-width:901px){.hamburger{display:none}.mob-menu{display:none}.mob-overlay{display:none}}
@media(max-width:600px){
  body{padding-top:68px}
  .cards-grid{grid-template-columns:1fr}
  .search-form{flex-direction:column}
  .sf{min-width:100%}
}
</style>
</head>
<body>

<!-- ═══ NAVBAR ═══ -->
<nav class="site-nav">
  <a href="{{ route('premiere') }}" class="nav-logo">
    <span class="logo-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg></span>
    Hôtelio
  </a>

  <ul class="nav-links">
    <li><a href="{{ route('premiere') }}">{{ __('Accueil') }}</a></li>
    <li><a href="{{ route('hotels.public') }}" class="active">{{ __('Hôtels') }}</a></li>
  </ul>

  <div class="nav-right">
    {{-- Bouton Mode sombre/clair --}}
    <button class="theme-btn" id="themeToggle">
      <i class="fa-solid fa-moon" id="themeIcon"></i>
      <span id="themeLabel">{{ __('Mode sombre') }}</span>
    </button>

    {{-- Sélecteur de langue --}}
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

<!-- ═══ MOBILE MENU PRO ═══ -->
<div class="mob-menu" id="mobMenu">

  <div class="mob-ci-stripe"></div>

  <div class="mob-header">
    <a href="{{ url('/') }}" class="mob-brand">
      <div class="mob-brand-icon">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
      </div>
      <div>
        <div class="mob-brand-name">Hôtelio</div>
        <div class="mob-brand-tag">{{ __("La plateforme N°1 des hébergements en Côte d'Ivoire") }}</div>
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
      <a href="{{ url('/') }}" class="mob-nav-item">
        <div class="mob-nav-ico" style="background:rgba(247,127,0,.12)">
          <i class="fas fa-house" style="color:var(--coral)"></i>
        </div>
        <span class="mob-nav-text">{{ __('Accueil') }}</span>
        <i class="fas fa-chevron-right mob-chev"></i>
      </a>
      <a href="{{ route('hotels.public') }}" class="mob-nav-item mob-active">
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

<div class="page-wrap">

<!-- ═══ HERO RECHERCHE ═══ -->
<div class="search-hero">
  <div class="search-hero-title">{{ __('Hôtels') }} — Côte d'Ivoire</div>
  <div class="search-hero-sub">{{ __('Trouvez votre hébergement idéal parmi des milliers d\'hôtels et de propriétés à travers le monde.') }}</div>
  <form method="GET" action="{{ route('hotels.public') }}" class="search-form">
    <div class="sf">
      <div class="sf-label">{{ __('Destination') }}</div>
      <input class="sf-input" type="text" name="ville" placeholder="{{ __('Ville, quartier...') }}" value="{{ request('ville') }}" aria-label="{{ __('Destination') }}">
    </div>
    <div class="sf">
      <div class="sf-label">{{ __('Arrivée') }}</div>
      <input class="sf-input" type="date" name="checkin" value="{{ request('checkin') }}" aria-label="{{ __('Date d\'arrivée') }}">
    </div>
    <div class="sf">
      <div class="sf-label">{{ __('Départ') }}</div>
      <input class="sf-input" type="date" name="checkout" value="{{ request('checkout') }}" aria-label="{{ __('Date de départ') }}">
    </div>
    <div class="sf" style="max-width:150px">
      <div class="sf-label">{{ __('Étoiles min.') }}</div>
      <select class="sf-input" name="stars" aria-label="{{ __('Étoiles minimum') }}">
        <option value="">{{ __('Toutes') }}</option>
        @foreach([1,2,3,4,5] as $s)
          <option value="{{ $s }}" {{ request('stars') == $s ? 'selected' : '' }}>{{ $s }} ★</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="search-btn">
      <i class="fa-solid fa-magnifying-glass"></i> {{ __('Rechercher') }}
    </button>
  </form>
</div>

<!-- ═══ LAYOUT ═══ -->
<div class="layout">

  <!-- FILTRES -->
  <aside class="filters-panel">
    <div class="filter-card">
      <div class="filter-title">{{ __('Catégories') }}</div>
      <div class="filter-group">
        @foreach($categories as $cat)
        <a href="{{ route('hotels.public', array_merge(request()->query(), ['categorie' => $cat])) }}"
           class="filter-option {{ request('categorie') === $cat ? 'active' : '' }}">
          <i class="fa-solid fa-{{ match($cat) {
            'Luxe' => 'crown', 'Plage' => 'umbrella-beach', 'Affaires' => 'briefcase', default => 'hotel'
          } }}"></i>
          {{ $cat }}
        </a>
        @endforeach
        @if(request('categorie'))
        <a href="{{ route('hotels.public', array_diff_key(request()->query(), ['categorie' => ''])) }}"
           class="filter-option" style="color:#e63946">
          <i class="fa-solid fa-xmark"></i> {{ __('Effacer filtre') }}
        </a>
        @endif
      </div>
    </div>

    <div class="filter-card">
      <div class="filter-title">{{ __('Prix par nuit') }}</div>
      <form method="GET" action="{{ route('hotels.public') }}">
        @foreach(request()->except(['prix_max']) as $key => $val)
          <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach
        <input class="price-input" type="number" name="prix_max"
               placeholder="Max FCFA" value="{{ request('prix_max') }}" min="0" step="5000"
               aria-label="{{ __('Prix maximum par nuit') }}"
               style="width:100%;padding:9px 10px;border-radius:8px;border:1.5px solid var(--border);font-size:13px;font-family:'Plus Jakarta Sans',sans-serif">
        <button type="submit" class="apply-filter">{{ __('Appliquer') }}</button>
      </form>
    </div>

    <div class="filter-card">
      <div class="filter-title">{{ __('Villes') }}</div>
      <div class="filter-group" style="max-height:200px;overflow-y:auto">
        @foreach($cities as $city)
        <a href="{{ route('hotels.public', array_merge(request()->query(), ['ville' => $city])) }}"
           class="filter-option {{ request('ville') === $city ? 'active' : '' }}">
          <i class="fa-solid fa-location-dot"></i> {{ $city }}
        </a>
        @endforeach
      </div>
    </div>
  </aside>

  <!-- RÉSULTATS -->
  <main class="results">
    <div class="results-header">
      <p class="results-count">
        <strong>{{ $hotels->total() }}</strong> {{ __('hôtel(s) trouvé(s)') }}
        @if(request('ville')) {{ __('dans') }} <strong>{{ request('ville') }}</strong>@endif
      </p>
      <select class="sort-select" onchange="window.location.href=this.value">
        <option value="{{ route('hotels.public', array_merge(request()->query(), ['sort' => 'rating'])) }}"
          {{ request('sort','rating') === 'rating' ? 'selected' : '' }}>{{ __('Mieux notés') }}</option>
        <option value="{{ route('hotels.public', array_merge(request()->query(), ['sort' => 'price_asc'])) }}"
          {{ request('sort') === 'price_asc' ? 'selected' : '' }}>{{ __('Prix croissant') }}</option>
        <option value="{{ route('hotels.public', array_merge(request()->query(), ['sort' => 'price_desc'])) }}"
          {{ request('sort') === 'price_desc' ? 'selected' : '' }}>{{ __('Prix décroissant') }}</option>
      </select>
    </div>

    @if($hotels->isEmpty())
      <div class="empty-state">
        <div class="empty-icon"><i class="fa-solid fa-hotel"></i></div>
        <div class="empty-title">{{ __('Aucun hôtel trouvé') }}</div>
        <p class="empty-sub">{{ __("Essayez avec d'autres critères ou explorez toutes nos destinations.") }}</p>
        <a href="{{ route('hotels.public') }}" style="display:inline-block;margin-top:16px;padding:12px 24px;background:var(--coral);color:white;border-radius:10px;font-weight:600">
          {{ __('Voir tous les hôtels') }}
        </a>
      </div>
    @else
      <div class="cards-grid">
        @foreach($hotels as $hotel)
        @php
          $gradients = [
            'linear-gradient(135deg,#667eea,#764ba2)',
            'linear-gradient(135deg,#f7971e,#ffd200)',
            'linear-gradient(135deg,#11998e,#38ef7d)',
            'linear-gradient(135deg,#ee0979,#ff6a00)',
            'linear-gradient(135deg,#4776e6,#8e54e9)',
            'linear-gradient(135deg,#f953c6,#b91d73)',
            'linear-gradient(135deg,#0f9b58,#00bf8f)',
            'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)',
            'linear-gradient(135deg,#1c92d2,#f2fcfe)',
            'linear-gradient(135deg,#005c97,#363795)',
            'linear-gradient(135deg,#00b4db,#0083b0)',
            'linear-gradient(135deg,#56ab2f,#a8e063)',
          ];
          $grad = $gradients[$hotel->id % count($gradients)];
        @endphp
        <div class="hotel-card">
          <div class="card-img" style="{{ !$hotel->image ? 'background:'.$grad : '' }}">
            @if($hotel->image)
              <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" loading="lazy">
            @else
              <div class="card-img-placeholder">
                <i class="fa-solid fa-hotel"></i>
                <span>{{ $hotel->city }}</span>
              </div>
            @endif
            {{-- Lien couvrant l'image : tap mobile = Voir les détails (accessible sans connexion) --}}
            <a href="{{ route('hotels.details', $hotel) }}" class="card-img-link" aria-label="{{ $hotel->name }}"></a>
            <span class="card-badge {{ $hotel->stars >= 4 ? 'premium' : '' }}">{{ $hotel->category }}</span>
            @auth
              @if(auth()->user()->role === 'client')
                <button class="card-heart {{ $favoriteIds->contains($hotel->id) ? 'liked' : '' }}"
                        data-url="{{ route('hotels.favorite', $hotel) }}"
                        onclick="toggleFavPublic(event, this)"
                        title="{{ $favoriteIds->contains($hotel->id) ? __('Retirer des favoris') : __('Ajouter aux favoris') }}">
                  <i class="fa-{{ $favoriteIds->contains($hotel->id) ? 'solid' : 'regular' }} fa-heart"></i>
                </button>
              @endif
            @else
              <a href="{{ route('login') }}" class="card-heart" title="{{ __('Connectez-vous pour sauvegarder') }}">
                <i class="fa-regular fa-heart"></i>
              </a>
            @endauth
          </div>
          <div class="card-body">
            <div class="card-name">{{ $hotel->name }}</div>
            <div class="card-loc">
              <i class="fa-solid fa-location-dot"></i>
              {{ $hotel->quartier }}, {{ $hotel->city }}
            </div>
            <div class="card-stars">
              @for($i = 1; $i <= 5; $i++)
                <i class="fa-{{ $i <= $hotel->stars ? 'solid' : 'regular' }} fa-star"></i>
              @endfor
            </div>
            @if($hotel->services && count($hotel->services))
            <div class="card-tags">
              @foreach(array_slice($hotel->services, 0, 3) as $svc)
                <span class="card-tag">{{ $svc }}</span>
              @endforeach
            </div>
            @endif
            <div class="card-footer">
              <div class="card-price">
                {{ number_format($hotel->price, 0, ',', ' ') }} FCFA
                <small>{{ __('/ nuit') }}</small>
              </div>
              <div class="card-rating">
                <i class="fa-solid fa-star"></i> {{ number_format($hotel->rating, 1) }}
              </div>
            </div>
            <a href="{{ route('hotels.details', $hotel) }}" class="btn-book">
                <i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}
            </a>
          </div>
        </div>
        @endforeach
      </div>

    @endif
  </main>
</div>

<!-- ═══ PAGINATION ═══ -->
@if($hotels->hasPages())
<div style="max-width:1400px;margin:0 auto;padding:8px 56px 40px;display:flex;justify-content:flex-end;">
  {{ $hotels->links() }}
</div>
@endif

<script>
/* ── Mode clair / sombre ── */
const htmlEl         = document.documentElement;
const themeToggle    = document.getElementById('themeToggle');
const themeIcon      = document.getElementById('themeIcon');
const themeLabel     = document.getElementById('themeLabel');
const mobThemeToggle = document.getElementById('mobThemeToggle');
const mobThemeIcon   = document.getElementById('mobThemeIcon');
const mobThemeLabel  = document.getElementById('mobThemeLabel');
const mobToggleTrack = document.getElementById('mobToggleTrack');
const LABEL_DARK     = '{{ __("Mode sombre") }}';
const LABEL_LIGHT    = '{{ __("Mode clair") }}';

function applyTheme(theme) {
  htmlEl.setAttribute('data-theme', theme);
  localStorage.setItem('hotelio-pub-theme', theme);
  const isDark = theme === 'dark';
  if (themeIcon)      themeIcon.className           = isDark ? 'fa-solid fa-sun'  : 'fa-solid fa-moon';
  if (themeLabel)     themeLabel.textContent         = isDark ? LABEL_LIGHT        : LABEL_DARK;
  if (mobThemeIcon)   mobThemeIcon.className         = isDark ? 'fas fa-sun'       : 'fas fa-moon';
  if (mobThemeLabel)  mobThemeLabel.textContent      = isDark ? LABEL_LIGHT        : LABEL_DARK;
  if (mobToggleTrack) mobToggleTrack.classList.toggle('on', isDark);
}
(function(){ applyTheme(localStorage.getItem('hotelio-pub-theme') || 'light'); })();
themeToggle?.addEventListener('click',    () => applyTheme(htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));
mobThemeToggle?.addEventListener('click', () => applyTheme(htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));

/* ── Menu mobile ── */
const hamburger  = document.getElementById('hamburger');
const mobMenu    = document.getElementById('mobMenu');
const mobOverlay = document.getElementById('mobOverlay');
const mobClose   = document.getElementById('mobClose');
function openMenu(){mobMenu.classList.add('open');mobOverlay.classList.add('open');hamburger.classList.add('open');document.body.style.overflow='hidden'}
function closeMenu(){mobMenu.classList.remove('open');mobOverlay.classList.remove('open');hamburger.classList.remove('open');document.body.style.overflow=''}
hamburger?.addEventListener('click', () => mobMenu.classList.contains('open') ? closeMenu() : openMenu());
mobClose?.addEventListener('click', closeMenu);
mobOverlay?.addEventListener('click', closeMenu);
let tx = 0, tyStart = 0;
mobMenu?.addEventListener('touchstart', e => { tx = e.touches[0].clientX; tyStart = e.touches[0].clientY; }, {passive:true});
mobMenu?.addEventListener('touchend',   e => { if(e.changedTouches[0].clientX - tx > 60) closeMenu(); });

/* ── Dropdown langue ── */
const langNavBtn      = document.getElementById('langNavBtn');
const langNavDropdown = document.getElementById('langNavDropdown');
const langNavWrap     = document.getElementById('langNavWrap');
langNavBtn?.addEventListener('click', e => {
  e.stopPropagation();
  const open = langNavDropdown.classList.contains('open');
  langNavDropdown.classList.toggle('open', !open);
  langNavBtn.classList.toggle('open', !open);
});

/* ── Dropdown profil ── */
const profileNavBtn      = document.getElementById('profileNavBtn');
const profileNavDropdown = document.getElementById('profileNavDropdown');
const profileNavWrap     = document.getElementById('profileNavWrap');
profileNavBtn?.addEventListener('click', e => {
  e.stopPropagation();
  const open = profileNavDropdown.classList.contains('open');
  profileNavDropdown.classList.toggle('open', !open);
  profileNavBtn.classList.toggle('open', !open);
});

/* Fermer dropdowns en cliquant ailleurs */
document.addEventListener('click', e => {
  if (langNavWrap && !langNavWrap.contains(e.target)) {
    langNavDropdown?.classList.remove('open');
    langNavBtn?.classList.remove('open');
  }
  if (profileNavWrap && !profileNavWrap.contains(e.target)) {
    profileNavDropdown?.classList.remove('open');
    profileNavBtn?.classList.remove('open');
  }
});

/* ── Favoris (AJAX) ── */
function toggleFavPublic(e, btn) {
  e.preventDefault();
  e.stopPropagation();
  if (btn.disabled) return;
  btn.disabled = true;

  fetch(btn.dataset.url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  })
  .then(r => r.json())
  .then(data => {
    const icon = btn.querySelector('i');
    if (data.favorite) {
      btn.classList.add('liked');
      icon.className  = 'fa-solid fa-heart';
      btn.title       = '{{ __("Retirer des favoris") }}';
      showFavToast('{{ __("Ajouté aux favoris") }} ❤️', true);
    } else {
      btn.classList.remove('liked');
      icon.className  = 'fa-regular fa-heart';
      btn.title       = '{{ __("Ajouter aux favoris") }}';
      showFavToast('{{ __("Retiré des favoris") }}', false);
    }
  })
  .catch(() => {})
  .finally(() => { btn.disabled = false; });
}

function showFavToast(msg, success) {
  const t = document.createElement('div');
  t.textContent = msg;
  Object.assign(t.style, {
    position:'fixed', bottom:'24px', right:'24px', zIndex:'9999',
    padding:'12px 20px', borderRadius:'12px', fontSize:'14px', fontWeight:'600',
    background: success ? '#009A44' : '#64748b', color:'white',
    boxShadow:'0 8px 24px rgba(0,0,0,.2)',
    transition:'opacity .4s', opacity:'1',
  });
  document.body.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 400); }, 2500);
}
</script>

@include('partials.public-footer')

</div>{{-- .page-wrap --}}
@include('partials.cookie-banner')
</body>
</html>
