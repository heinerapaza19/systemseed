@props(['user' => null, 'size' => 'md'])
@php
    $user ??= auth()->user();
    $sizes = ['sm' => 'h-8 w-8 text-xs', 'md' => 'h-10 w-10 text-sm', 'lg' => 'h-14 w-14 text-lg', 'xl' => 'h-20 w-20 text-2xl'];
    $initials = collect(preg_split('/\s+/', trim($user?->name ?? 'U')))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $gradient = $user?->isAdmin() ? 'from-seed-green to-seed-lime' : 'from-seed-teal to-seed-cyan';
@endphp
@if($user?->avatar && str_starts_with($user->avatar, 'https://'))
    <img src="{{ $user->avatar }}" alt="" referrerpolicy="no-referrer" {{ $attributes->merge(['class' => "{$sizes[$size]} shrink-0 rounded-full object-cover ring-2 ring-seed-teal/30"]) }}>
@else
    <span {{ $attributes->merge(['class' => "{$sizes[$size]} grid shrink-0 place-items-center rounded-full bg-gradient-to-br $gradient font-black text-seed-900 ring-2 ring-white/5"]) }} aria-hidden="true">{{ $initials ?: 'U' }}</span>
@endif
