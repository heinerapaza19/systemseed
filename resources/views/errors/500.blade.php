@include('errors.layout', [
    'code' => 500,
    'tone' => 'gold',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#ffc83d" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 20h20z"/><path d="M12 10v4"/><path d="M12 17h.01"/></svg>',
    'eyebrow' => 'Error del servidor',
    'heading' => 'Algo salió mal de nuestro lado',
    'message' => 'Ocurrió un error inesperado. Ya quedó registrado; inténtalo de nuevo en unos minutos.',
    'status' => 'internal error',
])
