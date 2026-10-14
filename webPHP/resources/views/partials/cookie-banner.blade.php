<div id="cookieBanner" style="
    display:none; position:fixed; left:0; right:0; bottom:0; z-index:9999;
    background:#0f172a; color:#e2e8f0; padding:1rem 1.25rem;
    align-items:center; gap:1rem; flex-wrap:wrap;
    box-shadow:0 -4px 24px rgba(0,0,0,.25); font-size:.85rem;
">
    <div style="flex:1; min-width:240px; line-height:1.5;">
        {{ __('Nous utilisons des cookies pour faire fonctionner le site (session, préférences de langue) et mesurer son usage.') }}
        <a href="{{ route('pages.show', 'confidentialite') }}" style="color:#F77F00; text-decoration:underline;">{{ __('En savoir plus') }}</a>
    </div>
    <button type="button" onclick="document.getElementById('cookieBanner').remove(); localStorage.setItem('hotelio_cookie_consent','1');" style="
        background:#F77F00; color:#fff; border:none; border-radius:8px;
        padding:.6rem 1.2rem; font-weight:700; font-size:.85rem; cursor:pointer; flex-shrink:0;
    ">
        {{ __("J'ai compris") }}
    </button>
</div>
<script>
    (function () {
        try {
            if (!localStorage.getItem('hotelio_cookie_consent')) {
                var el = document.getElementById('cookieBanner');
                if (el) el.style.display = 'flex';
            }
        } catch (e) {}
    })();
</script>
