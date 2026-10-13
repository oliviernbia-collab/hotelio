@extends('layouts.app')

@section('content')

{{-- HEADER --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-title">
        <div>
            <div style="font-size:1.2rem;font-weight:700"><i class="fa-solid fa-star-half-stroke" style="color:var(--ci-orange)"></i> {{ __('Modération des avis') }}</div>
            <p style="margin:.3rem 0 0;color:var(--text-secondary);font-size:.88rem;">{{ __('Un avis publié par un client, web ou mobile, doit être validé avant de compter dans la note publique de l\'hôtel.') }}</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            @foreach(['pending' => __('En attente') . ' (' . $counts['pending'] . ')', 'approved' => __('Approuvés') . ' (' . $counts['approved'] . ')', 'rejected' => __('Rejetés') . ' (' . $counts['rejected'] . ')', 'all' => __('Tous')] as $value => $label)
            <a href="{{ route('reviews.index', ['status' => $value]) }}"
               class="{{ $status === $value ? 'btn' : 'btn-secondary' }}"
               style="padding:.5rem 1rem;font-size:.82rem;">{{ $label }}</a>
            @endforeach
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert-success" style="margin-bottom:1.5rem;">
    <i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span>
</div>
@endif

{{-- LISTE --}}
<div class="card">
    @forelse($reviews as $review)
    @php
        $statusMap = [
            'pending'  => ['rgba(245,158,11,.15)', '#f59e0b', __('En attente')],
            'approved' => ['rgba(16,185,129,.15)', '#10b981', __('Approuvé')],
            'rejected' => ['rgba(230,57,70,.12)',  '#fca5a5', __('Rejeté')],
        ];
        [$sbg, $sfg, $slbl] = $statusMap[$review->status] ?? ['rgba(148,163,184,.1)', '#94a3b8', $review->status];
    @endphp
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:14px;background:var(--bg-input);border:1px solid var(--border);margin-bottom:.75rem;">

        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-bottom:.35rem;">
                <span style="font-weight:700;color:var(--text-primary);font-size:.92rem;">{{ $review->user->name ?? __('Utilisateur supprimé') }}</span>
                <span style="color:var(--text-muted);font-size:.8rem;">· <i class="fa-solid fa-hotel" style="opacity:.6;"></i> {{ $review->hotel->name ?? '—' }}</span>
                <span style="color:#f4a423;font-size:.8rem;">
                    @for($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>@endfor
                </span>
                <span style="display:inline-flex;align-items:center;padding:.2rem .6rem;border-radius:999px;font-size:.7rem;font-weight:700;background:{{ $sbg }};color:{{ $sfg }};">
                    {{ $slbl }}
                </span>
            </div>
            @if($review->title)
            <div style="font-weight:600;color:var(--text-primary);font-size:.88rem;margin-bottom:.15rem;">{{ $review->title }}</div>
            @endif
            <div style="font-size:.85rem;color:var(--text-secondary);">{{ $review->comment }}</div>
            <div style="font-size:.74rem;color:var(--text-muted);margin-top:.4rem;">{{ $review->created_at?->format('d/m/Y H:i') }}</div>
        </div>

        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;">
            @if($review->status !== 'approved')
            <form method="POST" action="{{ route('reviews.approve', $review->id) }}" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .75rem;border-radius:10px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.12);color:#10b981;font-size:.78rem;font-weight:600;font-family:inherit;cursor:pointer;">
                    <i class="fa-solid fa-check"></i> {{ __('Approuver') }}
                </button>
            </form>
            @endif
            @if($review->status !== 'rejected')
            <form method="POST" action="{{ route('reviews.reject', $review->id) }}" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .75rem;border-radius:10px;border:1px solid rgba(230,57,70,.3);background:rgba(230,57,70,.1);color:#fca5a5;font-size:.78rem;font-weight:600;font-family:inherit;cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i> {{ __('Rejeter') }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3.5rem;color:var(--text-muted);">
        <i class="fa-solid fa-star-half-stroke" style="font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;"></i>
        <p style="font-weight:500;">{{ __('Aucun avis dans cette catégorie.') }}</p>
    </div>
    @endforelse

    @if($reviews->hasPages())
    <div style="margin-top:1.25rem;display:flex;justify-content:center;">{{ $reviews->links() }}</div>
    @endif
</div>

@endsection
