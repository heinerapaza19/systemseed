@props(['bind' => null, 'name' => null, 'category' => null, 'size' => 'sm'])
{{--
    Insignia de tecnología con punto de color según categoría.
      Estático:  <x-technology-badge name="Laravel" category="backend" />
      Alpine:    <x-technology-badge bind="tech" />   (tech = objeto de /api/v1/technologies)
--}}
@php($sizeClass = $size === 'md' ? '!px-3 !py-1.5 !text-xs' : '')
@if($bind)
    <span {{ $attributes->merge(['class' => "tech-badge $sizeClass"]) }} :title="$seed.techLabel({{ $bind }}.category)">
        <span class="tech-dot" :style="`background:${$seed.techColor({{ $bind }}.category)}`"></span>
        <span x-text="{{ $bind }}.name"></span>
    </span>
@else
    <span {{ $attributes->merge(['class' => "tech-badge $sizeClass"]) }} title="{{ $category }}">
        <span class="tech-dot" style="background: {{ ['backend' => '#b8ff39', 'frontend' => '#2be7ff', 'database' => '#18e2c3', 'mobile' => '#ffc83d', 'devops' => '#64ff58'][strtolower((string) $category)] ?? '#a5b7b6' }}"></span>
        {{ $name ?? $slot }}
    </span>
@endif
