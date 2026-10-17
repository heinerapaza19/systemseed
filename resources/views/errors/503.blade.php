@include('errors.layout', [
    'code' => 503,
    'tone' => 'cyan',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#2ee6d6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z"/></svg>',
    'eyebrow' => 'Mantenimiento',
    'heading' => 'Estamos mejorando SystemSeed',
    'message' => 'El sitio está en mantenimiento por unos minutos. Vuelve pronto: estamos sembrando novedades.',
    'status' => 'maintenance mode',
    'hideBack' => true,
])
