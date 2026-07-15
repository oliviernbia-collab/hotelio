<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Hôtelio — {{ __('Trouvez Votre Endroit de Rêve') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}

:root{
  --ocean:#0C2340;
  --ocean-deep:#061422;
  --sand:#F8F0E3;
  --sand-warm:#EFE3CC;
  --coral:orange;
  --coral-light:#F0876F;
  --gold:orange;
  --gold-pale:#F0D9A8;
  --white:#FFFFFF;
  --text:#1A1A1A;
  --text-2:#4A4A4A;
  --text-3:#7A7A7A;
  --border:#E8E0D4;
  --shadow:rgba(12,35,64,0.12);

  /* Variables manquantes dans l'original */
  --primary:#F77F00;
  --primary-light:#e06e00;
  --secondary:#009A44;
  --secondary-light:#007a35;
}

body::before{
  content:"";
  position:fixed;
  top:0;left:0;
  width:100%;height:4px;
  background:linear-gradient(to right,#F77F00 33%,#FFFFFF 33% 66%,#009E60 66%);
  z-index:999;
}
.footer-info{ background:#111; }
.fi-discover{ background:var(--primary); }
.fi-discover:hover{ background:var(--primary-light); }
.mob-nav a.active{ color:var(--primary); }
.mob-nav a:hover{ color:var(--secondary); }
.search-btn{ background:var(--primary); }
.search-btn:hover{ background:var(--primary-light); }
.hero-title span{ color:var(--primary); }
.section-title span{ color:var(--secondary); }
.nav-links a.active{ color:var(--primary); }

html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--white);color:var(--text);overflow-x:hidden}

/* ===== NAV ===== */
nav{
  position:fixed;top:0;left:0;right:0;z-index:200;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 56px;height:72px;
  background:rgba(255,255,255,0.97);
  border-bottom:1px solid rgba(232,224,212,0.6);
  backdrop-filter:blur(12px);
  animation:navIn 0.7s ease both;
  gap:1rem;
}
@keyframes navIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}

.nav-logo{
  display:flex;align-items:center;gap:8px;flex-shrink:0;
  font-family:'Fraunces',serif;font-size:24px;font-weight:400;
  color:var(--ocean);text-decoration:none;letter-spacing:-0.02em;
}
.logo-icon{
  width:36px;height:36px;background:var(--coral);
  border-radius:10px;display:flex;align-items:center;justify-content:center;
}
.logo-icon svg{width:20px;height:20px;fill:white}
.nav-logo-img{
  height:44px;width:44px;object-fit:contain;
  filter:drop-shadow(0 1px 4px rgba(0,0,0,.18));
}

.nav-links{display:flex;align-items:center;gap:6px;list-style:none}
.nav-links a{
  font-size:14px;font-weight:400;color:var(--text-2);
  text-decoration:none;padding:8px 14px;border-radius:8px;
  transition:all 0.2s;
}
.nav-links a:hover{color:var(--ocean);background:var(--sand)}
.nav-links a.active{color:var(--coral);font-weight:500}

.nav-right{display:flex;align-items:center;gap:10px}

/* ── Sélecteur de langue (desktop) ── */
.lang-nav-wrap{position:relative}
.lang-nav-btn{
  display:flex;align-items:center;gap:6px;
  font-size:13px;font-weight:400;color:var(--text-2);
  padding:7px 12px;border:1px solid var(--border);border-radius:8px;
  background:transparent;cursor:pointer;transition:all 0.2s;font-family:inherit;
}
.lang-nav-btn:hover,.lang-nav-btn.open{border-color:var(--ocean);color:var(--ocean)}
.lang-nav-btn .fa-chevron-down{font-size:10px;transition:transform .2s}
.lang-nav-btn.open .fa-chevron-down{transform:rotate(180deg)}
.lang-nav-dropdown{
  position:absolute;top:calc(100% + 8px);right:0;
  background:white;border:1px solid var(--border);border-radius:12px;
  box-shadow:0 8px 32px rgba(12,35,64,.12);
  z-index:300;overflow:hidden;display:none;flex-direction:column;
  min-width:155px;
}
.lang-nav-dropdown.open{display:flex}
.lang-nav-opt{
  display:flex;align-items:center;gap:8px;
  padding:10px 14px;font-size:13px;font-weight:500;
  color:var(--text);text-decoration:none;transition:background .15s;
}
.lang-nav-opt:hover{background:var(--sand)}
.lang-nav-opt.lang-cur{color:var(--primary);font-weight:700}

/* ── Bouton Se connecter ── */
.btn-login{
  display:inline-flex;align-items:center;justify-content:center;gap:8px;
  padding:10px 22px;border-radius:10px;
  background:#e63946;color:#ffffff;
  font-size:14px;font-weight:600;text-decoration:none;
  border:1.5px solid #e63946;transition:all 0.25s ease;
  box-shadow:0 6px 18px rgba(230,57,70,0.25);
}
.btn-login:hover{
  background:#ff4d5a;border-color:#ff4d5a;
  transform:translateY(-2px);box-shadow:0 10px 24px rgba(230,57,70,0.35);
}
.btn-login:active{transform:scale(0.97)}

/* ── Bouton S'inscrire / Réserver ── */
.btn-reserve{
  font-size:13px;font-weight:500;color:white;
  padding:9px 20px;border:1.5px solid var(--coral);border-radius:10px;
  background:var(--coral);cursor:pointer;transition:all 0.2s;text-decoration:none;
}
.btn-reserve:hover{
  background:var(--coral-light);border-color:var(--coral-light);
  transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,99,74,0.3);
}

