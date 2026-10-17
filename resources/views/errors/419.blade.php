@include('errors.layout', [
    'code' => 419,
    'tone' => 'cyan',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#2ee6d6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
    'eyebrow' => 'Sesión expirada',
    'heading' => 'La página expiró',
    'message' => 'Pasó mucho tiempo sin actividad y por seguridad el formulario se reinició. Vuelve atrás, recarga la página e inténtalo otra vez.',
    'status' => 'csrf token expired',
])
