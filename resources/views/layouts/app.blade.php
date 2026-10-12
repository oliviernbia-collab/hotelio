<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hôtelio — {{ $title ?? 'Dashboard' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    /* ══════════════════════════════════════════════════════
       VARIABLES — Dark (défaut)
       ══════════════════════════════════════════════════════ */
    :root {
        --ci-orange:        #F77F00;
        --ci-orange2:       #e06e00;
        --ci-green:         #009A44;
        --ci-orange-dim:    rgba(247,127,0,.14);
        --ci-green-dim:     rgba(0,154,68,.14);
        --ci-orange-border: rgba(247,127,0,.28);
        --ci-green-border:  rgba(0,154,68,.28);

        /* Fond général */
        --bg-body:    #020617;
        --bg-sidebar: rgba(10,18,36,.98);
        --bg-topbar:  rgba(10,18,36,.96);
        --bg-card:    rgba(10,18,36,.92);
        --bg-input:   rgba(148,163,184,.07);
        --bg-hover:   rgba(247,127,0,.06);
        --bg-search:  rgba(255,255,255,.05);

        /* Textes */
        --text-primary:   #e2e8f0;
        --text-secondary: #94a3b8;
        --text-muted:     #64748b;

        /* Bordures */
        --border:         rgba(148,163,184,.14);
        --card-border:    rgba(255,255,255,.12);
        --sidebar-border: rgba(247,127,0,.28);

        /* Nav */
        --nav-link-color:  #cbd5e1;
        --nav-active-bg:   rgba(247,127,0,.15);
        --nav-active-text: #fff7ed;

        /* Table */
        --table-header: #64748b;
        --table-hover:  rgba(247,127,0,.05);
        --table-border: rgba(247,127,0,.08);

        /* Badges */
        --badge-success-bg: rgba(0,154,68,.15); --badge-success-fg: #a7f3d0;
        --badge-danger-bg:  rgba(230,57,70,.13); --badge-danger-fg:  #fca5a5;
        --badge-warning-bg: rgba(247,127,0,.15); --badge-warning-fg: #fed7aa;
        --badge-sec-bg:     rgba(148,163,184,.12); --badge-sec-fg: #cbd5e1;

        /* Inputs */
        --input-border:   rgba(247,127,0,.18);
        --input-text:     #cbd5e1;
        --label-color:    #cbd5e1;

        /* Meta */
        --meta-text: #cbd5e1;

        font-family: Inter, ui-sans-serif, system-ui, sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color-scheme: dark;
    }

    /* ══════════════════════════════════════════════════════
       VARIABLES — Light
       ══════════════════════════════════════════════════════ */
    [data-theme="light"] {
        --bg-body:    #f1f5f9;
        --bg-sidebar: #ffffff;
        --bg-topbar:  #ffffff;
        --bg-card:    #ffffff;
        --bg-input:   #f8fafc;
        --bg-hover:   rgba(247,127,0,.06);
        --bg-search:  #f1f5f9;

        --text-primary:   #0f172a;
        --text-secondary: #475569;
        --text-muted:     #94a3b8;

        --border:         rgba(0,0,0,.09);
        --card-border:    rgba(0,0,0,.09);
        --sidebar-border: rgba(247,127,0,.22);

        --nav-link-color:  #334155;
        --nav-active-bg:   rgba(247,127,0,.1);
        --nav-active-text: #c45300;

        --table-header: #475569;
        --table-hover:  rgba(247,127,0,.04);
        --table-border: rgba(0,0,0,.06);

        --badge-success-bg: #d1fae5; --badge-success-fg: #065f46;
        --badge-danger-bg:  #fee2e2; --badge-danger-fg:  #991b1b;
        --badge-warning-bg: #fef3c7; --badge-warning-fg: #92400e;
        --badge-sec-bg:     #e2e8f0; --badge-sec-fg:     #475569;

        --input-border: rgba(247,127,0,.3);
        --input-text:   #0f172a;
        --label-color:  #475569;

        --meta-text: #334155;
        color-scheme: light;
    }

    /* ══════════════════════════════════════════════════════
       BASE
       ══════════════════════════════════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
        min-height: 100vh;
        background: var(--bg-body);
        color: var(--text-primary);
        transition: background .35s, color .35s;
    }

    [data-theme="dark"] body {
        background:
            radial-gradient(circle at top left,  rgba(247,127,0,.09), transparent 32%),
            radial-gradient(circle at bottom right, rgba(0,154,68,.07), transparent 32%),
            #020617;
    }

    a { color: inherit; text-decoration: none; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(247,127,0,.35); border-radius: 10px; }

    /* ══════════════════════════════════════════════════════
       LAYOUT
       ══════════════════════════════════════════════════════ */
    .layout { display: flex; min-height: 100vh; }

    /* ══════════════════════════════════════════════════════
       OVERLAY MOBILE (backdrop)
       ══════════════════════════════════════════════════════ */
    .mob-backdrop {
        display: none;
        position: fixed; inset: 0; z-index: 39;
        background: rgba(2,6,23,.65);
        backdrop-filter: blur(4px);
        opacity: 0;
        transition: opacity .3s;
    }
    .mob-backdrop.open { opacity: 1; }

    /* ══════════════════════════════════════════════════════
       SIDEBAR
       ══════════════════════════════════════════════════════ */
    .sidebar {
        width: 288px;
        position: fixed; inset: 0 auto 0 0;
        background: var(--bg-sidebar);
        border-right: 1px solid var(--sidebar-border);
        display: flex; flex-direction: column;
        z-index: 40;
        overflow: hidden;
        transition: transform .32s cubic-bezier(.4,0,.2,1), box-shadow .32s;
    }

    /* Zone scrollable du nav (tout sauf le bas) */
    .sidebar-inner {
        flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden;
        padding: 1.5rem 1.25rem 1rem;
        display: flex; flex-direction: column; gap: 1.25rem;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }

    /* Bas fixe : thème + déconnexion */
    .sidebar-footer {
        padding: 1rem 1.25rem 1.25rem;
        border-top: 1px solid var(--border);
        display: flex; flex-direction: column; gap: .6rem;
        flex-shrink: 0;
    }

    /* ── Marque ── */
    .brand {
        display: flex; align-items: center; gap: .75rem;
    }
    .brand-icon {
        width: 42px; height: 42px;
        background: var(--ci-orange); border-radius: 13px;
        display: grid; place-items: center;
        font-size: 1.15rem; color: white; font-weight: 800;
        flex-shrink: 0;
    }
    .brand-logo-wrap {
        width: 48px; height: 48px;
        background: #fff;
        border-radius: 12px;
        display: grid; place-items: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,.25);
    }
    .brand-logo-img {
        width: 42px; height: 42px;
        object-fit: contain;
    }
    .brand-info { display: flex; flex-direction: column; gap: 3px; }
    .brand-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
    .brand-tag {
        font-size: .7rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: .08em; color: #a7f3d0;
        background: var(--ci-green-dim); border-radius: 20px;
        padding: 1px 8px; width: fit-content;
    }

    /* Bouton fermer — mobile uniquement */
    .sidebar-close {
        display: none;
        margin-left: auto; flex-shrink: 0;
        width: 34px; height: 34px; border-radius: 10px;
        background: var(--ci-orange-dim); border: 1px solid var(--ci-orange-border);
        color: var(--ci-orange); cursor: pointer;
        align-items: center; justify-content: center; font-size: .9rem;
        transition: background .2s;
    }
    .sidebar-close:hover { background: rgba(247,127,0,.25); }

    /* ── Profil ── */
    .sidebar-profile {
        background: var(--ci-orange-dim);
        border: 1px solid var(--ci-orange-border);
        padding: .9rem 1rem; border-radius: 16px;
        display: flex; align-items: center; gap: .75rem;
    }
    .profile-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: var(--ci-orange);
        display: grid; place-items: center;
        font-weight: 700; font-size: 1rem; color: white;
        flex-shrink: 0; text-transform: uppercase;
    }
    .profile-info { flex: 1; min-width: 0; }
    .profile-name  { font-weight: 700; font-size: .88rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .profile-email { font-size: .72rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .role-badge {
        display: inline-flex; padding: .2rem .65rem;
        border-radius: 999px; color: #fff7ed;
        background: var(--ci-orange-dim); border: 1px solid var(--ci-orange-border);
        font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        margin-top: 3px;
    }

    /* ── Navigation ── */
    .nav-section {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .14em; color: var(--text-muted);
        padding: .5rem .25rem .15rem;
    }
    .nav-group { display: flex; flex-direction: column; gap: .3rem; }
    .nav-link {
        display: flex; align-items: center; gap: .75rem;
        padding: .75rem 1rem; border-radius: 12px;
        color: var(--nav-link-color); font-size: .88rem; font-weight: 500;
        transition: background .18s, color .18s, transform .15s;
        position: relative;
    }
    .nav-link:hover {
        background: var(--nav-active-bg);
        color: var(--nav-active-text);
        transform: translateX(3px);
    }
    .nav-link.active {
        background: var(--nav-active-bg);
        color: var(--nav-active-text);
        font-weight: 600;
    }
    .nav-link.active::before {
        content: '';
        position: absolute; left: 0; top: 20%; bottom: 20%;
        width: 3px; border-radius: 0 3px 3px 0;
        background: var(--ci-orange);
    }
    .nav-icon {
        width: 32px; height: 32px; border-radius: 9px;
        background: var(--ci-orange-dim);
        display: flex; align-items: center; justify-content: center;
        font-size: .82rem; color: var(--ci-orange);
        flex-shrink: 0; transition: background .18s;
    }
    .nav-link:hover .nav-icon,
    .nav-link.active .nav-icon { background: rgba(247,127,0,.22); }

    /* ── Sidebar footer ── */
    .btn-theme {
        width: 100%; display: flex; align-items: center; gap: .75rem;
        padding: .75rem 1rem; border-radius: 12px; cursor: pointer;
        background: var(--ci-orange); color: white;
        border: none; font-size: .88rem; font-weight: 700;
        font-family: inherit; transition: background .2s, transform .15s;
    }
    .btn-theme:hover { background: var(--ci-orange2); transform: translateY(-1px); }
    .btn-theme .theme-icon { font-size: 1rem; transition: transform .5s; }
    .btn-theme:hover .theme-icon { transform: rotate(180deg); }

    .btn-logout {
        width: 100%; display: flex; align-items: center; gap: .75rem;
        padding: .7rem 1rem; border-radius: 12px; cursor: pointer;
        background: rgba(230,57,70,.1); color: #fca5a5;
        border: 1px solid rgba(230,57,70,.25);
        font-size: .85rem; font-weight: 600; font-family: inherit;
        transition: background .2s;
    }
    .btn-logout:hover { background: rgba(230,57,70,.2); }

    /* ══════════════════════════════════════════════════════
       TOPBAR
       ══════════════════════════════════════════════════════ */
    .main { margin-left: 288px; flex: 1; display: flex; flex-direction: column; min-width: 0; }

    .topbar {
        position: sticky; top: 0; z-index: 30;
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem 1.75rem;
        background: var(--bg-topbar);
        border-bottom: 1px solid var(--border);
        backdrop-filter: blur(12px);
        transition: background .35s, border-color .35s;
    }

    /* Bouton hamburger mobile */
    .btn-hamburger {
        display: none;
        flex-direction: column; justify-content: center; align-items: center;
        gap: 5px; width: 40px; height: 40px;
        border-radius: 10px; border: 1px solid var(--border);
        background: var(--bg-input); cursor: pointer;
        flex-shrink: 0; transition: border-color .2s;
    }
    .btn-hamburger:hover { border-color: var(--ci-orange); }
    .ham-line {
        display: block; width: 18px; height: 2px;
        background: var(--text-primary); border-radius: 2px;
        transition: transform .3s, opacity .3s, width .3s;
    }
    /* Croix quand ouvert */
    .btn-hamburger.is-open .ham-line:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .btn-hamburger.is-open .ham-line:nth-child(2) { opacity: 0; transform: scaleX(0); }
    .btn-hamburger.is-open .ham-line:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    .topbar-brand-mob {
        display: none;
        align-items: center; gap: .5rem;
        font-weight: 800; font-size: 1.05rem; color: var(--ci-orange);
    }

    .topbar-search {
        flex: 1; max-width: 400px;
        display: flex; align-items: center; gap: .6rem;
        background: var(--bg-search);
        border: 1px solid var(--border);
        border-radius: 12px; padding: .65rem 1rem;
        transition: border-color .2s, background .35s;
    }
    .topbar-search:focus-within { border-color: var(--ci-orange); }
    .topbar-search i  { color: var(--text-muted); font-size: .85rem; flex-shrink: 0; }
    .topbar-search input {
        flex: 1; border: none; outline: none; background: transparent;
        color: var(--text-primary); font-size: .88rem; font-family: inherit;
    }
    .topbar-search input::placeholder { color: var(--text-muted); }

    .topbar-right { display: flex; align-items: center; gap: .6rem; margin-left: auto; }

    /* Bouton thème dans la topbar (visible sur mobile) */
    .topbar-theme {
        display: none;
        width: 38px; height: 38px; border-radius: 10px;
        background: var(--ci-orange); color: white; border: none;
        cursor: pointer; font-size: .9rem;
        align-items: center; justify-content: center;
        transition: background .2s;
    }
    .topbar-theme:hover { background: var(--ci-orange2); }

    /* Notification bell */
    .notif-wrap { position: relative; }
    .topbar-notif {
        position: relative;
        width: 38px; height: 38px; border-radius: 10px;
        background: var(--bg-input); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--text-secondary);
        font-size: .9rem; transition: border-color .2s, color .2s;
    }
    .topbar-notif:hover { border-color: var(--ci-orange); color: var(--ci-orange); }
    .notif-badge {
        position: absolute; top: -5px; right: -5px;
        min-width: 18px; height: 18px; border-radius: 10px;
        background: #e63946; border: 2px solid var(--bg-topbar);
        font-size: .65rem; font-weight: 800; color: white;
        display: flex; align-items: center; justify-content: center;
        padding: 0 4px; line-height: 1;
    }

    /* Dropdown notifications */
    .notif-dropdown {
        position: absolute; top: calc(100% + 10px); right: 0;
        width: 360px; max-height: 480px;
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 18px; box-shadow: 0 20px 60px rgba(0,0,0,.25);
        z-index: 200; overflow: hidden;
        display: none; flex-direction: column;
        animation: dropIn .22s ease;
    }
    .notif-dropdown.open { display: flex; }
    @keyframes dropIn {
        from { opacity: 0; transform: translateY(-8px) scale(.97); }
        to   { opacity: 1; transform: translateY(0)   scale(1);    }
    }

    .notif-dd-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: .9rem 1.1rem; border-bottom: 1px solid var(--border);
        font-size: .82rem; font-weight: 700; color: var(--text-primary);
        flex-shrink: 0;
    }
    .notif-dd-list {
        overflow-y: auto; flex: 1;
        max-height: 340px;
    }
    .notif-dd-list::-webkit-scrollbar { width: 4px; }
    .notif-dd-item {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .8rem 1.1rem; border-bottom: 1px solid var(--border);
        cursor: pointer; transition: background .15s;
        text-decoration: none; color: var(--text-primary);
    }
    .notif-dd-item:hover { background: var(--bg-hover); }
    .notif-dd-item.unread { background: rgba(247,127,0,.05); }
    .notif-dd-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0;
    }
    .notif-dd-title  { font-size: .82rem; font-weight: 700; line-height: 1.3; }
    .notif-dd-msg    { font-size: .73rem; color: var(--text-secondary); line-height: 1.4;
                       white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 240px; }
    .notif-dd-time   { font-size: .68rem; color: var(--text-muted); margin-top: 2px; }
    .notif-dd-dot    {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--ci-orange); flex-shrink: 0; margin-top: 5px;
    }
    .notif-dd-footer {
        padding: .75rem 1.1rem; border-top: 1px solid var(--border);
        display: flex; gap: .5rem; justify-content: space-between;
        flex-shrink: 0;
    }
    .notif-dd-footer a, .notif-dd-footer button {
        font-size: .78rem; font-weight: 600; cursor: pointer;
        background: none; border: none; font-family: inherit;
        padding: .3rem .5rem; border-radius: 6px; transition: background .15s;
        text-decoration: none;
    }
    .notif-dd-footer a         { color: var(--ci-orange); }
    .notif-dd-footer a:hover   { background: var(--ci-orange-dim); }
    .notif-dd-footer button     { color: var(--text-muted); }
    .notif-dd-footer button:hover { color: var(--text-primary); background: var(--bg-input); }

    /* ══════════════════════════════════════════════════════
       CONTENT
       ══════════════════════════════════════════════════════ */
    .content { padding: 1.5rem 1.75rem 2.5rem; }

    /* ══════════════════════════════════════════════════════
       COMPONENTS
       ══════════════════════════════════════════════════════ */
    /* Card */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--card-border);
        border-radius: 20px; padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(0,0,0,.08);
        transition: background .35s, border-color .35s;
    }
    .card-title {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .75rem;
        margin-bottom: 1rem; color: var(--ci-orange); font-weight: 700;
    }

    /* Buttons */
    .btn {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .7rem 1.25rem; border: none; border-radius: 999px;
        background: var(--ci-orange); color: white;
        font-weight: 700; font-size: .88rem; font-family: inherit;
        cursor: pointer; transition: background .2s, transform .15s;
        text-decoration: none;
    }
    .btn:hover { background: var(--ci-orange2); transform: translateY(-1px); }
    .btn-secondary {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .65rem 1.1rem; border-radius: 999px;
        border: 1px solid var(--ci-orange-border);
        background: var(--ci-orange-dim); color: var(--nav-active-text);
        font-weight: 600; font-size: .85rem; font-family: inherit;
        cursor: pointer; transition: background .2s;
        text-decoration: none;
    }
    .btn-secondary:hover { background: rgba(247,127,0,.22); }
    .btn-green { background: var(--ci-green) !important; }
    .btn-green:hover { background: #007a36 !important; }

    /* Badges */
    .badge {
        display: inline-flex; padding: .3rem .75rem; border-radius: 999px;
        font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
    }
    .badge-success  { background: var(--badge-success-bg); color: var(--badge-success-fg); }
    .badge-danger   { background: var(--badge-danger-bg);  color: var(--badge-danger-fg); }
    .badge-warning  { background: var(--badge-warning-bg); color: var(--badge-warning-fg); }
    .badge-secondary { background: var(--badge-sec-bg);    color: var(--badge-sec-fg); }
    .badge-primary  { background: var(--ci-orange-dim);    color: var(--ci-orange); }

    .tag {
        display: inline-flex; padding: .4rem .8rem; border-radius: 999px;
        background: var(--badge-sec-bg); color: var(--text-primary); font-size: .82rem;
    }
    .tag.active { background: var(--ci-orange-dim); color: var(--ci-orange); }

    /* Grid & helpers */
    .grid { display: grid; gap: 1.25rem; }
    .cards-row { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 1.25rem; }
    .grid-cols-2 { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); }
    .grid-cols-3 { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); }
    .grid-cols-4 { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); }
    .grid-cols-6 { display: grid; grid-template-columns: repeat(6,minmax(0,1fr)); }
    .card-sm { padding: 1.25rem; }
    .meta-list { display: flex; flex-direction: column; gap: .5rem; }
    .meta-line { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .5rem; color: var(--meta-text); }
    .hero-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .6rem 1rem; border-radius: 999px;
        background: var(--ci-orange-dim); color: var(--ci-orange); font-weight: 700; font-size: .85rem;
    }

    /* Table */
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: .9rem 1rem; text-align: left; border-bottom: 1px solid var(--table-border); }
    .table th { color: var(--table-header); font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; font-weight: 700; }
    .table tbody tr { transition: background .15s; }
    .table tbody tr:hover { background: var(--table-hover); }
    .table td { color: var(--text-primary); }
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-responsive .table { min-width: 640px; }

    /* ── Selects globaux : fix lisibilité des <option> (dark + light) ──────── */
    select,
    .form-select,
    .form-group select {
        color-scheme: dark;
        background: var(--bg-input);
        color: var(--input-text);
        border: 1px solid var(--input-border);
        border-radius: 14px;
        padding: .85rem 1rem;
        font-family: inherit;
        font-size: .92rem;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .35s;
    }
    select option,
    .form-select option,
    .form-group select option {
        background: #1e293b;
        color: #e2e8f0;
    }
    [data-theme="light"] select,
    [data-theme="light"] .form-select,
    [data-theme="light"] .form-group select {
        color-scheme: light;
        background: #f8fafc;
        color: #0f172a;
    }
    [data-theme="light"] select option,
    [data-theme="light"] .form-select option,
    [data-theme="light"] .form-group select option {
        background: #ffffff;
        color: #0f172a;
    }
    select:focus,
    .form-select:focus,
    .form-group select:focus { border-color: var(--ci-orange); box-shadow: 0 0 0 3px rgba(247,127,0,.12); }

    /* Form */
    .form-group { display: grid; gap: .5rem; margin-bottom: 1rem; }
    .form-group label { font-size: .88rem; color: var(--label-color); font-weight: 500; }
    .form-group input,
    .form-group textarea {
        width: 100%;
        background: var(--bg-input);
        border: 1px solid var(--input-border);
        color: var(--input-text);
        border-radius: 14px; padding: .85rem 1rem;
        outline: none; font-size: .92rem; font-family: inherit;
        transition: border-color .2s, box-shadow .2s, background .35s;
    }
    .form-group input:focus,
    .form-group textarea:focus { border-color: var(--ci-orange); box-shadow: 0 0 0 3px rgba(247,127,0,.12); }

    /* Alerts */
    .alert-success {
        background: var(--badge-success-bg); border: 1px solid rgba(0,154,68,.3);
        border-radius: 14px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;
        color: var(--badge-success-fg); display: flex; align-items: center; gap: .75rem;
    }
    .alert-error {
        background: var(--badge-danger-bg);  border: 1px solid rgba(230,57,70,.3);
        border-radius: 14px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;
        color: var(--badge-danger-fg); display: flex; align-items: center; gap: .75rem;
    }
    .alert-info {
        background: rgba(59,130,246,.12); border: 1px solid rgba(59,130,246,.3);
        border-radius: 14px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;
        color: #93c5fd; display: flex; align-items: center; gap: .75rem;
    }

    /* Toast */
    #toast {
        position: fixed; bottom: 24px; right: 24px; z-index: 999;
        background: var(--ci-green); color: white;
        padding: .85rem 1.25rem; border-radius: 14px;
        font-size: .88rem; font-weight: 600;
        display: flex; align-items: center; gap: .6rem;
        box-shadow: 0 8px 32px rgba(0,154,68,.4);
        transform: translateY(20px); opacity: 0;
        transition: transform .3s, opacity .3s;
        pointer-events: none;
    }
    #toast.show { transform: translateY(0); opacity: 1; }

    /* ══════════════════════════════════════════════════════
       ANIMATIONS GLOBALES
       ══════════════════════════════════════════════════════ */

    /* ── Keyframes ── */
    @keyframes fadeUp    { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeDown  { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeLeft  { from{opacity:0;transform:translateX(28px)} to{opacity:1;transform:translateX(0)} }
    @keyframes fadeRight { from{opacity:0;transform:translateX(-28px)} to{opacity:1;transform:translateX(0)} }
    @keyframes fadeIn    { from{opacity:0} to{opacity:1} }
    @keyframes scaleIn   { from{opacity:0;transform:scale(.85)} to{opacity:1;transform:scale(1)} }
    @keyframes bounceIn  {
        0%  {opacity:0;transform:scale(.3)}
        50% {opacity:1;transform:scale(1.08)}
        70% {transform:scale(.95)}
        100%{transform:scale(1)}
    }
    @keyframes pulse     { 0%,100%{transform:scale(1)} 50%{transform:scale(1.04)} }
    @keyframes shimmer   {
        0%  {background-position:-200% 0}
        100%{background-position: 200% 0}
    }
    @keyframes spin      { to{transform:rotate(360deg)} }
    @keyframes slideInRight { from{transform:translateX(110%);opacity:0} to{transform:translateX(0);opacity:1} }
    @keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(110%);opacity:0} }
    @keyframes progressBar  { from{width:100%} to{width:0%} }
    @keyframes countUp      { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
    @keyframes slideShow    {
        0%   {opacity:0;transform:scale(1.06)}
        8%   {opacity:1;transform:scale(1)}
        33%  {opacity:1;transform:scale(1.02)}
        41%  {opacity:0;transform:scale(1.06)}
        100% {opacity:0}
    }
    @keyframes dotPulse     { 0%,100%{transform:scale(1)} 50%{transform:scale(1.5)} }

    /* ── Classes utilitaires d'animation ── */
    .anim-ready { opacity:0; }
    .anim-up    { animation: fadeUp    .6s ease both; }
    .anim-down  { animation: fadeDown  .6s ease both; }
    .anim-left  { animation: fadeLeft  .6s ease both; }
    .anim-right { animation: fadeRight .6s ease both; }
    .anim-fade  { animation: fadeIn    .6s ease both; }
    .anim-scale { animation: scaleIn   .5s cubic-bezier(.34,1.56,.64,1) both; }
    .anim-bounce{ animation: bounceIn  .65s ease both; }
    .anim-pulse { animation: pulse     2s ease infinite; }

    /* Délais */
    .d1 { animation-delay: .08s; }
    .d2 { animation-delay: .16s; }
    .d3 { animation-delay: .24s; }
    .d4 { animation-delay: .32s; }
    .d5 { animation-delay: .40s; }
    .d6 { animation-delay: .48s; }

    /* ── Skeleton loader (shimmer) ── */
    .skeleton {
        background: linear-gradient(90deg,var(--bg-input) 25%,rgba(255,255,255,.06) 50%,var(--bg-input) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 8px;
    }

    /* ── Card hover lift ── */
    .card-hover { transition: transform .25s, box-shadow .25s; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.2); }

    /* ══════════════════════════════════════════════════════
       SLIDESHOW HERO DASHBOARD
       ══════════════════════════════════════════════════════ */
    .dash-slideshow {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        min-height: 200px;
        margin-bottom: 1.5rem;
    }
    .dss-slide {
        position: absolute; inset: 0;
        background-size: cover; background-position: center;
        opacity: 0;
        transition: opacity 1s ease;
    }
    .dss-slide.dss-active { opacity: 1; }
    .dss-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(12,31,61,.82) 0%, rgba(0,154,68,.18) 100%);
        display: flex; align-items: center; padding: 2rem 2.5rem;
        gap: 2rem; flex-wrap: wrap;
    }
    .dss-content { flex: 1; min-width: 0; }
    .dss-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(247,127,0,.25); border: 1px solid rgba(247,127,0,.4);
        color: #fcd34d; padding: .3rem .9rem; border-radius: 20px;
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; margin-bottom: .75rem;
    }
    .dss-title {
        font-size: clamp(1.3rem,3vw,1.9rem); font-weight: 800;
        color: white; line-height: 1.2; margin-bottom: .5rem;
    }
    .dss-sub { font-size: .88rem; color: rgba(255,255,255,.7); }
    .dss-stats {
        display: flex; gap: 1.5rem; flex-wrap: wrap;
        flex-shrink: 0;
    }
    .dss-stat {
        text-align: center;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 14px; padding: .85rem 1.25rem;
        backdrop-filter: blur(8px);
    }
    .dss-stat-val {
        font-size: 1.6rem; font-weight: 800; color: white;
        line-height: 1; margin-bottom: .2rem;
    }
    .dss-stat-lbl { font-size: .68rem; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .08em; }
    /* Indicateurs (dots) */
    .dss-dots {
        position: absolute; bottom: 14px; left: 50%;
        transform: translateX(-50%);
        display: flex; gap: 6px;
    }
    .dss-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: rgba(255,255,255,.4); cursor: pointer;
        transition: background .3s, transform .3s;
        border: none; padding: 0;
    }
    .dss-dot.active {
        background: var(--ci-orange);
        transform: scale(1.35);
        animation: dotPulse 1.5s ease infinite;
    }

    /* ══════════════════════════════════════════════════════
       TOAST STACK — système de log automatique
       ══════════════════════════════════════════════════════ */
    #toast-container {
        position: fixed;
        top: 20px; right: 20px;
        z-index: 9999;
        display: flex; flex-direction: column; gap: 10px;
        pointer-events: none;
        max-width: 380px; width: calc(100% - 40px);
    }

    .toast-item {
        display: flex; align-items: flex-start; gap: .85rem;
        padding: 1rem 1.1rem;
        border-radius: 16px;
        box-shadow: 0 12px 40px rgba(0,0,0,.3), 0 2px 8px rgba(0,0,0,.15);
        pointer-events: all;
        position: relative; overflow: hidden;
        animation: slideInRight .38s cubic-bezier(.34,1.56,.64,1) both;
        border: 1px solid rgba(255,255,255,.08);
        backdrop-filter: blur(12px);
    }
    .toast-item.removing {
        animation: slideOutRight .3s ease both;
    }

    /* Couleurs par type */
    .toast-success { background: linear-gradient(135deg, rgba(0,154,68,.95), rgba(0,110,50,.9)); color: white; }
    .toast-error   { background: linear-gradient(135deg, rgba(220,38,38,.95), rgba(180,20,20,.9)); color: white; }
    .toast-warning { background: linear-gradient(135deg, rgba(217,119,6,.95), rgba(180,90,0,.9)); color: white; }
    .toast-info    { background: linear-gradient(135deg, rgba(37,99,235,.95), rgba(20,70,200,.9)); color: white; }

    .toast-icon-wrap {
        width: 36px; height: 36px; border-radius: 10px;
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .toast-body { flex: 1; min-width: 0; }
    .toast-title { font-weight: 800; font-size: .88rem; margin-bottom: 2px; }
    .toast-msg   { font-size: .8rem; opacity: .9; line-height: 1.4;
                   white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .toast-close {
        background: none; border: none; color: rgba(255,255,255,.7);
        cursor: pointer; font-size: .9rem; padding: .2rem;
        align-self: flex-start; flex-shrink: 0;
        transition: color .2s;
    }
    .toast-close:hover { color: white; }

    /* Barre de progression */
    .toast-progress {
        position: absolute; bottom: 0; left: 0;
        height: 3px; border-radius: 0 0 0 16px;
        background: rgba(255,255,255,.45);
        animation: progressBar var(--duration,5s) linear forwards;
    }

    /* ══════════════════════════════════════════════════════
       PROFIL UTILISATEUR — Topbar mobile
       ══════════════════════════════════════════════════════ */
    .topbar-user-mob {
        display: none;
        align-items: center; gap: .45rem;
    }
    .topbar-user-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--ci-orange);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .8rem; color: white;
        flex-shrink: 0; text-transform: uppercase;
        border: 2px solid rgba(247,127,0,.35);
    }
    .topbar-user-name {
        font-size: .8rem; font-weight: 700;
        color: var(--text-primary);
        max-width: 100px; overflow: hidden;
        text-overflow: ellipsis; white-space: nowrap;
    }

    /* ══════════════════════════════════════════════════════
       SÉLECTEUR DE LANGUE
       ══════════════════════════════════════════════════════ */
    .lang-wrap { position: relative; }
    .lang-btn {
        display: flex; align-items: center; gap: .35rem;
        padding: .45rem .7rem; border-radius: 10px;
        background: var(--bg-input); border: 1px solid var(--border);
        color: var(--text-secondary); cursor: pointer;
        font-size: .78rem; font-weight: 800; font-family: inherit;
        transition: border-color .2s, color .2s;
        height: 38px;
    }
    .lang-btn:hover { border-color: var(--ci-orange); color: var(--ci-orange); }
    .lang-btn.open  { border-color: var(--ci-orange); color: var(--ci-orange); }
    .lang-chevron { font-size: .6rem; transition: transform .2s; }
    .lang-btn.open .lang-chevron { transform: rotate(180deg); }
    .lang-dropdown {
        position: absolute; top: calc(100% + 8px); right: 0;
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,.22);
        z-index: 300; overflow: hidden;
        display: none; flex-direction: column;
        min-width: 155px;
        animation: dropIn .22s ease;
    }
    .lang-dropdown.open { display: flex; }
    .lang-option {
        display: flex; align-items: center; gap: .55rem;
        padding: .65rem 1rem; font-size: .84rem; font-weight: 500;
        color: var(--text-primary); text-decoration: none;
        transition: background .15s;
    }
    .lang-option:hover { background: var(--bg-hover); }
    .lang-option.lang-active { color: var(--ci-orange); font-weight: 700; background: var(--ci-orange-dim); }

    /* ══════════════════════════════════════════════════════
       RESPONSIVE — Mobile menu
       ══════════════════════════════════════════════════════ */
    @media (max-width: 1120px) {
        .sidebar {
            transform: translateX(-100%);
            box-shadow: none;
            width: 300px;
        }
        .sidebar.mob-open {
            transform: translateX(0);
            box-shadow: 4px 0 40px rgba(0,0,0,.45);
        }
        .mob-backdrop { display: block; pointer-events: none; }
        .mob-backdrop.open { pointer-events: all; }
        .sidebar-close { display: flex; }
        .main { margin-left: 0; }
        .btn-hamburger { display: flex; }
        .topbar-theme { display: flex; }
        .topbar-brand-mob { display: flex; }
        .topbar-user-mob { display: flex; }
        .cards-row, .grid-cols-3, .grid-cols-4 { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .grid-cols-6 { grid-template-columns: repeat(3,minmax(0,1fr)); }
        .grid[style*="2fr 1fr"], .grid[style*="1fr 2fr"], .grid[style*="1fr 1.5fr"], .grid[style*="1.5fr 1fr"] { grid-template-columns: 1fr !important; }
    }

    @media (max-width: 720px) {
        .topbar { padding: .85rem 1rem; }
        .topbar-search { display: none; }
        .content { padding: 1rem 1rem 2rem; }
        .cards-row, .grid-cols-2, .grid-cols-3, .grid-cols-4, .grid-cols-6 { grid-template-columns: 1fr; }
        .grid[style*="grid-template-columns: 1fr 1fr"], .grid[style*="grid-template-columns:1fr 1fr"] { grid-template-columns: 1fr !important; }
        .grid[style*="repeat(4"] { grid-template-columns: 1fr 1fr !important; }
        .card { padding: 1.1rem; border-radius: 16px; }
        .table th, .table td { padding: .65rem .6rem; font-size: .82rem; }
        .topbar-user-name { display: none; }
        .lang-btn span:not(.lang-chevron) { display: none; }
    }
    </style>
    @stack('styles')
</head>
<body>

<!-- ── Backdrop mobile ── -->
<div class="mob-backdrop" id="mobBackdrop" onclick="closeSidebar()"></div>

<!-- ════════════════════════════════════════════════════
     TOAST STACK — Log automatique
     ════════════════════════════════════════════════════ -->
<div id="toast-container" role="alert" aria-live="polite"></div>

{{-- Auto-log depuis les sessions PHP --}}
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',()=>toast('{{ addslashes(session('success')) }}','success'))</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',()=>toast('{{ addslashes(session('error')) }}','error'))</script>
@endif
@if(session('info'))
<script>document.addEventListener('DOMContentLoaded',()=>toast('{{ addslashes(session('info')) }}','info'))</script>
@endif
@if(session('warning'))
<script>document.addEventListener('DOMContentLoaded',()=>toast('{{ addslashes(session('warning')) }}','warning'))</script>
@endif

