@props(['name', 'maxWidth' => 'max-w-lg', 'title' => null, 'bare' => false])
{{-- Modal reutilizable. Se abre con $store.modal.open('nombre') y se cierra con Esc o clic fuera. --}}
<div x-cloak
     x-show="$store.modal.is('{{ $name }}')"
     x-transition.opacity.duration.200ms
     @keydown.escape.window="$store.modal.is('{{ $name }}') && $store.modal.close()"
     class="fixed inset-0 z-[100] flex items-end justify-center bg-seed-950/85 p-3 backdrop-blur-md sm:items-center sm:p-6"
     role="dialog" aria-modal="true" @if($title) aria-label="{{ $title }}" @endif>
    <div x-show="$store.modal.is('{{ $name }}')"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         @click.outside="$store.modal.close()"
         {{ $attributes->merge(['class' => "relative w-full $maxWidth ".($bare ? '' : 'rounded-2xl border border-seed-lime/25 bg-gradient-to-b from-seed-700 to-seed-850 p-6 shadow-seed sm:p-8')]) }}>
        <button type="button" class="absolute right-3 top-3 z-10 grid h-9 w-9 place-items-center rounded-lg text-seed-muted transition hover:bg-white/5 hover:text-white" @click="$store.modal.close()" aria-label="Cerrar">
            <x-icon name="x" :size="18" />
        </button>
        @if($title)<h2 class="pr-8 text-xl font-extrabold">{{ $title }}</h2>@endif
        {{ $slot }}
    </div>
</div>
