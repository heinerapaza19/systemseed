@props([
    'name',                 // campo del formulario (form.name) o usa model
    'label' => 'Imagen',
    'folder' => 'projects', // technologies | skills | projects | gallery
    'model' => null,
    'shape' => 'wide',      // wide (portada) | square (icono)
    'hint' => null,
    'full' => false,
])
@php
    $model ??= "form.$name";
    $id = 'img-'.str_replace(['.', '_'], '-', $name).'-'.substr(md5(uniqid('', true)), 0, 4);
@endphp
{{-- Imagen por URL o subida desde el PC (arrastrar y soltar o botón). --}}
<div {{ $attributes->merge(['class' => $full ? 'sm:col-span-2' : '']) }}
     x-data="imageInput(@js($folder), v => {{ $model }} = v, () => {{ $model }})">
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    <div class="flex gap-3 {{ $shape === 'square' ? 'items-center' : 'flex-col sm:flex-row sm:items-stretch' }}"
         @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="drop($event)">
        {{-- Vista previa / zona para soltar --}}
        <button type="button" @click="$refs.file.click()"
                class="group relative grid shrink-0 place-items-center overflow-hidden rounded-xl border border-dashed transition {{ $shape === 'square' ? 'h-16 w-16' : 'h-28 w-full sm:w-44' }}"
                :class="dragging ? 'border-seed-lime bg-seed-lime/10' : 'border-white/15 bg-seed-900/50 hover:border-seed-teal/60'"
                :aria-label="value ? 'Cambiar imagen' : 'Subir imagen'">
            <template x-if="value">
                <img :src="value" alt="" class="h-full w-full {{ $shape === 'square' ? 'object-contain p-2' : 'object-cover' }}">
            </template>
            <span x-show="!value" class="flex flex-col items-center gap-1 text-seed-dim transition group-hover:text-seed-teal">
                <x-icon name="image" :size="$shape === 'square' ? 20 : 24" />
                @if($shape !== 'square')<span class="text-[11px] font-semibold">Arrastra o elige</span>@endif
            </span>
            <span x-show="uploading" x-cloak class="absolute inset-0 grid place-items-center bg-seed-900/80">
                <span class="h-6 w-6 animate-spin rounded-full border-2 border-seed-lime/20 border-t-seed-lime"></span>
            </span>
        </button>

        <div class="min-w-0 flex-1 space-y-2">
            <div class="flex gap-2">
                <button type="button" class="btn btn-sm btn-cool shrink-0" @click="$refs.file.click()" :disabled="uploading">
                    <x-icon name="download" :size="14" class="rotate-180" />
                    <span x-text="uploading ? 'Subiendo…' : 'Subir desde PC'"></span>
                </button>
                <button type="button" x-show="value" class="btn btn-sm btn-ghost shrink-0 border border-white/10" @click="clear()" title="Quitar imagen">
                    <x-icon name="x" :size="14" /> Quitar
                </button>
            </div>
            <div class="relative">
                <x-icon name="globe" :size="15" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input id="{{ $id }}" type="text" x-model="{{ $model }}" placeholder="…o pega una URL (https://…)" class="form-input !py-2.5 !pl-10 text-xs">
            </div>
            <input type="file" x-ref="file" accept="image/png,image/jpeg,image/webp,image/gif" class="sr-only" @change="pick($event)">
            <p class="form-error" x-show="uploadError || err('{{ $name }}')" x-text="uploadError || err('{{ $name }}')"></p>
            <p class="form-hint" x-show="!uploadError && !err('{{ $name }}')">{{ $hint ?? 'JPG, PNG, WEBP o GIF · máx. 4 MB' }}</p>
        </div>
    </div>
</div>
