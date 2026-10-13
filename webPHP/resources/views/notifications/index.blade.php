@extends('layouts.app')

@section('content')
<style>
.notif-item{
  display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.25rem;
  border-radius:14px;border:1px solid var(--border);
  background:var(--bg-card);margin-bottom:.6rem;
  transition:background .18s,border-color .18s;
}
.notif-item.unread{
  border-left:3px solid var(--ci-orange);
  background:rgba(247,127,0,.05);
}
.notif-item:hover{border-color:var(--ci-orange-border)}
.notif-icon{
  width:42px;height:42px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;flex-shrink:0;
}
.notif-body{flex:1;min-width:0}
.notif-title{font-weight:700;font-size:.9rem;color:var(--text-primary);margin-bottom:3px}
.notif-msg{font-size:.82rem;color:var(--text-secondary);line-height:1.5;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:600px}
.notif-time{font-size:.72rem;color:var(--text-muted);margin-top:4px}
.notif-unread-dot{
  width:8px;height:8px;border-radius:50%;
  background:var(--ci-orange);flex-shrink:0;margin-top:6px;
}
.notif-actions{display:flex;gap:.4rem;flex-shrink:0;align-items:center}
.btn-notif{
  width:32px;height:32px;border-radius:8px;border:1px solid var(--border);
  background:var(--bg-input);color:var(--text-secondary);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:.78rem;transition:all .2s;
}
.btn-notif:hover{border-color:var(--ci-orange);color:var(--ci-orange)}
.btn-notif.del:hover{border-color:#e63946;color:#e63946}
</style>

<!-- Header -->
<div class="card" style="margin-bottom:1.25rem">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
    <div>
      <div style="font-size:1.15rem;font-weight:700">
        <i class="fa-regular fa-bell" style="color:var(--ci-orange)"></i>
        {{ __('Mes Notifications') }}
      </div>
      <p style="margin:.25rem 0 0;color:var(--text-secondary);font-size:.85rem">
        {{ $unreadCount }} {{ __('non lue(s)') }} · {{ $notifications->total() }} {{ __('au total') }}
      </p>
    </div>
    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
      @if($unreadCount > 0)
      <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn-secondary" style="font-size:.82rem">
          <i class="fa-solid fa-check-double"></i> {{ __('Tout marquer comme lu') }}
        </button>
      </form>
      @endif
      @if($notifications->total() > 0)
      <form method="POST" action="{{ route('notifications.destroy-all') }}"
            onsubmit="return confirm('{{ __('Supprimer toutes les notifications ?') }}')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-secondary" style="font-size:.82rem;color:#fca5a5;border-color:rgba(230,57,70,.3)">
          <i class="fa-solid fa-trash"></i> {{ __('Tout supprimer') }}
        </button>
      </form>
      @endif
    </div>
  </div>
</div>

@forelse($notifications as $notif)
@php
  $data  = $notif->data;
  $read  = !is_null($notif->read_at);
  $color = $data['color'] ?? 'var(--ci-orange)';
  $icon  = $data['icon']  ?? 'fa-bell';
@endphp
<div class="notif-item {{ $read ? '' : 'unread' }}">

  <!-- Icône -->
  <div class="notif-icon" style="background:{{ $color }}18;color:{{ $color }}">
    <i class="fa-solid {{ $icon }}"></i>
  </div>

  <!-- Contenu -->
  <div class="notif-body">
    <div class="notif-title">{{ $data['title'] ?? __('Notification') }}</div>
    <div class="notif-msg">{{ $data['message'] ?? '' }}</div>
    @if(!empty($data['amount']))
    <span style="font-size:.78rem;font-weight:700;color:var(--ci-green)">
      {{ number_format($data['amount'], 0, ',', ' ') }} FCFA
    </span>
    @endif
    <div class="notif-time">
      <i class="fa-regular fa-clock"></i> {{ $notif->created_at->diffForHumans() }}
      @if($read) · <span style="color:var(--ci-green)"><i class="fa-solid fa-check"></i> {{ __('Lu') }}</span>
      @else · <span style="color:var(--ci-orange);font-weight:600">{{ __('Nouveau') }}</span>
      @endif
    </div>
  </div>

  <!-- Point non-lu -->
  @if(!$read)
  <div class="notif-unread-dot"></div>
  @endif

  <!-- Actions -->
  <div class="notif-actions">
    @if(!empty($data['url']))
    <a href="{{ $data['url'] }}" class="btn-notif" title="{{ __('Voir') }}">
      <i class="fa-solid fa-arrow-right"></i>
    </a>
    @endif

    @if(!$read)
    <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
      @csrf @method('PATCH')
      <button type="submit" class="btn-notif" title="{{ __('Marquer comme lu') }}">
        <i class="fa-solid fa-check"></i>
      </button>
    </form>
    @endif

    <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}"
          onsubmit="return confirm('{{ __('Supprimer cette notification ?') }}')">
      @csrf @method('DELETE')
      <button type="submit" class="btn-notif del" title="{{ __('Supprimer') }}">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </form>
  </div>
</div>
@empty
<div class="card" style="text-align:center;padding:3rem">
  <i class="fa-regular fa-bell-slash" style="font-size:2.5rem;color:var(--text-muted);margin-bottom:1rem"></i>
  <div style="font-size:1rem;font-weight:600;margin-bottom:.4rem">{{ __('Aucune notification') }}</div>
  <p style="color:var(--text-secondary)">{{ __('Vous êtes à jour ! Les nouvelles notifications apparaîtront ici.') }}</p>
</div>
@endforelse

<!-- Pagination -->
@if($notifications->hasPages())
<div style="margin-top:1.25rem;display:flex;justify-content:center">
  {{ $notifications->links() }}
</div>
@endif
@endsection
