@include('errors.layout', [
    'code' => 401,
    'tone' => 'cyan',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#2ee6d6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>',
    'eyebrow' => 'Acceso restringido',
    'heading' => 'Necesitas iniciar sesión',
    'message' => 'Esta sección es solo para miembros. Inicia sesión o crea tu cuenta gratuita para continuar.',
    'status' => 'auth required',
    'primary' => ['Iniciar sesión', route('login')],
])