<div class="layout">

    <!-- ════════════════════════════════════════════════════
         SIDEBAR
         ════════════════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">

        <!-- Zone scrollable -->
        <div class="sidebar-inner">

            <!-- Marque + bouton fermer -->
            <div class="brand">
                <span class="brand-logo-wrap">
                    <img src="{{ asset('images/logo.png') }}" alt="Hôtelio" class="brand-logo-img">
                </span>
                <div class="brand-info">
                    <div class="brand-title">Hôtelio</div>
                    <span class="brand-tag">{{ __('Gestion hôtelière') }}</span>
                </div>
                <button class="sidebar-close" id="sidebarClose" onclick="closeSidebar()" title="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Profil -->
            @auth
            <div class="sidebar-profile">
                <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <div class="profile-info">
                    <div class="profile-name">{{ auth()->user()->name }}</div>
                    <div class="profile-email">{{ auth()->user()->email }}</div>
                    <span class="role-badge">
                        @if(auth()->user()->role==='admin') <i class="fa-solid fa-crown"></i>
                        @elseif(auth()->user()->role==='prestataire') <i class="fa-solid fa-building"></i>
                        @else <i class="fa-solid fa-user"></i>
                        @endif
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="nav-group">
                <a href="{{ route('premiere') }}" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-globe"></i></span>
                    <span>{{ __('Site public') }}</span>
                </a>

                @if(auth()->user()->role === 'admin')
                    <div class="nav-section">{{ __('Administration') }}</div>
                    <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-crown"></i></span><span>{{ __('Panel Admin') }}</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-users"></i></span><span>{{ __('Utilisateurs') }}</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-bar"></i></span><span>{{ __('Rapports') }}</span>
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="nav-link {{ request()->routeIs('subscriptions.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-credit-card"></i></span><span>{{ __('Abonnements') }}</span>
                    </a>
                    <a href="{{ route('payouts.index') }}" class="nav-link {{ request()->routeIs('payouts.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><span>{{ __('Reversements') }}</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gear"></i></span><span>{{ __('Paramètres') }}</span>
                    </a>
                    <a href="{{ route('admin.journal') }}" class="nav-link {{ request()->routeIs('admin.journal') ? 'active' : '' }}" style="position:relative;">
                        <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        <span>Journal d'activité</span>
                        @php $todayLogs = \App\Models\ActivityLog::whereDate('created_at', today())->count(); @endphp
                        @if($todayLogs > 0)
                        <span style="margin-left:auto;min-width:20px;height:20px;background:var(--ci-orange);border-radius:10px;font-size:.65rem;font-weight:800;color:white;display:flex;align-items:center;justify-content:center;padding:0 5px;">
                            {{ $todayLogs > 99 ? '99+' : $todayLogs }}
                        </span>
                        @endif
                    </a>

                @elseif(auth()->user()->role === 'prestataire')
                    <div class="nav-section">{{ __('Tableau de bord') }}</div>
                    <a href="{{ route('prestataire.index') }}" class="nav-link {{ request()->routeIs('prestataire.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span><span>{{ __('Dashboard') }}</span>
                    </a>

                    <div class="nav-section">{{ __('Hôtels & Chambres') }}</div>
                    <a href="{{ route('hotels.index', ['scope' => 'tous']) }}" class="nav-link {{ request()->routeIs('hotels.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-hotel"></i></span><span>{{ __('Hôtels') }}</span>
                    </a>
                    <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-bed"></i></span><span>{{ __('Chambres') }}</span>
                    </a>
                    <a href="{{ route('reservations.index') }}" class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span><span>{{ __('Réservations') }}</span>
                    </a>
                    <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span><span>{{ __('Calendrier') }}</span>
                    </a>

                    <div class="nav-section">{{ __('Services') }}</div>
                    <a href="{{ route('menu.index') }}" class="nav-link {{ request()->routeIs('menu.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-utensils"></i></span><span>{{ __('Menu') }}</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-receipt"></i></span><span>{{ __('Commandes') }}</span>
                    </a>
                    <a href="{{ route('pools.index') }}" class="nav-link {{ request()->routeIs('pools.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-water-ladder"></i></span><span>{{ __('Piscines') }}</span>
                    </a>

                    <div class="nav-section">{{ __('Gestion') }}</div>
                    <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-users"></i></span><span>{{ __('Clients') }}</span>
                    </a>
                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-user-tie"></i></span><span>{{ __('Personnel') }}</span>
                    </a>
                    <a href="{{ route('staff.schedule') }}" class="nav-link {{ request()->routeIs('staff.schedule') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span><span>{{ __('Planning') }}</span>
                    </a>
                    <a href="{{ route('pointage.index') }}" class="nav-link {{ request()->routeIs('pointage.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-clock"></i></span><span>{{ __('Pointage') }}</span>
                    </a>
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-money-bill-wave"></i></span><span>{{ __('Charges') }}</span>
                    </a>
                    <a href="{{ route('boutique.index') }}" class="nav-link {{ request()->routeIs('boutique.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-shop"></i></span><span>{{ __('Boutique') }}</span>
                    </a>
                    <a href="{{ route('facturations.index') }}" class="nav-link {{ request()->routeIs('facturations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span><span>{{ __('Facturation') }}</span>
                    </a>
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-ticket"></i></span><span>{{ __('Tickets') }}</span>
                    </a>

                @elseif(auth()->user()->role === 'employe')
                    {{-- ── EMPLOYÉ : menus selon permissions ── --}}
                    <div class="nav-section">Mon espace</div>
                    <a href="{{ route('prestataire.index') }}" class="nav-link {{ request()->routeIs('prestataire.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span><span>Dashboard</span>
                    </a>
                    <a href="{{ route('pointage.index') }}" class="nav-link {{ request()->routeIs('pointage.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-clock"></i></span><span>{{ __('Pointage') }}</span>
                    </a>
                    @if(auth()->user()->hasPermission('reservations'))
                    <a href="{{ route('reservations.index') }}" class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span><span>{{ __('Réservations') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('rooms'))
                    <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-bed"></i></span><span>{{ __('Chambres') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('calendar'))
                    <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span><span>{{ __('Calendrier') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('clients'))
                    <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-users"></i></span><span>{{ __('Clients') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('staff'))
                    <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-user-tie"></i></span><span>{{ __('Personnel') }}</span>
                    </a>
                    <a href="{{ route('staff.schedule') }}" class="nav-link {{ request()->routeIs('staff.schedule') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-days"></i></span><span>{{ __('Planning') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('menu'))
                    <a href="{{ route('menu.index') }}" class="nav-link {{ request()->routeIs('menu.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-utensils"></i></span><span>{{ __('Menu') }}</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-receipt"></i></span><span>{{ __('Commandes') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('pools'))
                    <a href="{{ route('pools.index') }}" class="nav-link {{ request()->routeIs('pools.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-water-ladder"></i></span><span>{{ __('Piscines') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('expenses'))
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-money-bill-wave"></i></span><span>{{ __('Charges') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('boutique'))
                    <a href="{{ route('boutique.index') }}" class="nav-link {{ request()->routeIs('boutique.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-store"></i></span><span>{{ __('Boutique') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('reports'))
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-bar"></i></span><span>{{ __('Rapports') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('billing'))
                    <a href="{{ route('facturations.index') }}" class="nav-link {{ request()->routeIs('facturations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span><span>{{ __('Facturation') }}</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('tickets'))
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-ticket"></i></span><span>{{ __('Tickets') }}</span>
                    </a>
                    @endif

                @else
                    <div class="nav-section">{{ __('Mon espace') }}</div>
                    <a href="{{ route('client.index') }}" class="nav-link {{ request()->routeIs('client.index') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span><span>{{ __('Mon Dashboard') }}</span>
                    </a>
                    <a href="{{ route('client.reservations') }}" class="nav-link {{ request()->routeIs('client.reservations') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span><span>{{ __('Mes Réservations') }}</span>
                    </a>
                    <a href="{{ route('client.favorites') }}" class="nav-link {{ request()->routeIs('client.favorites') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-heart"></i></span><span>{{ __('Mes Favoris') }}</span>
                        @php $favCount = auth()->user()->favoriteHotels()->count(); @endphp
                        @if($favCount > 0)
                        <span style="margin-left:auto;background:var(--ci-orange);color:white;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:10px;line-height:1.6">{{ $favCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('client.commandes') }}" class="nav-link {{ request()->routeIs('client.commandes') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-bag-shopping"></i></span><span>{{ __('Mes commandes') }}</span>
                    </a>
                    <a href="{{ route('boutique.clientboutique') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-shop"></i></span><span>{{ __('Boutique') }}</span>
                    </a>
                @endif

                <div class="nav-section">{{ __('Compte') }}</div>
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" style="position:relative">
                    <span class="nav-icon"><i class="fa-regular fa-bell"></i></span>
                    <span>{{ __('Notifications') }}</span>
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if($unread > 0)
                    <span style="margin-left:auto;min-width:20px;height:20px;background:#e63946;border-radius:10px;font-size:.68rem;font-weight:800;color:white;display:flex;align-items:center;justify-content:center;padding:0 5px">
                        {{ $unread > 99 ? '99+' : $unread }}
                    </span>
                    @endif
                </a>
                <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.index') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-id-card"></i></span><span>{{ __('Mon Profil') }}</span>
                </a>
            </div>
            @endauth
        </div>

        <!-- Footer fixe : thème + déconnexion -->
        <div class="sidebar-footer">
            <button class="btn-theme" id="themeBtnSidebar" onclick="toggleTheme()">
                <span class="theme-icon"><i class="fa-solid fa-moon" id="themeIconSidebar"></i></span>
                <span id="themeLabelSidebar">{{ __('Mode clair') }}</span>
            </button>
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    {{ __('Déconnexion') }}
                </button>
            </form>
            @endauth
        </div>
    </aside>

    <!-- ════════════════════════════════════════════════════
         MAIN
         ════════════════════════════════════════════════════ -->
    <main class="main">

        <!-- Topbar -->
        <div class="topbar">
            <!-- Hamburger (mobile) -->
            <button class="btn-hamburger" id="hamburger" onclick="openSidebar()" aria-label="Menu">
                <span class="ham-line"></span>
                <span class="ham-line"></span>
                <span class="ham-line"></span>
            </button>

            <!-- Logo mobile -->
            <div class="topbar-brand-mob">
                <i class="fa-solid fa-hotel" style="color:var(--ci-orange)"></i> Hôtelio
            </div>

            <!-- Recherche -->
            <div class="topbar-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="{{ __("Rechercher dans l'application…") }}">
            </div>

            <div class="topbar-right">

                <!-- Profil utilisateur (mobile uniquement) -->
                @auth
                <div class="topbar-user-mob">
                    <div class="topbar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span class="topbar-user-name">{{ auth()->user()->name }}</span>
                </div>
                @endauth

                <!-- Sélecteur de langue -->
                <div class="lang-wrap" id="langWrap">
                    <button class="lang-btn" id="langBtn" title="Language">
                        <i class="fa-solid fa-globe" style="font-size:.8rem"></i>
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <i class="fa-solid fa-chevron-down lang-chevron"></i>
                    </button>
                    <div class="lang-dropdown" id="langDropdown">
                        <a href="{{ route('locale.switch', 'fr') }}" class="lang-option {{ app()->getLocale() === 'fr' ? 'lang-active' : '' }}">
                            🇫🇷 {{ __('Français') }}
                        </a>
                        <a href="{{ route('locale.switch', 'en') }}" class="lang-option {{ app()->getLocale() === 'en' ? 'lang-active' : '' }}">
                            🇬🇧 {{ __('Anglais') }}
                        </a>
                        <a href="{{ route('locale.switch', 'es') }}" class="lang-option {{ app()->getLocale() === 'es' ? 'lang-active' : '' }}">
                            🇪🇸 {{ __('Espagnol') }}
                        </a>
                    </div>
                </div>

                <!-- Bouton thème (mobile, dans topbar) -->
                <button class="topbar-theme" onclick="toggleTheme()" title="{{ __('Changer le thème') }}">
                    <i class="fa-solid fa-moon" id="themeIconTopbar"></i>
                </button>

                <!-- Cloche notifications avec dropdown -->
                <div class="notif-wrap" id="notifWrap">
                    <div class="topbar-notif" id="notifBtn" onclick="toggleNotifDropdown()" title="{{ __('Notifications') }}">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notif-badge" id="notifBadge" style="display:none">0</span>
                    </div>

                    <!-- Dropdown -->
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-dd-header">
                            <span><i class="fa-regular fa-bell" style="color:var(--ci-orange)"></i> {{ __('Notifications') }}</span>
                            <span id="notifUnreadLabel" style="color:var(--ci-orange);font-size:.75rem"></span>
                        </div>
                        <div class="notif-dd-list" id="notifList">
                            <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:.85rem">
                                <i class="fa-regular fa-bell-slash" style="font-size:1.5rem;margin-bottom:.5rem;display:block"></i>
                                {{ __('Chargement…') }}
                            </div>
                        </div>
                        <div class="notif-dd-footer">
                            <a href="{{ route('notifications.index') }}">
                                <i class="fa-solid fa-list"></i> {{ __('Tout voir') }}
                            </a>
                            <form method="POST" action="{{ route('notifications.read-all') }}" id="readAllForm">
                                @csrf @method('PATCH')
                                <button type="submit">
                                    <i class="fa-solid fa-check-double"></i> {{ __('Tout lire') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Contenu -->
        <section class="content">
            @yield('content')
        </section>
    </main>
</div>

<script>
/* ══════════════════════════════════════════════════
   SÉLECTEUR DE LANGUE
   ══════════════════════════════════════════════════ */
const langBtn      = document.getElementById('langBtn');
const langDropdown = document.getElementById('langDropdown');
const langWrap     = document.getElementById('langWrap');

if (langBtn) {
    langBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = langDropdown.classList.contains('open');
        langDropdown.classList.toggle('open', !isOpen);
        langBtn.classList.toggle('open', !isOpen);
    });
}
document.addEventListener('click', function(e) {
    if (langWrap && !langWrap.contains(e.target)) {
        langDropdown?.classList.remove('open');
        langBtn?.classList.remove('open');
    }
});

/* ══════════════════════════════════════════════════
   MENU MOBILE
   ══════════════════════════════════════════════════ */
const sidebar     = document.getElementById('sidebar');
const backdrop    = document.getElementById('mobBackdrop');
const hamburger   = document.getElementById('hamburger');

function openSidebar() {
    sidebar.classList.add('mob-open');
    backdrop.classList.add('open');
    hamburger.classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebar.classList.remove('mob-open');
    backdrop.classList.remove('open');
    hamburger.classList.remove('is-open');
    document.body.style.overflow = '';
}

/* Fermer en appuyant sur Échap */
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

/* Fermer quand on clique sur un lien de nav (mobile) */
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 1120) closeSidebar();
    });
});

/* ══════════════════════════════════════════════════
   THÈME DARK / LIGHT
   ══════════════════════════════════════════════════ */
const root        = document.documentElement;
const iconSidebar = document.getElementById('themeIconSidebar');
const labelSidebar= document.getElementById('themeLabelSidebar');
const iconTopbar  = document.getElementById('themeIconTopbar');

function applyTheme(theme) {
    root.setAttribute('data-theme', theme);
    localStorage.setItem('hotelio-theme', theme);

    const isDark = theme === 'dark';
    /* Icône sidebar */
    if (iconSidebar) {
        iconSidebar.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        labelSidebar.textContent = isDark ? '{{ __("Mode clair") }}' : '{{ __("Mode sombre") }}';
    }
    /* Icône topbar (mobile) */
    if (iconTopbar) {
        iconTopbar.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
}

function toggleTheme() {
    const current = root.getAttribute('data-theme') || 'dark';
    applyTheme(current === 'dark' ? 'light' : 'dark');
}

/* Charger le thème sauvegardé */
(function () {
    const saved = localStorage.getItem('hotelio-theme') || 'dark';
    applyTheme(saved);
})();

/* ══════════════════════════════════════════════════
   TOAST
   ══════════════════════════════════════════════════ */
function notify(msg, type = 'success') {
    const toast = document.getElementById('toast');
    const span  = document.getElementById('toast-msg');
    const icon  = toast.querySelector('i');
    span.textContent = msg;
    icon.className   = type === 'error'
        ? 'fa-solid fa-circle-exclamation'
        : 'fa-solid fa-circle-check';
    toast.style.background = type === 'error' ? '#e63946' : 'var(--ci-green)';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

function toggleReservationForm(id) {
    const f = document.getElementById(id);
    f.style.display = f.style.display === 'none' || !f.style.display ? 'block' : 'none';
}

/* ══════════════════════════════════════════════════════════
   TOAST SYSTEM — Log automatique
   ══════════════════════════════════════════════════════════ */
const TOAST_CFG = {
    success: { icon: 'fa-circle-check',        title: '{{ __("Succès") }}',      duration: 5000 },
    error:   { icon: 'fa-circle-exclamation',  title: '{{ __("Erreur") }}',      duration: 7000 },
    warning: { icon: 'fa-triangle-exclamation',title: '{{ __("Attention") }}',   duration: 6000 },
    info:    { icon: 'fa-circle-info',         title: '{{ __("Information") }}', duration: 5000 },
};

function toast(message, type = 'success', title = null) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const cfg      = TOAST_CFG[type] || TOAST_CFG.success;
    const duration = cfg.duration;
    const toastEl  = document.createElement('div');
    toastEl.className = `toast-item toast-${type}`;

    toastEl.innerHTML = `
        <div class="toast-icon-wrap">
            <i class="fa-solid ${cfg.icon}"></i>
        </div>
        <div class="toast-body">
            <div class="toast-title">${title || cfg.title}</div>
            <div class="toast-msg">${message}</div>
        </div>
        <button class="toast-close" onclick="closeToast(this.parentElement)" title="Fermer">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="toast-progress" style="--duration:${duration}ms"></div>
    `;

    container.appendChild(toastEl);

    // Auto-dismiss
    const timer = setTimeout(() => closeToast(toastEl), duration);
    toastEl._timer = timer;

    // Pause au survol
    toastEl.addEventListener('mouseenter', () => {
        clearTimeout(toastEl._timer);
        toastEl.querySelector('.toast-progress').style.animationPlayState = 'paused';
    });
    toastEl.addEventListener('mouseleave', () => {
        toastEl.querySelector('.toast-progress').style.animationPlayState = 'running';
        toastEl._timer = setTimeout(() => closeToast(toastEl), 2000);
    });
}

function closeToast(el) {
    if (!el || el.classList.contains('removing')) return;
    clearTimeout(el._timer);
    el.classList.add('removing');
    el.addEventListener('animationend', () => el.remove(), { once: true });
}

// Alias pour la compatibilité avec l'ancien code
function notify(msg, type = 'success') { toast(msg, type); }

/* ══════════════════════════════════════════════════════════
   ENTRANCE ANIMATIONS — IntersectionObserver
   ══════════════════════════════════════════════════════════ */
(function initAnimations() {
    // Marquer tous les éléments animables comme prêts (invisibles)
    const selectors = [
        '.card', '.stat-box', '.rbox', '.hotel-card', '.notif-item',
        '.hotel-bar-row', '.event-item', '.why-card'
    ];
    document.querySelectorAll(selectors.join(',')).forEach((el, i) => {
        if (!el.dataset.animated) {
            el.dataset.animated = 'pending';
            el.classList.add('anim-ready');
        }
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.target.dataset.animated === 'pending') {
                const el    = entry.target;
                const idx   = parseInt(el.dataset.animIdx || 0);
                const types = ['anim-up','anim-up','anim-left','anim-right','anim-scale'];
                const type  = types[idx % types.length];
                const delay = Math.min(idx * 60, 400);

                el.style.animationDelay = delay + 'ms';
                el.classList.remove('anim-ready');
                el.classList.add(type);
                el.dataset.animated = 'done';
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

    // Numéroter et observer
    document.querySelectorAll(selectors.join(',')).forEach((el, i) => {
        el.dataset.animIdx = i;
        observer.observe(el);
    });

    // Re-observer les éléments ajoutés dynamiquement
    const mutObs = new MutationObserver(mutations => {
        mutations.forEach(m => {
            m.addedNodes.forEach(node => {
                if (node.nodeType !== 1) return;
                selectors.forEach(sel => {
                    node.querySelectorAll && node.querySelectorAll(sel).forEach((el, i) => {
                        if (!el.dataset.animated) {
                            el.dataset.animated = 'pending';
                            el.dataset.animIdx  = i;
                            el.classList.add('anim-ready');
                            observer.observe(el);
                        }
                    });
                });
            });
        });
    });
    mutObs.observe(document.body, { childList: true, subtree: true });
})();

/* ══════════════════════════════════════════════════════════
   ANIMATED COUNTERS — [data-count]
   ══════════════════════════════════════════════════════════ */
function animateCounter(el) {
    const target   = parseInt(el.dataset.count.replace(/\s/g,'')) || 0;
    const duration = 1200;
    const step     = 16;
    const steps    = Math.ceil(duration / step);
    let   current  = 0;
    const suffix   = el.dataset.suffix || '';
    const prefix   = el.dataset.prefix || '';

    const timer = setInterval(() => {
        current = Math.min(current + Math.ceil(target / steps), target);
        el.textContent = prefix + current.toLocaleString('fr-FR') + suffix;
        if (current >= target) clearInterval(timer);
    }, step);
}

const counterObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.counted) {
            entry.target.dataset.counted = '1';
            animateCounter(entry.target);
            counterObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));

/* ══════════════════════════════════════════════════════════
   DASHBOARD SLIDESHOW
   ══════════════════════════════════════════════════════════ */
function initSlideshow(containerId, interval = 4500) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const slides = container.querySelectorAll('.dss-slide');
    const dots   = container.querySelectorAll('.dss-dot');
    if (!slides.length) return;

    let current = 0, timer = null;

    function goTo(idx) {
        slides[current].classList.remove('dss-active');
        dots[current]?.classList.remove('active');
        current = (idx + slides.length) % slides.length;
        slides[current].classList.add('dss-active');
        dots[current]?.classList.add('active');
    }

    function next() { goTo(current + 1); }

    function startAuto() { timer = setInterval(next, interval); }
    function stopAuto()  { clearInterval(timer); }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { stopAuto(); goTo(i); startAuto(); });
    });

    // Swipe support
    let touchX = 0;
    container.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; stopAuto(); }, {passive:true});
    container.addEventListener('touchend',   e => {
        const diff = touchX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
        startAuto();
    });

    slides[0].classList.add('dss-active');
    dots[0]?.classList.add('active');
    startAuto();
}

