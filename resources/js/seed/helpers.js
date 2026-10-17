import { api, errorMessage } from './api';

const config = () => window.SystemSeed ?? { auth: false, user: null };

export const isAuthenticated = () => Boolean(config().auth);

/** Login conservando la página actual para volver después de iniciar sesión. */
export const loginUrl = (path = window.location.pathname) => `/login?redirect=${encodeURIComponent(path)}`;

/* ---------- Portadas generadas (colores solo de la paleta SystemSeed) ---------- */
const COVER_PALETTES = [
    ['#b8ff39', '#0a3a2a'], // lima
    ['#18e2c3', '#063238'], // turquesa
    ['#2be7ff', '#062a3a'], // cian
    ['#ffc83d', '#2d2a0e'], // dorado
    ['#64ff58', '#0a3324'], // verde
];

export const coverStyle = project => {
    const index = Math.abs(Number(project?.id ?? 0)) % COVER_PALETTES.length;
    const [c1, c2] = COVER_PALETTES[index];
    return `--c1:${c1};--c2:${c2}`;
};

export const monogram = (text = '') => text
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map(word => word[0])
    .join('')
    .toUpperCase() || 'SS';

/* ---------- Estado del proyecto ---------- */
const STATUS = {
    published: { label: 'Publicado', tone: 'chip-lime' },
    draft: { label: 'En desarrollo', tone: 'chip-gold' },
    archived: { label: 'Archivado', tone: 'chip-muted' },
};
export const statusLabel = status => STATUS[status]?.label ?? 'Publicado';
export const statusTone = status => STATUS[status]?.tone ?? 'chip-lime';

/* ---------- Tecnologías ---------- */
const TECH_COLORS = {
    backend: '#b8ff39',
    frontend: '#2be7ff',
    database: '#18e2c3',
    mobile: '#ffc83d',
    devops: '#64ff58',
    tools: '#ffc83d',
};
export const techColor = category => TECH_COLORS[String(category ?? '').toLowerCase()] ?? '#a5b7b6';
export const techLabel = category => ({
    backend: 'Backend', frontend: 'Frontend', database: 'Base de datos', mobile: 'Móvil', devops: 'DevOps', tools: 'Herramientas', other: 'Otros',
})[String(category ?? '').toLowerCase()] ?? (category || 'General');

/* ---------- Skills: el nivel es texto libre ("Avanzado", "85", "85%") ---------- */
const LEVELS = { basico: 35, básico: 35, principiante: 30, intermedio: 60, avanzado: 85, experto: 95 };
export const skillLevel = level => {
    const raw = String(level ?? '').trim();
    const numeric = parseInt(raw, 10);
    if (!Number.isNaN(numeric)) {
        const pct = Math.max(5, Math.min(100, numeric));
        return { pct, label: `${pct}%` };
    }
    const pct = LEVELS[raw.toLowerCase()] ?? null;
    return { pct, label: raw ? raw.charAt(0).toUpperCase() + raw.slice(1) : '' };
};

/* ---------- Fechas ---------- */
export const formatDate = value => {
    if (!value) return '';
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('es-PE', { day: 'numeric', month: 'short', year: 'numeric' });
};

export const formatMonth = value => {
    if (!value) return '';
    const [year, month] = String(value).split('-').map(Number);
    if (!year) return '';
    const date = new Date(year, (month || 1) - 1, 1);
    const text = date.toLocaleDateString('es-PE', { month: 'short', year: 'numeric' }).replace('.', '');
    return text.charAt(0).toUpperCase() + text.slice(1);
};

export const period = item => `${formatMonth(item?.start_date)} — ${item?.is_current ? 'Actualidad' : (formatMonth(item?.end_date) || 'Actualidad')}`;

export const timeAgo = value => {
    if (!value) return '';
    const date = new Date(value);
    const seconds = Math.round((date.getTime() - Date.now()) / 1000);
    const units = [['year', 31536000], ['month', 2592000], ['week', 604800], ['day', 86400], ['hour', 3600], ['minute', 60]];
    const rtf = new Intl.RelativeTimeFormat('es', { numeric: 'auto' });
    for (const [unit, size] of units) {
        if (Math.abs(seconds) >= size) return rtf.format(Math.round(seconds / size), unit);
    }
    return 'hace un momento';
};

export const greeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Buenos días';
    if (hour < 19) return 'Buenas tardes';
    return 'Buenas noches';
};

/* ---------- Toasts ---------- */
export const toast = (message, type = 'success') => window.Alpine?.store('toasts')?.push(message, type);

/* ---------- Recursos protegidos ---------- */
const RESOURCE = {
    demo: { path: 'demo', label: 'la demo' },
    download: { path: 'download', label: 'la descarga' },
    source: { path: 'source-code', label: 'el código' },
};

const safeUrl = url => {
    try {
        const parsed = new URL(url, window.location.origin);
        return ['http:', 'https:'].includes(parsed.protocol) ? parsed.href : null;
    } catch {
        return null;
    }
};

/**
 * Abre un recurso del proyecto (demo / descarga / código) pidiendo la URL al
 * backend en el momento del clic. La URL nunca se imprime en el HTML: el
 * backend decide si el usuario tiene permiso.
 */
export const openProjectResource = async (project, type = 'demo') => {
    const resource = RESOURCE[type];
    if (!project?.id || !resource) return;

    // Se abre la pestaña de forma síncrona para que el navegador no la bloquee.
    const tab = window.open('about:blank', '_blank');
    if (tab) {
        tab.opener = null;
        tab.document.title = 'Abriendo… | SystemSeed';
        tab.document.body.style.cssText = 'background:#031318;color:#a5b7b6;font:14px Inter,system-ui,sans-serif;display:grid;place-items:center;height:100vh;margin:0';
        tab.document.body.textContent = `Preparando ${resource.label}…`;
    }

    try {
        const response = await api.get(`/projects/${project.id}/${resource.path}`);
        const url = safeUrl(response?.data?.data?.url);
        if (!url) throw new Error('invalid-url');
        if (tab) tab.location.replace(url);
        else window.location.assign(url);
    } catch (error) {
        tab?.close();
        if (error?.response?.status === 401) {
            window.location.href = loginUrl();
            return;
        }
        toast(error?.message === 'invalid-url' ? 'El recurso no tiene un enlace válido.' : errorMessage(error), 'error');
    }
};

/* ---------- WhatsApp ---------- */
/**
 * Obtiene el número de WhatsApp desde el perfil (campo whatsapp_url).
 * Acepta "https://wa.me/51987654321", "https://api.whatsapp.com/send?phone=51987654321" o "+51 987 654 321".
 */
/** Número de WhatsApp de SystemSeed (Perú +51). Se usa si el perfil no tiene uno registrado. */
export const DEFAULT_WHATSAPP = '51980814183';

export const whatsappNumber = value => {
    const raw = String(value || DEFAULT_WHATSAPP);
    const fromPhoneParam = raw.match(/phone=(\+?\d[\d\s-]*)/);
    const digits = (fromPhoneParam ? fromPhoneParam[1] : raw).replace(/\D/g, '');
    return digits.length >= 8 && digits.length <= 15 ? digits : null;
};

export const whatsappLink = (number, text = '') => `https://wa.me/${number}${text ? `?text=${encodeURIComponent(text)}` : ''}`;

/** ¿El valor es una URL/ruta de imagen? (para iconos que pueden ser imagen o texto) */
export const isImageUrl = value => typeof value === 'string' && /^(https?:\/\/|\/)/.test(value.trim());
