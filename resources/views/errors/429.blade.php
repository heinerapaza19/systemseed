@include('errors.layout', [
    'code' => 429,
    'tone' => 'gold',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#ffc83d" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7z"/></svg>',
    'eyebrow' => 'Demasiados intentos',
    'heading' => 'Ve un poco más despacio',
    'message' => 'Recibimos muchas solicitudes seguidas. Espera un minuto y vuelve a intentarlo.',
    'status' => 'too many requests',
])