// Lancer tous les slideshows présents sur la page
document.querySelectorAll('.dash-slideshow[id]').forEach(el => initSlideshow(el.id));

/* ══════════════════════════════════════════════════
   NOTIFICATIONS DROPDOWN
   ══════════════════════════════════════════════════ */
let notifLoaded = false;

function toggleNotifDropdown() {
    const dropdown = document.getElementById('notifDropdown');
    const isOpen   = dropdown.classList.contains('open');

    if (isOpen) {
        dropdown.classList.remove('open');
        return;
    }

    dropdown.classList.add('open');
    if (!notifLoaded) loadNotifications();
}

/* Fermer en cliquant en dehors */
document.addEventListener('click', e => {
    const wrap = document.getElementById('notifWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notifDropdown')?.classList.remove('open');
    }
});

function loadNotifications() {
    fetch('{{ route("notifications.fetch") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        notifLoaded = true;
        renderNotifications(data.notifications, data.unread_count);
    })
    .catch(() => {
        document.getElementById('notifList').innerHTML =
            '<div style="text-align:center;padding:1.5rem;color:var(--text-muted)">{{ __("Impossible de charger les notifications.") }}</div>';
    });
}

function renderNotifications(items, unreadCount) {
    /* Badge */
    const badge = document.getElementById('notifBadge');
    if (unreadCount > 0) {
        badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }

    /* Label */
    const label = document.getElementById('notifUnreadLabel');
    if (label) label.textContent = unreadCount > 0 ? `${unreadCount} non lue(s)` : '';

    /* Liste */
    const list = document.getElementById('notifList');
    if (!items.length) {
        list.innerHTML = `
            <div style="text-align:center;padding:2rem;color:var(--text-muted)">
                <i class="fa-regular fa-bell-slash" style="font-size:1.5rem;margin-bottom:.5rem;display:block"></i>
                {{ __('Aucune notification') }}
            </div>`;
        return;
    }

    list.innerHTML = items.map(n => {
        const d     = n.data;
        const color = d.color || 'var(--ci-orange)';
        const icon  = d.icon  || 'fa-bell';
        const url   = d.url   || '#';
        return `
        <a class="notif-dd-item ${n.read ? '' : 'unread'}" href="${url}"
           onclick="markReadAndGo(event,'${n.id}','${url}')">
            <div class="notif-dd-icon" style="background:${color}18;color:${color}">
                <i class="fa-solid ${icon}"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div class="notif-dd-title">${d.title || 'Notification'}</div>
                <div class="notif-dd-msg">${d.message || ''}</div>
                <div class="notif-dd-time"><i class="fa-regular fa-clock"></i> ${n.created_at}</div>
            </div>
            ${!n.read ? '<div class="notif-dd-dot"></div>' : ''}
        </a>`;
    }).join('');
}

