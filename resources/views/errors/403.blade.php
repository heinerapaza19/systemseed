@include('errors.layout', [
    'code' => 403,
    'tone' => 'gold',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#ffc83d" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>',
    'eyebrow' => 'Sin permiso',
    'heading' => 'No tienes acceso a esta página',
    'message' => 'Tu cuenta no tiene permisos para ver este contenido. Si crees que es un error, escríbenos desde la sección de contacto.',
    'status' => 'forbidden',
])
