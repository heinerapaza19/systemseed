@props(['name', 'id' => null, 'autocomplete' => 'current-password', 'model' => null, 'placeholder' => '••••••••'])
@php($id ??= $name)
<div class="relative" x-data="{ visible: false }">
    <x-icon name="key" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
    <input :type="visible ? 'text' : 'password'" type="password" id="{{ $id }}" name="{{ $name }}" autocomplete="{{ $autocomplete }}" placeholder="{{ $placeholder }}"
           @if($model) x-model="{{ $model }}" @endif
           {{ $attributes->merge(['class' => 'form-input form-input-icon pr-12']) }}>
    <button type="button" class="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-lg text-seed-dim transition hover:text-seed-lime"
            @click="visible = !visible" :aria-label="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'">
        <x-icon name="eye" :size="17" x-show="!visible" />
        <x-icon name="eye-off" :size="17" x-show="visible" x-cloak />
    </button>
</div>