function markReadAndGo(e, id, url) {
    e.preventDefault();
    fetch(`/notifications/${id}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    }).finally(() => { window.location.href = url; });
}

/* Charger le compteur dès le démarrage */
fetch('{{ route("notifications.fetch") }}', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
})
.then(r => r.json())
.then(data => {
    const badge = document.getElementById('notifBadge');
    if (data.unread_count > 0) {
        badge.textContent    = data.unread_count > 99 ? '99+' : data.unread_count;
        badge.style.display  = 'flex';
    }
    const label = document.getElementById('notifUnreadLabel');
    if (label && data.unread_count > 0) label.textContent = `${data.unread_count} non lue(s)`;
})
.catch(() => {});

/* ── Polling temps réel des notifications ─────────────────────────────────── */
@auth
(function () {
    const POLL_INTERVAL = 30000; // 30 s
    const FETCH_URL = '{{ route("notifications.fetch") }}';

    let lastUnread = parseInt(document.getElementById('notifBadge')?.textContent || '0', 10) || 0;
    let pollTimer  = null;
    let pollActive = true;

    function updateBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent   = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.style.cssText = [
            'position:fixed','bottom:1.5rem','right:1.5rem','z-index:9999',
            'background:var(--ci-orange)','color:#fff','padding:.8rem 1.2rem',
            'border-radius:14px','box-shadow:0 8px 24px rgba(0,0,0,.3)',
            'font-size:.88rem','font-weight:600','display:flex',
            'align-items:center','gap:.6rem','max-width:320px',
            'animation:slideInToast .3s ease',
        ].join(';');
        toast.innerHTML = '<i class="fa-solid fa-bell"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity .4s';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    function poll() {
        if (!pollActive) return;
        fetch(FETCH_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const count = data.unread_count || 0;
                updateBadge(count);

                // Si nouvelles notifications depuis le dernier poll
                if (count > lastUnread) {
                    const diff = count - lastUnread;
                    showToast(`${diff} nouvelle${diff > 1 ? 's' : ''} notification${diff > 1 ? 's' : ''}`);

                    // Animer la cloche
                    const bell = document.querySelector('.topbar-notif i');
                    if (bell) {
                        bell.style.animation = 'none';
                        setTimeout(() => { bell.style.animation = 'bellShake .5s ease 2'; }, 10);
                    }
                }
                lastUnread = count;

                // Si le dropdown est ouvert, le rafraîchir silencieusement
                const dd = document.getElementById('notifDropdown');
                if (dd && dd.classList.contains('open')) {
                    renderNotifications(data.notifications, count);
                }
                notifLoaded = false;
            }).catch(() => {});
    }

    // Pause quand onglet caché, reprise quand onglet actif
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            pollActive = false;
            clearInterval(pollTimer);
        } else {
            pollActive = true;
            poll(); // poll immédiat au retour
            pollTimer = setInterval(poll, POLL_INTERVAL);
        }
    });

    // Premier poll après 30 s puis intervalles réguliers
    pollTimer = setInterval(poll, POLL_INTERVAL);
})();

const style = document.createElement('style');
style.textContent = `
@keyframes slideInToast { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }
@keyframes bellShake { 0%,100%{transform:rotate(0)} 20%{transform:rotate(-20deg)} 40%{transform:rotate(15deg)} 60%{transform:rotate(-10deg)} 80%{transform:rotate(5deg)} }
`;
document.head.appendChild(style);
@endauth
</script>
@stack('scripts')
</body>
</html>
