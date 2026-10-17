/**
 * Cliente HTTP para /api/v1.
 *
 * La autenticación se hace con la sesión web de Laravel (Sanctum stateful):
 * el navegador envía la cookie de sesión y el token XSRF de forma automática.
 * Nunca se guardan ni se exponen tokens Bearer en el frontend.
 */
export const api = window.axios.create({
    baseURL: '/api/v1',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
});

export const unwrap = response => response?.data?.data ?? [];

export const errorMessage = error => {
    const status = error?.response?.status;
    const data = error?.response?.data;

    if (data?.errors) {
        const first = Object.values(data.errors)[0];
        if (Array.isArray(first) && first.length) return first[0];
    }
    if (status === 401) return 'Tu sesión expiró. Inicia sesión nuevamente.';
    if (status === 403) return data?.message === 'Cuenta inactiva' ? 'Tu cuenta está inactiva.' : 'No tienes permiso para realizar esta acción.';
    if (status === 404) return 'El recurso solicitado no está disponible.';
    if (status === 429) return 'Demasiados intentos. Espera un momento e inténtalo otra vez.';
    if (!error?.response) return 'No pudimos conectar con el servidor. Revisa tu conexión.';

    return data?.message || 'No pudimos completar la solicitud. Inténtalo nuevamente.';
};