/* ── Profil connecté (desktop) ── */
.profile-nav-wrap{position:relative}
.profile-nav-btn{
  display:flex;align-items:center;gap:8px;
  padding:6px 12px 6px 6px;border:1.5px solid var(--border);border-radius:40px;
  background:transparent;cursor:pointer;transition:border-color .2s;font-family:inherit;
}
.profile-nav-btn:hover,.profile-nav-btn.open{border-color:var(--primary)}
.profile-nav-avatar{
  width:32px;height:32px;border-radius:50%;
  background:var(--primary);color:white;
  display:flex;align-items:center;justify-content:center;
  font-size:.82rem;font-weight:800;text-transform:uppercase;flex-shrink:0;
}
.profile-nav-name{font-size:13px;font-weight:600;color:var(--ocean);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.profile-nav-btn .fa-chevron-down{font-size:10px;color:var(--text-3);transition:transform .2s}
.profile-nav-btn.open .fa-chevron-down{transform:rotate(180deg)}
.profile-nav-dropdown{
  position:absolute;top:calc(100% + 10px);right:0;
  background:white;border:1px solid var(--border);border-radius:14px;
  box-shadow:0 12px 40px rgba(12,35,64,.14);
  z-index:300;overflow:hidden;display:none;flex-direction:column;min-width:195px;
}
.profile-nav-dropdown.open{display:flex}
.profile-nav-opt{
  display:flex;align-items:center;gap:10px;
  padding:11px 16px;font-size:14px;font-weight:500;
  color:var(--text);text-decoration:none;transition:background .15s;
  border:none;background:transparent;cursor:pointer;width:100%;font-family:inherit;text-align:left;
}
.profile-nav-opt:hover{background:var(--sand)}
.profile-nav-opt i{width:16px;color:var(--text-3)}
.profile-nav-sep{height:1px;background:var(--border);margin:4px 0}
.profile-nav-opt.logout{color:#e63946}.profile-nav-opt.logout i{color:#e63946}
.profile-nav-opt.logout:hover{background:#fff0f0}

/* ===== HERO (SPLIT) ===== */
.hero{
  display:flex;min-height:100vh;overflow:hidden;position:relative;
}

/* ── Gauche : texte + formulaire ── */
.hero-left{
  width:48%;min-width:380px;
  display:flex;flex-direction:column;justify-content:center;
  padding:100px 52px 60px;
  background:linear-gradient(160deg,#fdf8f1 0%,#f0e8d8 50%,#e8dece 100%);
  position:relative;z-index:2;
  overflow-y:auto;
}
.hero-badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(255,255,255,.85);border:1px solid rgba(201,145,61,.3);
  border-radius:50px;padding:6px 16px 6px 8px;
  font-size:12px;font-weight:500;color:var(--gold);
  margin-bottom:24px;animation:fadeUp .8s .2s ease both;backdrop-filter:blur(8px);
  width:fit-content;
}
.badge-dot{width:24px;height:24px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.badge-dot svg{width:12px;height:12px;fill:white}
.hero-title{
  font-family:'Fraunces',serif;font-size:clamp(36px,4.5vw,62px);
  font-weight:300;line-height:1.08;letter-spacing:-.03em;
  color:var(--ocean);margin-bottom:10px;animation:fadeUp .9s .35s ease both;
}
.hero-title span{color:var(--coral);font-style:italic}
.hero-sub{
  font-size:16px;font-weight:300;line-height:1.6;
  color:var(--text-2);margin-bottom:28px;
  animation:fadeUp .9s .5s ease both;
}

/* Formulaire de recherche vertical */
.search-v{
  background:white;border-radius:20px;
  box-shadow:0 12px 48px rgba(12,35,64,.12),0 2px 8px rgba(12,35,64,.06);
  padding:20px;display:flex;flex-direction:column;gap:10px;
  border:1px solid rgba(232,224,212,.8);
  animation:fadeUp 1s .65s ease both;
}
.sv-row{display:flex;gap:10px}
.sv-field{display:flex;flex-direction:column;gap:4px;flex:1}
.sv-label{
  font-size:10px;font-weight:700;text-transform:uppercase;
  letter-spacing:.1em;color:var(--text-3);
}
.sv-input{
  padding:11px 13px;border:1.5px solid var(--border);border-radius:10px;
  font-size:13px;color:var(--text);font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--sand);transition:border-color .2s;
}
.sv-input:focus{outline:none;border-color:var(--coral)}
.sv-input::placeholder{color:var(--text-3)}
.sv-btn{
  padding:13px;background:var(--coral);color:white;border:none;border-radius:12px;
  font-size:14px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:all .25s;margin-top:4px;
}
.sv-btn:hover{background:var(--primary-light);transform:translateY(-1px);box-shadow:0 8px 24px rgba(247,127,0,.3)}
.sv-btn svg{width:16px;height:16px;fill:none;stroke:white;stroke-width:2.5}

/* ── Droite : slideshow ── */
.hero-right{
  flex:1;position:relative;overflow:hidden;
}
.hero-slide{
  position:absolute;inset:0;
  background-size:cover;background-position:center;
  opacity:0;transition:opacity 1.2s ease;
}
.hero-slide.hs-active{opacity:1}

/* Dégradé de fondu à gauche de l'image */
.hero-right::after{
  content:'';position:absolute;left:0;top:0;bottom:0;width:80px;z-index:1;
  background:linear-gradient(to right,#efe3cc,transparent);
  pointer-events:none;
}

/* Indicateurs dots */
.hero-dots{
  position:absolute;bottom:20px;left:50%;transform:translateX(-50%);
  display:flex;gap:7px;z-index:10;
}
.hero-dot{
  width:8px;height:8px;border-radius:50%;border:none;
  background:rgba(255,255,255,.5);cursor:pointer;transition:background .3s,transform .3s;
  padding:0;
}
.hero-dot.active{background:white;transform:scale(1.4)}

/* ===== CATEGORIES ===== */
.categories{position:relative;z-index:1;padding:28px 56px 0;background:white;border-bottom:1px solid var(--border)}
.cat-scroll{display:flex;gap:6px;overflow-x:auto;padding-bottom:20px;scrollbar-width:none;-ms-overflow-style:none}
.cat-scroll::-webkit-scrollbar{display:none}
.cat-item{display:flex;flex-direction:column;align-items:center;gap:7px;padding:10px 20px;border-radius:12px;cursor:pointer;transition:all 0.2s;white-space:nowrap;flex-shrink:0;border-bottom:2px solid transparent}
.cat-item:hover{background:var(--sand)}.cat-item.active{border-bottom-color:var(--ocean);color:var(--ocean)}
.cat-icon{width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:22px}
.cat-label{font-size:12px;font-weight:500;color:var(--text-2)}.cat-item.active .cat-label{color:var(--ocean);font-weight:600}
.filter-btn{
  margin-left:auto;flex-shrink:0;align-self:center;margin-bottom:20px;
  display:flex;align-items:center;gap:7px;
  padding:9px 18px;border:1.5px solid var(--border);border-radius:10px;
  background:white;font-size:13px;font-weight:500;color:var(--text-2);cursor:pointer;transition:all 0.2s;
}
.filter-btn:hover{border-color:var(--ocean);color:var(--ocean)}
.filter-btn svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8}

/* ===== SECTIONS ===== */
.section{padding:64px 56px}.section:nth-child(even){background:var(--sand)}
.section-hdr{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px}
.section-title{font-family:'Fraunces',serif;font-size:clamp(22px,2.5vw,30px);font-weight:400;color:var(--ocean);letter-spacing:-0.02em;line-height:1.2}
.section-title span{color:var(--coral)}
.see-all{font-size:13px;font-weight:500;color:var(--coral);text-decoration:none;display:flex;align-items:center;gap:4px;transition:gap 0.2s}
.see-all:hover{gap:8px}.see-all svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2}

/* Cards grid */
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
.card{border-radius:16px;overflow:hidden;cursor:pointer;transition:transform 0.3s,box-shadow 0.3s;background:white;box-shadow:0 2px 12px rgba(12,35,64,0.06);position:relative}
.card:hover{transform:translateY(-6px);box-shadow:0 20px 48px rgba(12,35,64,0.14)}
.card-img{position:relative;height:200px;overflow:hidden}
.card-img-inner{width:100%;height:100%;transition:transform 0.5s ease}.card:hover .card-img-inner{transform:scale(1.06)}

/* Lien transparent sur l'image = tap mobile → Voir & Réserver */
.card-img-link{position:absolute;inset:0;z-index:2;display:block}

.card-badge{position:absolute;top:12px;left:12px;z-index:3;background:rgba(255,255,255,0.95);border-radius:6px;padding:4px 10px;font-size:11px;font-weight:600;color:var(--ocean);backdrop-filter:blur(4px)}
.card-heart{position:absolute;top:12px;right:12px;z-index:3;width:32px;height:32px;background:rgba(255,255,255,0.9);border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background 0.2s;cursor:pointer}
.card-heart:hover{background:white}.card-heart svg{width:16px;height:16px;fill:none;stroke:#777;stroke-width:2;transition:stroke 0.2s}.card-heart:hover svg{stroke:var(--coral)}
.card-body{padding:16px}
.card-name{font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px}
.card-loc{font-size:12px;color:var(--text-3);margin-bottom:10px;display:flex;align-items:center;gap:4px}.card-loc svg{width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2}
.card-foot{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.card-price{font-size:14px;font-weight:600;color:var(--ocean)}.card-price span{font-size:12px;font-weight:400;color:var(--text-3)}
.card-rating{display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:var(--text)}.card-rating svg{width:13px;height:13px;fill:var(--gold);stroke:none}

/* Bouton Voir & Réserver */
.btn-voir-reserver{
  display:flex;align-items:center;justify-content:center;gap:6px;
  padding:10px 14px;background:var(--primary);color:white;
  border-radius:10px;font-size:13px;font-weight:700;
  text-decoration:none;transition:background .2s,transform .15s;
  font-family:'Plus Jakarta Sans',sans-serif;
}
.btn-voir-reserver:hover{background:var(--primary-light);transform:translateY(-1px)}

/* Placeholders */
.ph-apt1{background:linear-gradient(135deg,#667eea,#764ba2)}.ph-villa1{background:linear-gradient(135deg,#f7971e,#ffd200)}
.ph-suite{background:linear-gradient(135deg,#11998e,#38ef7d)}.ph-studio{background:linear-gradient(135deg,#ee0979,#ff6a00)}
.ph-apt2{background:linear-gradient(135deg,#4776e6,#8e54e9)}.ph-villa2{background:linear-gradient(135deg,#f953c6,#b91d73)}
.ph-res{background:linear-gradient(135deg,#0f9b58,#00bf8f)}.ph-pent{background:linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)}
.ph-maison1{background:linear-gradient(135deg,#1c92d2,#f2fcfe)}.ph-balnaire{background:linear-gradient(135deg,#005c97,#363795)}
.ph-mer{background:linear-gradient(135deg,#00b4db,#0083b0)}.ph-villa3{background:linear-gradient(135deg,#56ab2f,#a8e063)}
.card-img-inner::after{content:'';position:absolute;inset:0;background:linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.18) 100%)}
.card-scene{position:relative;width:100%;height:100%;display:flex;align-items:flex-end;justify-content:center;padding:0 16px}
.building{position:absolute;bottom:0;background:rgba(255,255,255,0.12);border-radius:4px 4px 0 0}

/* Image réelle dans les cartes */
.card-img img.card-real-img{
  width:100%;height:100%;object-fit:cover;display:block;
  transition:transform .5s ease;
}
.card:hover .card-img img.card-real-img{transform:scale(1.06)}

/* ===== FOOTER ===== */
.footer-info{background:var(--ocean-deep);color:rgba(255,255,255,0.85);padding:64px 56px 40px}
.fi-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;margin-bottom:56px}
.fi-brand{font-family:'Fraunces',serif;font-size:28px;font-weight:300;color:white;display:flex;align-items:center;gap:10px;margin-bottom:16px}
.fi-brand .logo-icon{background:var(--coral)}
.fi-desc{font-size:14px;font-weight:300;line-height:1.7;color:rgba(255,255,255,0.55);margin-bottom:24px;max-width:280px}
.fi-discover{display:inline-flex;align-items:center;gap:8px;background:var(--coral);color:white;padding:11px 22px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.25s}
.fi-discover:hover{background:var(--coral-light);transform:translateY(-2px)}
.fi-col-title{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;color:var(--gold);margin-bottom:20px}
.fi-links{list-style:none;display:flex;flex-direction:column;gap:12px}
.fi-links a{font-size:14px;color:rgba(255,255,255,0.5);text-decoration:none;transition:color 0.2s}.fi-links a:hover{color:white}
.fi-bottom{display:flex;justify-content:space-between;align-items:center;padding-top:24px;border-top:1px solid rgba(255,255,255,0.08);font-size:12px;color:rgba(255,255,255,0.3);flex-wrap:wrap;gap:12px}
.fi-lang-links{display:flex;gap:12px}
.fi-lang-links a{font-size:12px;color:rgba(255,255,255,0.35);text-decoration:none;transition:color .2s;padding:3px 8px;border-radius:5px}
.fi-lang-links a:hover{color:rgba(255,255,255,.7)}.fi-lang-links a.lang-cur{color:var(--primary);font-weight:600}

@keyframes fadeUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}

/* ===== HAMBURGER ===== */
.hamburger{display:none;flex-direction:column;justify-content:center;align-items:center;width:42px;height:42px;border:1.5px solid var(--border);border-radius:10px;background:transparent;cursor:pointer;gap:5px;transition:all 0.2s;flex-shrink:0}
.hamburger:hover{border-color:var(--ocean)}
.ham-line{display:block;width:18px;height:1.8px;background:var(--ocean);border-radius:2px;transition:transform 0.35s cubic-bezier(.77,0,.18,1),opacity 0.25s,width 0.25s;transform-origin:center}
[data-theme="dark"] .hamburger{border-color:rgba(255,255,255,.35);background:rgba(255,255,255,.08)}
[data-theme="dark"] .hamburger:hover{border-color:rgba(255,255,255,.65);background:rgba(255,255,255,.15)}
[data-theme="dark"] .ham-line{background:#ffffff}
.hamburger.open .ham-line:nth-child(1){transform:translateY(6.8px) rotate(45deg)}
.hamburger.open .ham-line:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open .ham-line:nth-child(3){transform:translateY(-6.8px) rotate(-45deg)}

/* ===== MOBILE OVERLAY ===== */
.mob-overlay{position:fixed;inset:0;z-index:190;background:rgba(6,20,34,0.55);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:opacity 0.35s ease}
.mob-overlay.open{opacity:1;pointer-events:all}

/* ══════════════════════════════════════════
   MOBILE MENU PRO
   ══════════════════════════════════════════ */
.mob-menu{
  position:fixed;top:0;right:0;bottom:0;z-index:195;
  width:min(360px,93vw);
  background:white;
  transform:translateX(110%);
  transition:transform .42s cubic-bezier(.77,0,.18,1);
  display:flex;flex-direction:column;overflow:hidden;
  box-shadow:-8px 0 48px rgba(12,35,64,.18);
}
.mob-menu.open{transform:translateX(0)}

/* CI stripe */
.mob-ci-stripe{height:4px;flex-shrink:0;background:linear-gradient(to right,#F77F00 33%,#FFFFFF 33% 66%,#009E60 66%)}

/* Header */
.mob-header{display:flex;align-items:center;justify-content:space-between;padding:13px 16px;border-bottom:1px solid var(--border);flex-shrink:0}
.mob-brand{display:flex;align-items:center;gap:10px;text-decoration:none}
.mob-brand-icon{width:38px;height:38px;background:var(--coral);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mob-brand-icon svg{width:20px;height:20px;fill:white}
.mob-brand-name{font-family:'Fraunces',serif;font-size:19px;font-weight:400;color:var(--ocean);letter-spacing:-.02em;line-height:1}
.mob-brand-tag{font-size:10px;color:var(--text-3);font-weight:500;margin-top:2px}
.mob-close-btn{width:36px;height:36px;border-radius:10px;border:1.5px solid var(--border);background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;font-size:14px;color:var(--text-2)}
.mob-close-btn:hover{border-color:var(--coral);color:var(--coral);background:rgba(247,127,0,.06)}

/* Profile card (connecté) */
.mob-profile-card{margin:14px 14px 0;padding:14px;border-radius:16px;background:linear-gradient(135deg,rgba(247,127,0,.1),rgba(247,127,0,.04));border:1px solid rgba(247,127,0,.2);display:flex;align-items:center;gap:12px;flex-shrink:0}
.mob-profile-ava{width:46px;height:46px;border-radius:50%;background:var(--coral);color:white;display:flex;align-items:center;justify-content:center;font-size:1.15rem;font-weight:800;text-transform:uppercase;flex-shrink:0;border:3px solid rgba(255,255,255,.8)}
.mob-profile-info{flex:1;min-width:0}
.mob-profile-name{font-weight:700;font-size:.88rem;color:var(--ocean);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mob-profile-email{font-size:.69rem;color:var(--text-3);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mob-role-badge{display:inline-flex;align-items:center;gap:3px;font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--coral);background:rgba(247,127,0,.12);padding:2px 8px;border-radius:20px;margin-top:4px}

/* Auth section (non connecté) */
.mob-auth-section{margin:14px 14px 0;flex-shrink:0}
.mob-welcome-text{font-size:.78rem;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px}
.mob-auth-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.mob-btn-connexion{display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border:1.5px solid var(--ocean);border-radius:12px;font-size:13px;font-weight:600;color:var(--ocean);text-decoration:none;transition:all .2s}
.mob-btn-connexion:hover{background:var(--ocean);color:white}
.mob-btn-inscription{display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border:1.5px solid var(--coral);border-radius:12px;background:var(--coral);font-size:13px;font-weight:600;color:white;text-decoration:none;transition:all .2s}
.mob-btn-inscription:hover{background:var(--coral-light)}

/* Zone scrollable */
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

/* Paramètre : setting row */
.mob-setting-item{display:flex;align-items:center;gap:12px;padding:10px 8px;border-radius:12px}
.mob-setting-info{flex:1;min-width:0}
.mob-setting-label{font-size:14px;font-weight:500;color:var(--text)}
.mob-setting-sub{font-size:11px;color:var(--text-3);margin-top:1px}

/* Toggle switch thème */
.mob-theme-toggle{border:none;background:none;cursor:pointer;padding:4px;flex-shrink:0}
.mob-toggle-track{display:block;width:46px;height:26px;border-radius:13px;background:#e2e8f0;position:relative;transition:background .3s}
.mob-toggle-track.on{background:var(--coral)}
.mob-toggle-thumb{display:block;width:20px;height:20px;border-radius:50%;background:white;position:absolute;top:3px;left:3px;transition:transform .3s;box-shadow:0 1px 4px rgba(0,0,0,.2)}
.mob-toggle-track.on .mob-toggle-thumb{transform:translateX(20px)}

/* Drapeaux langue */
.mob-lang-flags{display:flex;gap:6px;flex-shrink:0}
.mob-flag{font-size:22px;text-decoration:none;padding:2px;border-radius:6px;transition:transform .2s;opacity:.45;line-height:1}
.mob-flag:hover{opacity:.85;transform:scale(1.12)}
.mob-flag-cur{opacity:1;transform:scale(1.12)}

/* Footer drawer */
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

/* Animations d'entrée */
.mob-menu.open .mob-nav-item,.mob-menu.open .mob-setting-item{animation:mobItemIn .35s ease both}
.mob-menu.open .mob-nav-item:nth-child(1),.mob-menu.open .mob-setting-item:nth-child(1){animation-delay:.04s}
.mob-menu.open .mob-nav-item:nth-child(2),.mob-menu.open .mob-setting-item:nth-child(2){animation-delay:.08s}
.mob-menu.open .mob-nav-item:nth-child(3),.mob-menu.open .mob-setting-item:nth-child(3){animation-delay:.12s}
.mob-menu.open .mob-nav-item:nth-child(4),.mob-menu.open .mob-setting-item:nth-child(4){animation-delay:.16s}
.mob-menu.open .mob-nav-item:nth-child(5){animation-delay:.20s}
.mob-menu.open .mob-nav-item:nth-child(6){animation-delay:.24s}
@keyframes mobItemIn{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:translateX(0)}}

@media(max-width:1100px){.cards{grid-template-columns:repeat(2,1fr)}}

/* Hero responsive */
@media(max-width:900px){
  .hero{flex-direction:column-reverse;min-height:100vh}
  .hero-left{width:100%;min-width:unset;padding:40px 24px 48px;justify-content:flex-start}
  .hero-right{height:45vh;min-height:260px;width:100%;flex:none}
  .hero-right::after{display:none}
  .hero-dots{bottom:12px}
  .sv-row{flex-direction:column;gap:8px}
}
@media(max-width:768px){
  nav{padding:0 16px}.nav-links,.nav-right{display:none}.hamburger{display:flex}
  .section{padding:40px 20px}
  .categories{padding:20px 20px 0}
  .cards{grid-template-columns:1fr 1fr;gap:16px}
  .fi-grid{grid-template-columns:1fr 1fr;gap:32px}.footer-info{padding:40px 20px 28px}
}
@media(min-width:769px){.hamburger{display:none}.mob-menu{display:none}.mob-overlay{display:none}}
@media(max-width:480px){
  .cards{grid-template-columns:1fr}
  .hero-left{padding:32px 16px 40px}
}

/* ── Catégorie lien ── */
.cat-item{text-decoration:none;color:inherit}

/* ══ MODAL FILTRES ══ */
.filters-overlay{
  position:fixed;inset:0;z-index:400;
  background:rgba(6,20,34,.55);backdrop-filter:blur(4px);
  opacity:0;pointer-events:none;transition:opacity .3s;
}
.filters-overlay.open{opacity:1;pointer-events:all}
.filters-panel{
  position:fixed;bottom:0;left:0;right:0;z-index:401;
  background:white;border-radius:20px 20px 0 0;
  padding:24px 24px 32px;
  transform:translateY(100%);transition:transform .4s cubic-bezier(.77,0,.18,1);
  max-height:80vh;overflow-y:auto;
  box-shadow:0 -12px 48px rgba(12,35,64,.18);
}
.filters-panel.open{transform:translateY(0)}
.filters-panel-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.filters-panel-title{font-family:'Fraunces',serif;font-size:20px;font-weight:400;color:var(--ocean)}
.filters-panel-close{
  width:36px;height:36px;border-radius:10px;
  border:1.5px solid var(--border);background:transparent;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:14px;color:var(--text-2);transition:all .2s;
}
.filters-panel-close:hover{border-color:var(--coral);color:var(--coral)}
.filters-group{margin-bottom:16px}
.filters-group label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);margin-bottom:6px}
.filters-group select,.filters-group input{
  width:100%;padding:11px 13px;border:1.5px solid var(--border);border-radius:10px;
  font-size:13px;color:var(--text);font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--sand);transition:border-color .2s;
}
.filters-group select:focus,.filters-group input:focus{outline:none;border-color:var(--coral)}
[data-theme="dark"] .filters-panel{background:#1e293b}
[data-theme="dark"] .filters-panel-title{color:#e2e8f0}
[data-theme="dark"] .filters-panel-close{border-color:rgba(148,163,184,.2);color:#94a3b8}
[data-theme="dark"] .filters-group label{color:#64748b}
[data-theme="dark"] .filters-group select,[data-theme="dark"] .filters-group input{background:rgba(148,163,184,.07);border-color:rgba(148,163,184,.15);color:#e2e8f0}

/* ══ BOUTON THÈME ══ */
.theme-btn{
  display:flex;align-items:center;gap:6px;
  font-size:13px;font-weight:500;color:var(--text-2);
  padding:7px 12px;border:1px solid var(--border);border-radius:8px;
  background:transparent;cursor:pointer;transition:all .2s;font-family:inherit;
}
.theme-btn:hover{border-color:var(--ocean);color:var(--ocean)}
.theme-btn i{font-size:13px}

/* ══ MODE SOMBRE ══ */
[data-theme="dark"] body{background:#0f172a;color:#e2e8f0}
[data-theme="dark"] nav{background:rgba(15,23,42,.97);border-color:rgba(148,163,184,.1)}
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
[data-theme="dark"] .hero-left{background:linear-gradient(160deg,#0f172a 0%,#1a2740 50%,#0c1220 100%)}
[data-theme="dark"] .hero-badge{background:rgba(30,41,59,.9);border-color:rgba(201,145,61,.25)}
[data-theme="dark"] .hero-title{color:#e2e8f0}
[data-theme="dark"] .hero-sub{color:#94a3b8}
[data-theme="dark"] .search-v{background:#1e293b;border-color:rgba(148,163,184,.12);box-shadow:0 12px 48px rgba(0,0,0,.35)}
[data-theme="dark"] .sv-label{color:#64748b}
[data-theme="dark"] .sv-input{background:rgba(148,163,184,.07);border-color:rgba(148,163,184,.15);color:#e2e8f0}
[data-theme="dark"] .sv-input::placeholder{color:#475569}
[data-theme="dark"] .hero-right::after{background:linear-gradient(to right,#1a2740,transparent)}
[data-theme="dark"] .search-box{background:#1e293b;border-color:rgba(148,163,184,.12);box-shadow:0 20px 60px rgba(0,0,0,.4)}
[data-theme="dark"] .search-field{border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .sf-label{color:#64748b}
[data-theme="dark"] .sf-input{color:#e2e8f0}
[data-theme="dark"] .sf-input::placeholder{color:#475569}
[data-theme="dark"] .categories{background:#1e293b;border-color:rgba(148,163,184,.1)}
[data-theme="dark"] .cat-label{color:#64748b}
[data-theme="dark"] .cat-item:hover{background:rgba(148,163,184,.06)}
[data-theme="dark"] .cat-item.active{border-color:#94a3b8}
[data-theme="dark"] .cat-item.active .cat-label{color:#e2e8f0}
[data-theme="dark"] .cat-icon i{color:#64748b}
[data-theme="dark"] .cat-item.active .cat-icon i{color:#e2e8f0}
[data-theme="dark"] .filter-btn{background:transparent;border-color:rgba(148,163,184,.2);color:#94a3b8}
[data-theme="dark"] .section{background:#0f172a}
[data-theme="dark"] .section:nth-child(even){background:#0d1b2e}
[data-theme="dark"] .section-title{color:#e2e8f0}
[data-theme="dark"] .card{background:#1e293b;box-shadow:0 2px 12px rgba(0,0,0,.35)}
[data-theme="dark"] .card-name{color:#e2e8f0}
[data-theme="dark"] .card-loc{color:#64748b}
[data-theme="dark"] .card-price{color:#e2e8f0}
[data-theme="dark"] .card-price span{color:#64748b}
[data-theme="dark"] .card-rating{color:#e2e8f0}
/* ── Dark mode menu mobile ── */
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
[data-theme="dark"] .footer-info{background:#020617}
</style>
</head>
<body>

<!-- ===== NAV ===== -->
<nav>
  <a href="#" class="nav-logo">
    <img src="{{ asset('images/logo.png') }}" alt="Hôtelio" class="nav-logo-img">
    Hôtelio
  </a>

  <ul class="nav-links">
    <li><a href="#" class="active">{{ __('Accueil') }}</a></li>
    <li><a href="{{ route('hotels.public') }}">{{ __('Hôtels') }}</a></li>
  </ul>

  <div class="nav-right">

    {{-- ── Bouton Mode clair/sombre ── --}}
    <button class="theme-btn" id="themeToggle">
      <i class="fa-solid fa-moon" id="themeIcon"></i>
      <span id="themeLabel">{{ __('Mode sombre') }}</span>
    </button>

    {{-- ── Sélecteur de langue ── --}}
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

    {{-- ── Auth ── --}}
    @auth
      <div class="profile-nav-wrap" id="profileNavWrap">
        <button class="profile-nav-btn" id="profileNavBtn">
          <div class="profile-nav-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
          <span class="profile-nav-name">{{ auth()->user()->name }}</span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>
        <div class="profile-nav-dropdown" id="profileNavDropdown">
          @if(auth()->user()->role==='admin')
            <a href="{{ route('admin.index') }}" class="profile-nav-opt"><i class="fa-solid fa-crown"></i> Panel Admin</a>
          @elseif(auth()->user()->role==='prestataire')
            <a href="{{ route('prestataire.index') }}" class="profile-nav-opt"><i class="fa-solid fa-gauge-high"></i> Mon Dashboard</a>
          @else
            <a href="{{ route('client.index') }}" class="profile-nav-opt"><i class="fa-solid fa-gauge-high"></i> Mon Dashboard</a>
          @endif
          <a href="{{ route('profile.index') }}" class="profile-nav-opt"><i class="fa-solid fa-id-card"></i> Mon Profil</a>
          <div class="profile-nav-sep"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="profile-nav-opt logout"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</button>
          </form>
        </div>
      </div>
    @else
      <a href="{{ route('login') }}" class="btn-login">Se connecter</a>
      <a href="{{ route('register') }}" class="btn-reserve">S'inscrire</a>
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

  {{-- Bande drapeau CI --}}
  <div class="mob-ci-stripe"></div>

  {{-- ── Header : logo + fermer ── --}}
  <div class="mob-header">
    <a href="{{ url('/') }}" class="mob-brand">
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

  {{-- ── Section auth ── --}}
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

  {{-- ── Zone scrollable ── --}}
  <div class="mob-scroll">

    {{-- Navigation principale --}}
    <div class="mob-section-label">{{ __('Navigation') }}</div>
    <div class="mob-nav-list">
      <a href="{{ url('/') }}" class="mob-nav-item mob-active">
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

    {{-- Hébergement --}}
    <div class="mob-hebergement">
    <div class="mob-section-label">{{ __('Hébergement') }}</div>
    <div class="mob-nav-list">
    </div>
    </div>{{-- fin .mob-hebergement --}}

    {{-- Mon compte (si connecté) --}}
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

    {{-- Paramètres : thème + langue --}}
    <div class="mob-section-label">{{ __('Paramètres') }}</div>

    {{-- Toggle mode sombre --}}
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

    {{-- Langue --}}
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

  </div>{{-- end mob-scroll --}}

  {{-- ── Footer drawer ── --}}
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

<!-- ===== HERO ===== -->
<section class="hero">

  <!-- ── GAUCHE : texte + recherche ── -->
  <div class="hero-left">
    <div class="hero-badge">
      <span class="badge-dot"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
      {{ __("La plateforme N°1 des hébergements en Côte d'Ivoire") }}
    </div>

    <h1 class="hero-title">
      {{ __('Trouvez Votre') }}<br>
      <span>{{ __('Endroit de Rêve') }}</span>
    </h1>
    <p class="hero-sub">{{ __('Pour une Meilleure Expérience') }}</p>

    <!-- Formulaire de recherche vertical -->
    <form class="search-v" method="GET" action="{{ route('hotels.public') }}">
      <div class="sv-field">
        <div class="sv-label">{{ __('Où') }}</div>
        <input class="sv-input" type="text" name="ville" placeholder="{{ __('Destination, ville...') }}">
      </div>
      <div class="sv-row">
        <div class="sv-field">
          <div class="sv-label">{{ __('Arrivée') }}</div>
          <input class="sv-input" type="date" name="checkin" value="{{ date('Y-m-d') }}">
        </div>
        <div class="sv-field">
          <div class="sv-label">{{ __('Départ') }}</div>
          <input class="sv-input" type="date" name="checkout" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
        </div>
      </div>
      <div class="sv-field">
        <div class="sv-label">{{ __('Voyageurs') }}</div>
        <input class="sv-input" type="number" name="voyageurs" min="1" max="20" placeholder="{{ __('Ajouter des voyageurs') }}">
      </div>
      <button type="submit" class="sv-btn">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        {{ __('Rechercher') }}
      </button>
    </form>
  </div>

  <!-- ── DROITE : slideshow photos ── -->
  <div class="hero-right" id="heroSlideshow">
    <div class="hero-slide hs-active" style="background-image:url('{{ asset('images/h0.jpg') }}')"></div>
    <div class="hero-slide"           style="background-image:url('{{ asset('images/h10.jpg') }}')"></div>
    <div class="hero-slide"           style="background-image:url('{{ asset('images/h11.jpg') }}')"></div>
    <div class="hero-slide"           style="background-image:url('{{ asset('images/h13.jpg') }}')"></div>
    <div class="hero-dots">
      <button class="hero-dot active"></button>
      <button class="hero-dot"></button>
      <button class="hero-dot"></button>
      <button class="hero-dot"></button>
    </div>
  </div>

</section>

<!-- ===== CATEGORIES ===== -->
<div class="categories">
  <div class="cat-scroll">
    @php $cats=[
      ['icon'=>'fa-globe',          'label'=>'Tous',           'active'=>true, 'href'=>route('hotels.public')],
      ['icon'=>'fa-mountain',       'label'=>'Lac',            'href'=>route('hotels.public',['ville'=>'Lac'])],
      ['icon'=>'fa-leaf',           'label'=>'Parcs Nationaux','href'=>route('hotels.public',['ville'=>'Parc'])],
      ['icon'=>'fa-home',           'label'=>'Chalets',        'href'=>route('hotels.public',['categorie'=>'Chalet'])],
      ['icon'=>'fa-umbrella-beach', 'label'=>'Îles',           'href'=>route('hotels.public',['ville'=>'Île'])],
      ['icon'=>'fa-umbrella-beach', 'label'=>'Plages',         'href'=>route('hotels.public',['ville'=>'Plage'])],
      ['icon'=>'fa-home',           'label'=>'Mini Maisons',   'href'=>route('hotels.public',['categorie'=>'Maison'])],
      ['icon'=>'fa-tent',           'label'=>'Camping',        'href'=>route('hotels.public',['categorie'=>'Camping'])],
      ['icon'=>'fa-swimmer',        'label'=>'Piscines',       'href'=>route('hotels.public',['categorie'=>'Piscine'])],
    ]; @endphp
    @foreach($cats as $c)
    <a href="{{ $c['href'] }}" class="cat-item {{ $c['active'] ?? false ? 'active':'' }}">
      <div class="cat-icon"><i class="fas {{ $c['icon'] }}"></i></div>
      <div class="cat-label">{{ __($c['label']) }}</div>
    </a>
    @endforeach
    <button class="filter-btn" id="filtersOpenBtn">
      <svg viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
      {{ __('Filtres') }}
    </button>
  </div>
</div>

<!-- ═══ OVERLAY + PANEL FILTRES ═══ -->
<div class="filters-overlay" id="filtersOverlay"></div>
<div class="filters-panel" id="filtersPanel">
  <div class="filters-panel-hdr">
    <h3 class="filters-panel-title">{{ __('Filtres de recherche') }}</h3>
    <button class="filters-panel-close" id="filtersPanelClose"><i class="fas fa-xmark"></i></button>
  </div>
  <form action="{{ route('hotels.public') }}" method="GET">
    <div class="filters-group">
      <label>{{ __('Destination / Ville') }}</label>
      <input type="text" name="ville" placeholder="{{ __('Destination, ville...') }}" value="{{ request('ville') }}">
    </div>
    <div class="filters-group">
      <label>{{ __('Date d\'arrivée') }}</label>
      <input type="date" name="checkin" value="{{ request('checkin', date('Y-m-d')) }}">
    </div>
    <div class="filters-group">
      <label>{{ __('Date de départ') }}</label>
      <input type="date" name="checkout" value="{{ request('checkout', date('Y-m-d', strtotime('+1 day'))) }}">
    </div>
    <div class="filters-group">
      <label>{{ __('Voyageurs') }}</label>
      <input type="number" name="voyageurs" min="1" max="20" placeholder="1" value="{{ request('voyageurs') }}">
    </div>
    <div class="filters-group">
      <label>{{ __('Étoiles minimum') }}</label>
      <select name="stars">
        <option value="">{{ __('Toutes les catégories') }}</option>
        <option value="1" {{ request('stars')==1?'selected':'' }}>★ 1 {{ __('étoile et +') }}</option>
        <option value="2" {{ request('stars')==2?'selected':'' }}>★★ 2 {{ __('étoiles et +') }}</option>
        <option value="3" {{ request('stars')==3?'selected':'' }}>★★★ 3 {{ __('étoiles et +') }}</option>
        <option value="4" {{ request('stars')==4?'selected':'' }}>★★★★ 4 {{ __('étoiles et +') }}</option>
        <option value="5" {{ request('stars')==5?'selected':'' }}>★★★★★ 5 {{ __('étoiles') }}</option>
      </select>
    </div>
    <div class="filters-group">
      <label>{{ __('Prix maximum (FCFA / nuit)') }}</label>
      <input type="number" name="prix_max" placeholder="{{ __('Ex: 150000') }}" value="{{ request('prix_max') }}" min="0" step="5000">
    </div>
    <button type="submit" class="sv-btn" style="margin-top:8px">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      {{ __('Voir les résultats') }}
    </button>
  </form>
</div>

<!-- ===== ABIDJAN ===== -->
<section class="section">
  <div class="section-hdr">
    <div>
      <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;color:var(--coral);margin-bottom:6px">Côte d'Ivoire · Abidjan</div>
      <h2 class="section-title">{{ __('Séjours les Mieux Notés') }}<br>à <span>Abidjan</span></h2>
    </div>
    <a href="{{ route('hotels.public') }}" class="see-all">{{ __('Voir tout') }} <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg></a>
  </div>
  <div class="cards">
    @forelse($hotelsAbidjan as $hotel)
    @php $link = auth()->check() ? route('hotels.show', $hotel) : route('login'); @endphp
    <div class="card">
      <div class="card-img">
        @if($hotel->image)
          <img src="{{ str_starts_with($hotel->image,'http') ? $hotel->image : asset('storage/'.$hotel->image) }}" alt="{{ $hotel->name }}" loading="lazy" class="card-real-img">
        @else
          <img src="{{ asset('images/h1.jpg') }}" alt="{{ $hotel->name }}" loading="lazy" class="card-real-img">
        @endif
        <a href="{{ $link }}" class="card-img-link" aria-label="{{ __('Voir & Réserver') }} {{ $hotel->name }}"></a>
        <span class="card-badge">{{ $hotel->category }}</span>
        <div class="card-heart"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></div>
      </div>
      <div class="card-body">
        <div class="card-name">{{ $hotel->name }}</div>
        <div class="card-loc"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ $hotel->quartier }}, {{ $hotel->city }}</div>
        <div class="card-foot">
          <div class="card-price">{{ number_format($hotel->price, 0, ',', ' ') }} FCFA <span>{{ __('/ nuit') }}</span></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>{{ number_format($hotel->rating, 1) }}</div>
        </div>
        <a href="{{ $link }}" class="btn-voir-reserver"><i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}</a>
      </div>
    </div>
    @empty
    <p style="color:#94a3b8;padding:1rem;">Aucun hôtel disponible à Abidjan pour le moment.</p>
    @endforelse
  </div>
</section>

<!-- ===== YAMOUSSOUKRO ===== -->
<section class="section">
  <div class="section-hdr">
    <div>
      <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;color:var(--coral);margin-bottom:6px">Côte d'Ivoire · Centre</div>
      <h2 class="section-title">{{ __('Séjours Week-End Disponibles') }}<br>à <span>Yamoussoukro</span></h2>
    </div>
    <a href="{{ route('hotels.public') }}" class="see-all">{{ __('Voir tout') }} <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg></a>
  </div>
  <div class="cards">
    @forelse($hotelsYamoussoukro as $hotel)
    @php $link = auth()->check() ? route('hotels.show', $hotel) : route('login'); @endphp
    <div class="card">
      <div class="card-img">
        @if($hotel->image)
          <img src="{{ str_starts_with($hotel->image,'http') ? $hotel->image : asset('storage/'.$hotel->image) }}" alt="{{ $hotel->name }}" loading="lazy" class="card-real-img">
        @else
          <img src="{{ asset('images/h5.jpg') }}" alt="{{ $hotel->name }}" loading="lazy" class="card-real-img">
        @endif
        <a href="{{ $link }}" class="card-img-link" aria-label="{{ __('Voir & Réserver') }} {{ $hotel->name }}"></a>
        <span class="card-badge">{{ $hotel->category }}</span>
        <div class="card-heart"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></div>
      </div>
      <div class="card-body">
        <div class="card-name">{{ $hotel->name }}</div>
        <div class="card-loc"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ $hotel->quartier }}, {{ $hotel->city }}</div>
        <div class="card-foot">
          <div class="card-price">{{ number_format($hotel->price, 0, ',', ' ') }} FCFA <span>{{ __('/ nuit') }}</span></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>{{ number_format($hotel->rating, 1) }}</div>
        </div>
        <a href="{{ $link }}" class="btn-voir-reserver"><i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}</a>
      </div>
    </div>
    @empty
    <p style="color:#94a3b8;padding:1rem;">Aucun hôtel disponible à Yamoussoukro pour le moment.</p>
    @endforelse
  </div>
</section>

<!-- ===== SAN-PÉDRO ===== -->
<section class="section">
  <div class="section-hdr">
    <div>
      <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;color:var(--coral);margin-bottom:6px">Côte d'Ivoire · Sud-Ouest</div>
      <h2 class="section-title">{{ __('Belles Maisons') }}<br>à <span>San-Pédro</span></h2>
    </div>
    <a href="{{ route('hotels.public') }}" class="see-all">{{ __('Voir tout') }} <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg></a>
  </div>
  <div class="cards">
    @forelse($hotelsSanPedro as $hotel)
    @php $link = auth()->check() ? route('hotels.show', $hotel) : route('login'); @endphp
    <div class="card">
      <div class="card-img">
        @if($hotel->image)
          <img src="{{ str_starts_with($hotel->image,'http') ? $hotel->image : asset('storage/'.$hotel->image) }}" alt="{{ $hotel->name }}" loading="lazy" class="card-real-img">
        @else
          <img src="{{ asset('images/h16.jpg') }}" alt="{{ $hotel->name }}" loading="lazy" class="card-real-img">
        @endif
        <a href="{{ $link }}" class="card-img-link" aria-label="{{ __('Voir & Réserver') }} {{ $hotel->name }}"></a>
        <span class="card-badge">{{ $hotel->category }}</span>
        <div class="card-heart"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></div>
      </div>
      <div class="card-body">
        <div class="card-name">{{ $hotel->name }}</div>
        <div class="card-loc"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ $hotel->quartier }}, {{ $hotel->city }}</div>
        <div class="card-foot">
          <div class="card-price">{{ number_format($hotel->price, 0, ',', ' ') }} FCFA <span>{{ __('/ nuit') }}</span></div>
          <div class="card-rating"><svg viewBox="0 0 24 24"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>{{ number_format($hotel->rating, 1) }}</div>
        </div>
        <a href="{{ $link }}" class="btn-voir-reserver"><i class="fa-solid fa-eye"></i> {{ __('Voir & Réserver') }}</a>
      </div>
    </div>
    @empty
    <p style="color:#94a3b8;padding:1rem;">Aucun hôtel disponible dans cette région pour le moment.</p>
    @endforelse
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer-info">
  <div class="fi-grid">
    <div>
      <div class="fi-brand">
        <span style="width:44px;height:44px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.3);">
          <img src="{{ asset('images/logo.png') }}" alt="Hôtelio" style="height:38px;width:38px;object-fit:contain;">
        </span>
        Hôtelio
      </div>
      <p class="fi-desc">{{ __("Trouvez votre hébergement idéal parmi des milliers d'hôtels et de propriétés à travers le monde.") }}</p>
      @auth
        <a href="{{ auth()->user()->role === 'admin' ? route('admin.index') : (auth()->user()->role === 'prestataire' ? route('prestataire.index') : route('client.index')) }}" class="fi-discover">
          <i class="fa-solid fa-gauge-high" style="font-size:12px"></i> {{ __('Mon Dashboard') }}
        </a>
      @else
        <a href="{{ route('register') }}" class="fi-discover">{{ __('Découvrir') }} →</a>
      @endauth
    </div>
    <div>
      <div class="fi-col-title">{{ __('Hôtels') }}</div>
      <ul class="fi-links">
        <li><a href="{{ route('hotels.public') }}">{{ __('Hôtels populaires') }}</a></li>
        <li><a href="{{ route('hotels.public', ['categorie' => 'Luxe']) }}">{{ __('Villas de luxe') }}</a></li>
        <li><a href="{{ route('hotels.public') }}">{{ __('Appartements') }}</a></li>
        <li><a href="{{ route('hotels.public') }}">{{ __('Maisons de vacances') }}</a></li>
      </ul>
    </div>
    <div>
      <div class="fi-col-title">{{ __('Support') }}</div>
      <ul class="fi-links">
        <li><a href="{{ route('register', ['role' => 'prestataire']) }}">{{ __('Mettre en location') }}</a></li>
        <li><a href="{{ route('pages.show', 'ressources-hotes') }}">{{ __('Ressources hôtes') }}</a></li>
        <li><a href="{{ route('pages.show', 'forum') }}">{{ __('Forum communauté') }}</a></li>
        <li><a href="{{ route('pages.show', 'assistance-hotes') }}">{{ __('Assistance hôtes') }}</a></li>
      </ul>
    </div>
    <div>
      <div class="fi-col-title">{{ __('Légal') }}</div>
      <ul class="fi-links">
        <li><a href="{{ route('pages.show', 'centre-aide') }}">{{ __("Centre d'aide") }}</a></li>
        <li><a href="{{ route('pages.show', 'securite') }}">{{ __('Sécurité') }}</a></li>
        <li><a href="{{ route('pages.show', 'conditions-utilisation') }}">{{ __("Conditions d'utilisation") }}</a></li>
        <li><a href="{{ route('pages.show', 'confidentialite') }}">{{ __('Politique de confidentialité') }}</a></li>
      </ul>
    </div>
  </div>
  <div class="fi-bottom">
    <span>© 2026 Hôtelio. {{ __('Tous droits réservés.') }}</span>
    <div class="fi-lang-links">
      <a href="{{ route('locale.switch', 'fr') }}" class="{{ app()->getLocale()==='fr' ? 'lang-cur' : '' }}">🇫🇷 Français</a>
      <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale()==='en' ? 'lang-cur' : '' }}">🇬🇧 English</a>
      <a href="{{ route('locale.switch', 'es') }}" class="{{ app()->getLocale()==='es' ? 'lang-cur' : '' }}">🇪🇸 Español</a>
    </div>
  </div>
</footer>

<script>
// ── Slideshow hero ──
(function() {
  const slides  = document.querySelectorAll('#heroSlideshow .hero-slide');
  const dots    = document.querySelectorAll('#heroSlideshow .hero-dot');
  if (!slides.length) return;
  let current = 0, timer = null;

  function goTo(n) {
    slides[current].classList.remove('hs-active');
    dots[current]?.classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('hs-active');
    dots[current]?.classList.add('active');
  }

  function start() { timer = setInterval(() => goTo(current + 1), 3000); }
  function stop()  { clearInterval(timer); }

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => { stop(); goTo(i); start(); });
  });

  // Swipe mobile
  let touchX = 0;
  const heroR = document.getElementById('heroSlideshow');
  heroR?.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; stop(); }, {passive:true});
  heroR?.addEventListener('touchend',   e => {
    const diff = touchX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
    start();
  });

  start();
})();

// ── Catégories — highlight au clic avant navigation
document.querySelectorAll('.cat-item').forEach(item => {
  item.addEventListener('click', () => {
    document.querySelectorAll('.cat-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');
  });
});

// ── Panel Filtres ──
const filtersOpenBtn    = document.getElementById('filtersOpenBtn');
const filtersOverlay    = document.getElementById('filtersOverlay');
const filtersPanel      = document.getElementById('filtersPanel');
const filtersPanelClose = document.getElementById('filtersPanelClose');

function openFilters()  { filtersPanel.classList.add('open');    filtersOverlay.classList.add('open');    document.body.style.overflow='hidden'; }
function closeFilters() { filtersPanel.classList.remove('open'); filtersOverlay.classList.remove('open'); document.body.style.overflow=''; }

filtersOpenBtn?.addEventListener('click', openFilters);
filtersPanelClose?.addEventListener('click', closeFilters);
filtersOverlay?.addEventListener('click', closeFilters);

// Swipe vers le bas pour fermer
let fpTouchY = 0;
filtersPanel?.addEventListener('touchstart', e => { fpTouchY = e.touches[0].clientY; }, {passive:true});
filtersPanel?.addEventListener('touchend',   e => { if(e.changedTouches[0].clientY - fpTouchY > 60) closeFilters(); });

// Favoris
document.querySelectorAll('.card-heart').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.preventDefault(); e.stopPropagation();
    const svg = btn.querySelector('svg');
    const isLiked = svg.style.fill === 'var(--coral)';
    svg.style.fill = isLiked ? 'none' : 'var(--coral)';
    svg.style.stroke = isLiked ? '#777' : 'var(--coral)';
  });
});

// Animation scroll cartes
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if(entry.isIntersecting){
      entry.target.style.animation = `fadeUp 0.6s ${(i % 4) * 0.08}s ease both`;
      observer.unobserve(entry.target);
    }
  });
}, {threshold: 0.1});
document.querySelectorAll('.card').forEach(card => observer.observe(card));

// Menu mobile
const hamburger  = document.getElementById('hamburger');
const mobMenu    = document.getElementById('mobMenu');
const mobOverlay = document.getElementById('mobOverlay');
const mobClose   = document.getElementById('mobClose');
function openMenu(){mobMenu.classList.add('open');mobOverlay.classList.add('open');hamburger.classList.add('open');document.body.style.overflow='hidden'}
function closeMenu(){mobMenu.classList.remove('open');mobOverlay.classList.remove('open');hamburger.classList.remove('open');document.body.style.overflow=''}
hamburger.addEventListener('click', () => mobMenu.classList.contains('open') ? closeMenu() : openMenu());
mobClose?.addEventListener('click', closeMenu);
mobOverlay.addEventListener('click', closeMenu);
document.querySelectorAll('.mob-nav a').forEach(a => {
  a.addEventListener('click', () => {
    document.querySelectorAll('.mob-nav a').forEach(l => l.classList.remove('active'));
    a.classList.add('active');
    setTimeout(closeMenu, 180);
  });
});
let touchStartX = 0;
mobMenu.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, {passive:true});
mobMenu.addEventListener('touchend', e => { if(e.changedTouches[0].clientX - touchStartX > 60) closeMenu(); });

// ── Mode clair / sombre ──
const htmlEl         = document.documentElement;
const themeToggle    = document.getElementById('themeToggle');     // desktop
const themeIcon      = document.getElementById('themeIcon');
const themeLabel     = document.getElementById('themeLabel');
const mobThemeToggle = document.getElementById('mobThemeToggle');  // mobile
const mobThemeIcon   = document.getElementById('mobThemeIcon');
const mobThemeLabel  = document.getElementById('mobThemeLabel');
const mobToggleTrack = document.getElementById('mobToggleTrack');
const LABEL_DARK     = '{{ __("Mode sombre") }}';
const LABEL_LIGHT    = '{{ __("Mode clair") }}';

function applyTheme(theme) {
  htmlEl.setAttribute('data-theme', theme);
  localStorage.setItem('hotelio-pub-theme', theme);
  const isDark = theme === 'dark';
  /* Desktop navbar */
  if (themeIcon)  themeIcon.className   = isDark ? 'fa-solid fa-sun'  : 'fa-solid fa-moon';
  if (themeLabel) themeLabel.textContent = isDark ? LABEL_LIGHT        : LABEL_DARK;
  /* Mobile menu */
  if (mobThemeIcon)   mobThemeIcon.className    = isDark ? 'fas fa-sun' : 'fas fa-moon';
  if (mobThemeLabel)  mobThemeLabel.textContent  = isDark ? LABEL_LIGHT : LABEL_DARK;
  if (mobToggleTrack) mobToggleTrack.classList.toggle('on', isDark);
}
(function(){ applyTheme(localStorage.getItem('hotelio-pub-theme') || 'light'); })();
themeToggle?.addEventListener('click',    () => applyTheme(htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));
mobThemeToggle?.addEventListener('click', () => applyTheme(htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));

// ── Dropdown langue (desktop) ──
const langNavBtn      = document.getElementById('langNavBtn');
const langNavDropdown = document.getElementById('langNavDropdown');
const langNavWrap     = document.getElementById('langNavWrap');
langNavBtn?.addEventListener('click', e => {
  e.stopPropagation();
  const open = langNavDropdown.classList.contains('open');
  langNavDropdown.classList.toggle('open', !open);
  langNavBtn.classList.toggle('open', !open);
});
document.addEventListener('click', e => {
  if(langNavWrap && !langNavWrap.contains(e.target)){
    langNavDropdown?.classList.remove('open');
    langNavBtn?.classList.remove('open');
  }
});

// Dropdown profil connecté (desktop)
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
  if(profileNavWrap && !profileNavWrap.contains(e.target)){
    profileNavDropdown?.classList.remove('open');
    profileNavBtn?.classList.remove('open');
  }
});
</script>
</body>
</html>
