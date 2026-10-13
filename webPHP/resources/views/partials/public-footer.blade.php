<footer style="background:#061422;color:rgba(255,255,255,.85);padding:64px 56px 40px;margin-top:80px;">
  <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;margin-bottom:56px;max-width:1280px;margin-left:auto;margin-right:auto;">

    {{-- MARQUE --}}
    <div>
      <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:300;color:white;display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <span style="width:36px;height:36px;background:#F77F00;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg viewBox="0 0 24 24" width="20" height="20"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" fill="white"/><polyline points="9,22 9,12 15,12 15,22" fill="white"/></svg>
        </span>
        Hôtelio
      </div>
      <p style="font-size:14px;font-weight:300;line-height:1.7;color:rgba(255,255,255,.55);margin-bottom:24px;max-width:280px;">
        {{ __("Trouvez votre hébergement idéal parmi des milliers d'hôtels et de propriétés à travers le monde.") }}
      </p>
      @auth
        <a href="{{ auth()->user()->role === 'admin' ? route('admin.index') : (auth()->user()->role === 'prestataire' ? route('prestataire.index') : route('client.index')) }}"
           style="display:inline-flex;align-items:center;gap:8px;background:#F77F00;color:white;padding:11px 22px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;transition:all .25s;"
           onmouseover="this.style.background='#e06e00';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.background='#F77F00';this.style.transform='none'">
          <i class="fa-solid fa-gauge-high" style="font-size:12px"></i> {{ __('Mon Dashboard') }}
        </a>
      @else
        <a href="{{ route('register') }}"
           style="display:inline-flex;align-items:center;gap:8px;background:#F77F00;color:white;padding:11px 22px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;transition:all .25s;"
           onmouseover="this.style.background='#e06e00';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.background='#F77F00';this.style.transform='none'">
          {{ __('Découvrir') }} →
        </a>
      @endauth
    </div>

    {{-- COL HÔTELS --}}
    <div>
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;color:orange;margin-bottom:20px;">{{ __('Hôtels') }}</div>
      <ul style="list-style:none;display:flex;flex-direction:column;gap:12px;">
        <li><a href="{{ route('hotels.public') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Hôtels populaires') }}</a></li>
        <li><a href="{{ route('hotels.public', ['categorie' => 'Luxe']) }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Villas de luxe') }}</a></li>
        <li><a href="{{ route('hotels.public') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Appartements') }}</a></li>
        <li><a href="{{ route('hotels.public') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Maisons de vacances') }}</a></li>
      </ul>
    </div>

    {{-- COL SUPPORT --}}
    <div>
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;color:orange;margin-bottom:20px;">{{ __('Support') }}</div>
      <ul style="list-style:none;display:flex;flex-direction:column;gap:12px;">
        <li><a href="{{ route('register', ['role' => 'prestataire']) }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Mettre en location') }}</a></li>
        <li><a href="{{ route('pages.show', 'ressources-hotes') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Ressources hôtes') }}</a></li>
        <li><a href="{{ route('pages.show', 'forum') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Forum communauté') }}</a></li>
        <li><a href="{{ route('pages.show', 'assistance-hotes') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Assistance hôtes') }}</a></li>
      </ul>
    </div>

    {{-- COL LÉGAL --}}
    <div>
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;color:orange;margin-bottom:20px;">{{ __('Légal') }}</div>
      <ul style="list-style:none;display:flex;flex-direction:column;gap:12px;">
        <li><a href="{{ route('pages.show', 'centre-aide') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __("Centre d'aide") }}</a></li>
        <li><a href="{{ route('pages.show', 'securite') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Sécurité') }}</a></li>
        <li><a href="{{ route('pages.show', 'conditions-utilisation') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __("Conditions d'utilisation") }}</a></li>
        <li><a href="{{ route('pages.show', 'confidentialite') }}" style="font-size:14px;color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.5)'">{{ __('Politique de confidentialité') }}</a></li>
      </ul>
    </div>
  </div>

  {{-- BAS DU FOOTER --}}
  <div style="display:flex;justify-content:space-between;align-items:center;padding-top:24px;border-top:1px solid rgba(255,255,255,.08);font-size:12px;color:rgba(255,255,255,.3);flex-wrap:wrap;gap:12px;max-width:1280px;margin:0 auto;">
    <span>© 2026 Hôtelio. {{ __('Tous droits réservés.') }}</span>
    <div style="display:flex;gap:12px;">
      <a href="{{ route('locale.switch', 'fr') }}" style="font-size:12px;text-decoration:none;padding:3px 8px;border-radius:5px;transition:color .2s;color:{{ app()->getLocale()==='fr' ? '#F77F00' : 'rgba(255,255,255,.35)' }};font-weight:{{ app()->getLocale()==='fr' ? '600' : '400' }};">🇫🇷 Français</a>
      <a href="{{ route('locale.switch', 'en') }}" style="font-size:12px;text-decoration:none;padding:3px 8px;border-radius:5px;transition:color .2s;color:{{ app()->getLocale()==='en' ? '#F77F00' : 'rgba(255,255,255,.35)' }};font-weight:{{ app()->getLocale()==='en' ? '600' : '400' }};">🇬🇧 English</a>
      <a href="{{ route('locale.switch', 'es') }}" style="font-size:12px;text-decoration:none;padding:3px 8px;border-radius:5px;transition:color .2s;color:{{ app()->getLocale()==='es' ? '#F77F00' : 'rgba(255,255,255,.35)' }};font-weight:{{ app()->getLocale()==='es' ? '600' : '400' }};">🇪🇸 Español</a>
    </div>
  </div>
</footer>
