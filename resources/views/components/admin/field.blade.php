@props([
    'name',                 // clave en form (form.name)
    'label',
    'type' => 'text',       // text | textarea | number | date | datetime | url | email | select
    'options' => [],        // para select: [valor => etiqueta]
    'placeholder' => '',
    'hint' => null,
    'required' => false,
    'rows' => 4,
    'full' => false,        // ocupa las 2 columnas del formulario
    'model' => null,        // expresión x-model alternativa
    'icon' => null,
])
@php
    $model ??= "form.$name";
    $id = 'f-'.str_replace(['.', '_'], '-', $name);
    $inputType = ['datetime' => 'datetime-local'][$type] ?? $type;
@endphp
<div {{ $attributes->merge(['class' => $full ? 'sm:col-span-2' : '']) }}>
    <label class="form-label" for="{{ $id }}">{{ $label }} @if($required)<span class="text-seed-lime">*</span>@endif</label>
    <div class="relative">
        @if($icon)
            <x-icon :name="$icon" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
        @endif
        @if($type === 'textarea')
            <textarea id="{{ $id }}" rows="{{ $rows }}" x-model="{{ $model }}" placeholder="{{ $placeholder }}" @if($required) required @endif
                      class="form-input resize-y" :class="err('{{ $name }}') && '!border-seed-coral/60'"></textarea>
        @elseif($type === 'select')
            <select id="{{ $id }}" x-model="{{ $model }}" @if($required) required @endif class="form-input appearance-none pr-10" :class="err('{{ $name }}') && '!border-seed-coral/60'">
                @foreach($options as $value => $text)
                    <option value="{{ $value }}">{{ $text }}</option>
                @endforeach
            </select>
            <x-icon name="chevron-down" :size="15" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-seed-dim" />
        @else
            <input id="{{ $id }}" type="{{ $inputType }}" x-model="{{ $model }}" placeholder="{{ $placeholder }}" @if($required) required @endif
                   @if($type === 'number') min="0" @endif
                   class="form-input {{ $icon ? 'form-input-icon' : '' }}" :class="err('{{ $name }}') && '!border-seed-coral/60'">
        @endif
    </div>
    <p class="form-error" x-show="err('{{ $name }}')" x-text="err('{{ $name }}')"></p>
    @if($hint)<p class="form-hint" x-show="!err('{{ $name }}')">{{ $hint }}</p>@endif
</div>
