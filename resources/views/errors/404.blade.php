@include('errors.layout', [
    'code' => 404,
    'tone' => 'lime',
    'icon' => '<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#9cff3b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/><path d="M8.5 11h5"/></svg>',
    'eyebrow' => 'Página no encontrada',
    'heading' => 'Esta semilla no germinó',
    'message' => 'La página que buscas no existe, cambió de dirección o el proyecto ya no está publicado.',
    'status' => 'not found',
    'primary' => ['Explorar proyectos', url('/proyectos')],
])
