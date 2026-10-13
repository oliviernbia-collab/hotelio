@if ($paginator->hasPages())
@once
<style>
/* ══ PAGINATION HÔTELIO ══ */
.hpag{
  display:flex;align-items:center;justify-content:center;
  gap:.5rem;padding:2rem 0 .5rem;flex-wrap:wrap;
  font-family:'Plus Jakarta Sans',sans-serif;
}
.hpag-arrow{
  display:inline-flex;align-items:center;gap:.5rem;
  padding:.6rem 1.25rem;border-radius:40px;
  border:1.5px solid #E8E0D4;
  font-size:.82rem;font-weight:600;color:#4A4A4A;
  background:white;text-decoration:none;
  transition:all .2s;white-space:nowrap;
  box-shadow:0 1px 4px rgba(12,35,64,.06);
}
.hpag-arrow:hover:not(.hpag-disabled){
  border-color:#F77F00;color:#F77F00;
  background:rgba(247,127,0,.06);
  box-shadow:0 4px 12px rgba(247,127,0,.18);
  transform:translateY(-1px);
}
.hpag-disabled{opacity:.38;cursor:default;pointer-events:none;}
.hpag-nums{display:flex;align-items:center;gap:.3rem;}
.hpag-page{
  width:38px;height:38px;border-radius:10px;
  border:1.5px solid #E8E0D4;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:.82rem;font-weight:600;color:#4A4A4A;
  background:white;text-decoration:none;
  transition:all .2s;
}
.hpag-page:hover{border-color:#F77F00;color:#F77F00;background:rgba(247,127,0,.06);}
.hpag-page.hpag-current{
  background:#F77F00;border-color:#F77F00;color:white;
  cursor:default;box-shadow:0 4px 12px rgba(247,127,0,.35);
}
.hpag-dots{
  width:38px;height:38px;
  display:inline-flex;align-items:center;justify-content:center;
  color:#7A7A7A;font-size:.9rem;letter-spacing:.1em;
}
.hpag-info{
  font-size:.78rem;color:#7A7A7A;
  padding:.4rem .8rem;border-radius:8px;
  background:rgba(0,0,0,.04);
}
@media(max-width:540px){
  .hpag-nums{display:none}
  .hpag-arrow{padding:.6rem .9rem}
  .hpag-info{display:none}
}
/* Dark mode */
[data-theme="dark"] .hpag-arrow{background:#1e293b;border-color:rgba(148,163,184,.2);color:#94a3b8;box-shadow:none}
[data-theme="dark"] .hpag-arrow:hover:not(.hpag-disabled){border-color:#F77F00;color:#F77F00;background:rgba(247,127,0,.1)}
[data-theme="dark"] .hpag-page{background:#1e293b;border-color:rgba(148,163,184,.2);color:#94a3b8}
[data-theme="dark"] .hpag-page:hover{border-color:#F77F00;color:#F77F00;background:rgba(247,127,0,.1)}
[data-theme="dark"] .hpag-dots{color:#475569}
[data-theme="dark"] .hpag-info{background:rgba(255,255,255,.06);color:#64748b}
</style>
@endonce

<nav class="hpag" aria-label="{{ __('Pagination') }}">

  {{-- ← Précédent --}}
  @if ($paginator->onFirstPage())
    <span class="hpag-arrow hpag-disabled" aria-disabled="true">
      <i class="fa-solid fa-arrow-left" style="font-size:.7rem"></i>
      <span>{{ __('Précédent') }}</span>
    </span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" class="hpag-arrow" rel="prev" aria-label="{{ __('Page précédente') }}">
      <i class="fa-solid fa-arrow-left" style="font-size:.7rem"></i>
      <span>{{ __('Précédent') }}</span>
    </a>
  @endif

  {{-- Numéros de page --}}
  <div class="hpag-nums" role="list">
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="hpag-dots" aria-hidden="true">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="hpag-page hpag-current" aria-current="page" aria-label="{{ __('Page') }} {{ $page }}">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="hpag-page" aria-label="{{ __('Page') }} {{ $page }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach
  </div>

  {{-- Info page (desktop) --}}
  <span class="hpag-info">
    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
  </span>

  {{-- Suivant → --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="hpag-arrow" rel="next" aria-label="{{ __('Page suivante') }}">
      <span>{{ __('Suivant') }}</span>
      <i class="fa-solid fa-arrow-right" style="font-size:.7rem"></i>
    </a>
  @else
    <span class="hpag-arrow hpag-disabled" aria-disabled="true">
      <span>{{ __('Suivant') }}</span>
      <i class="fa-solid fa-arrow-right" style="font-size:.7rem"></i>
    </span>
  @endif

</nav>
@endif
