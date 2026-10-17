import { api, errorMessage, unwrap } from './api';
import { openProjectResource, toast } from './helpers';

/**
 * Panel del usuario registrado (SystemSeed User Workspace).
 * Usa solo /api/v1 (projects, technologies, user/*, auth/*, contact) con la sesión web.
 */

const fetchAll = async (path, params = {}, maxPages = 10) => {
    const items = [];
    let page = 1;
    let last = 1;
    do {
        const response = await api.get(path, { params: { ...params, page } });
        items.push(...unwrap(response));
        last = response?.data?.meta?.last_page ?? 1;
        page += 1;
    } while (page <= last && page <= maxPages);
    return items;
};

/** Conserva getters al combinar objetos. */
const extend = (...sources) => sources.reduce(
    (target, source) => Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)),
    {},
);

const MESSAGES = {
    favorites: ['Agregado a tus favoritos.', 'Quitado de favoritos.'],
    templates: ['Plantilla guardada.', 'Plantilla quitada de tus guardados.'],
    following: ['Ahora sigues este proyecto.', 'Dejaste de seguir este proyecto.'],
};

/**
 * Biblioteca del usuario: saber si un proyecto está en favoritos / plantillas / seguidos
 * y alternarlo. Se reutiliza en explorar, dashboard y las listas.
 */
const library = () => ({
    lib: { favorites: [], templates: [], following: [] },
    libBusy: {},
    pendingRemoval: null,

    async loadLibrary() {
        const [favorites, templates, following] = await Promise.allSettled([
            api.get('/user/favorites').then(unwrap),
            api.get('/user/templates').then(unwrap),
            api.get('/user/following').then(unwrap),
        ]);
        this.lib.favorites = favorites.status === 'fulfilled' ? favorites.value.map(p => p.id) : [];
        this.lib.templates = templates.status === 'fulfilled' ? templates.value.map(p => p.id) : [];
        this.lib.following = following.status === 'fulfilled' ? following.value.map(p => p.id) : [];
    },

    has(kind, project) {
        return this.lib[kind].includes(project?.id);
    },

    busy(kind, project) {
        return Boolean(this.libBusy[`${kind}-${project?.id}`]);
    },

    async toggle(kind, project) {
        const key = `${kind}-${project.id}`;
        if (this.libBusy[key]) return false;
        this.libBusy[key] = true;
        const active = this.has(kind, project);
        try {
            await api({ method: active ? 'delete' : 'post', url: `/user/${kind}/${project.id}` });
            this.lib[kind] = active ? this.lib[kind].filter(id => id !== project.id) : [...this.lib[kind], project.id];
            toast(MESSAGES[kind][active ? 1 : 0], active ? 'info' : 'success');
            return true;
        } catch (error) {
            toast(errorMessage(error), 'error');
            return false;
        } finally {
            delete this.libBusy[key];
        }
    },

    /** Pide confirmación antes de quitar algo importante. */
    confirmRemove(kind, project) {
        this.pendingRemoval = { kind, project };
        this.$store.modal.open('confirm-remove');
    },

    async doRemove() {
        if (!this.pendingRemoval) return;
        const { kind, project } = this.pendingRemoval;
        const done = await library().toggle.call(this, kind, project);
        if (done && this.onRemoved) this.onRemoved(kind, project);
        this.$store.modal.close();
        this.pendingRemoval = null;
    },

    open(project, type) {
        return openProjectResource(project, type);
    },
});

/** Listado con búsqueda, filtros y paginación en el cliente. */
const listing = (perPage = 9) => ({
    query: '',
    page: 1,
    perPage,
    get pages() {
        return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
    },
    get paged() {
        const page = Math.min(this.page, this.pages);
        return this.filtered.slice((page - 1) * this.perPage, page * this.perPage);
    },
    matches(project) {
        const q = this.query.trim().toLowerCase();
        if (!q) return true;
        return [project.title, project.short_description, ...(project.technologies ?? []).map(t => t.name)].join(' ').toLowerCase().includes(q);
    },
});

export default function registerUser(Alpine) {
    /* ------------------------------------------------------------------ */
    /* Dashboard                                                           */
    /* ------------------------------------------------------------------ */
    Alpine.data('userDashboard', () => extend(library(), {
        loading: true,
        dashboard: null,
        projects: [],
        updates: [],
        error: '',

        async init() {
            const [dashboard, projects] = await Promise.allSettled([
                api.get('/user/dashboard').then(res => res.data),
                fetchAll('/projects', { per_page: 50 }, 2),
                this.loadLibrary(),
            ]);
            if (dashboard.status === 'rejected') this.error = errorMessage(dashboard.reason);
            this.dashboard = dashboard.status === 'fulfilled' ? dashboard.value : null;
            this.projects = projects.status === 'fulfilled' ? projects.value : [];
            this.loading = false;
        },

        get stats() {
            return {
                favorites: this.lib.favorites.length,
                templates: this.lib.templates.length,
                following: this.lib.following.length,
                updates: this.dashboard?.recent_updates?.length ?? 0,
            };
        },

        /** Recomendados: destacados primero, sin los que ya son favoritos o seguidos. */
        get recommended() {
            const known = new Set([...this.lib.favorites, ...this.lib.following]);
            const byFeatured = (a, b) => Number(b.featured) - Number(a.featured);
            const fresh = this.projects.filter(p => !known.has(p.id)).sort(byFeatured);
            const rest = this.projects.filter(p => known.has(p.id)).sort(byFeatured);
            return [...fresh, ...rest].slice(0, 3);
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Explorar proyectos                                                  */
    /* ------------------------------------------------------------------ */
    Alpine.data('userExplore', () => extend(library(), listing(9), {
        loading: true,
        error: '',
        projects: [],
        technologies: [],
        tech: '',
        onlyFeatured: false,
        kind: 'all',

        async init() {
            api.get('/technologies').then(res => { this.technologies = unwrap(res); }).catch(() => {});
            this.loadLibrary();
            await this.load();
            this.$watch('tech', () => { this.page = 1; this.load(); });
            ['query', 'onlyFeatured', 'kind'].forEach(key => this.$watch(key, () => { this.page = 1; }));
        },

        async load() {
            this.loading = true;
            this.error = '';
            try {
                this.projects = await fetchAll('/projects', { per_page: 100, ...(this.tech ? { technology: this.tech } : {}) });
            } catch (error) {
                this.error = errorMessage(error);
            } finally {
                this.loading = false;
            }
        },

        get filtered() {
            return this.projects.filter(p => {
                if (this.onlyFeatured && !p.featured) return false;
                if (this.kind === 'demo' && !p.demo_enabled) return false;
                if (this.kind === 'templates' && !p.is_template) return false;
                if (this.kind === 'resources' && !(p.download_enabled || p.source_code_enabled)) return false;
                return this.matches(p);
            });
        },

        reset() {
            this.query = '';
            this.tech = '';
            this.onlyFeatured = false;
            this.kind = 'all';
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Favoritos / Plantillas / Seguidos                                   */
    /* ------------------------------------------------------------------ */
    Alpine.data('userCollection', kind => extend(library(), listing(9), {
        kind,
        loading: true,
        error: '',
        items: [],
        updates: [],
        filter: '',

        async init() {
            try {
                const tasks = [api.get(`/user/${this.kind}`).then(unwrap)];
                if (this.kind === 'following') tasks.push(api.get('/user/project-updates').then(unwrap));
                const [items, updates] = await Promise.all(tasks);
                this.items = items;
                this.updates = updates ?? [];
                this.lib[this.kind] = items.map(p => p.id);
            } catch (error) {
                this.error = errorMessage(error);
            } finally {
                this.loading = false;
            }
            this.loadLibrary();
            ['query', 'filter'].forEach(key => this.$watch(key, () => { this.page = 1; }));
        },

        onRemoved(kind, project) {
            if (kind === this.kind) this.items = this.items.filter(item => item.id !== project.id);
        },

        /** En su propia lista, quitar pide confirmación y saca la tarjeta. */
        toggle(kind, project) {
            if (kind === this.kind && this.has(kind, project)) return this.confirmRemove(kind, project);
            return library().toggle.call(this, kind, project);
        },

        get filtered() {
            return this.items.filter(p => {
                if (this.filter === 'demo' && !p.demo_enabled) return false;
                if (this.filter === 'download' && !p.download_enabled) return false;
                if (this.filter === 'code' && !p.source_code_enabled) return false;
                if (this.filter === 'featured' && !p.featured) return false;
                if (this.filter === 'updates' && !this.updatesFor(p).length) return false;
                return this.matches(p);
            });
        },

        updatesFor(project) {
            return this.updates.filter(u => u.project_id === project.id);
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Novedades (feed)                                                    */
    /* ------------------------------------------------------------------ */
    Alpine.data('userFeed', () => extend(listing(8), {
        loading: true,
        error: '',
        updates: [],
        project: '',
        range: '',

        async init() {
            try {
                this.updates = unwrap(await api.get('/user/project-updates'));
            } catch (error) {
                this.error = errorMessage(error);
            } finally {
                this.loading = false;
            }
            ['query', 'project', 'range'].forEach(key => this.$watch(key, () => { this.page = 1; }));
        },

        get projects() {
            const map = new Map();
            this.updates.forEach(u => u.project && map.set(u.project.id, u.project));
            return [...map.values()];
        },

        get filtered() {
            const q = this.query.trim().toLowerCase();
            const days = Number(this.range || 0);
            const since = days ? Date.now() - days * 86400000 : 0;
            return this.updates.filter(u => {
                if (this.project && String(u.project_id) !== String(this.project)) return false;
                if (since && new Date(u.published_at || u.created_at).getTime() < since) return false;
                return !q || `${u.title} ${u.content} ${u.project?.title ?? ''}`.toLowerCase().includes(q);
            });
        },

        isNew(update) {
            return Date.now() - new Date(update.published_at || update.created_at).getTime() < 7 * 86400000;
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Seguridad                                                           */
    /* ------------------------------------------------------------------ */
    Alpine.data('userSecurity', initialVerified => ({
        verified: Boolean(initialVerified),
        verifiedAt: null,
        sending: false,
        sent: '',
        form: { current_password: '', password: '', password_confirmation: '' },
        state: { loading: false, error: '', errors: {}, done: '' },

        async init() {
            try {
                const { data } = await api.get('/auth/email/verification-status');
                this.verified = Boolean(data.verified);
                this.verifiedAt = data.email_verified_at;
            } catch {
                // se mantiene el valor inicial del servidor
            }
        },

        get strength() {
            const value = this.form.password;
            let score = 0;
            if (value.length >= 8) score++;
            if (/[a-zA-Z]/.test(value) && /\d/.test(value)) score++;
            if (/[^a-zA-Z0-9]/.test(value) || value.length >= 12) score++;
            return score;
        },

        async resend() {
            this.sending = true;
            this.sent = '';
            try {
                const { data } = await api.post('/auth/email/verification-notification');
                this.verified = Boolean(data.verified);
                this.sent = data.message ?? 'Se envió un nuevo enlace de verificación.';
                toast(this.sent);
            } catch (error) {
                toast(errorMessage(error), 'error');
            } finally {
                this.sending = false;
            }
        },

        async changePassword() {
            this.state = { loading: true, error: '', errors: {}, done: '' };
            try {
                const response = await api.patch('/user/password', this.form);
                this.form = { current_password: '', password: '', password_confirmation: '' };
                this.state.done = response?.data?.message ?? 'Contraseña actualizada correctamente.';
                toast('Contraseña actualizada.');
            } catch (error) {
                this.state.errors = error?.response?.data?.errors ?? {};
                this.state.error = Object.keys(this.state.errors).length ? '' : errorMessage(error);
            } finally {
                this.state.loading = false;
            }
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Contacto (autocompleta nombre y correo)                             */
    /* ------------------------------------------------------------------ */
    Alpine.data('userContact', (name, email) => ({
        form: { name: name ?? '', email: email ?? '', subject: '', message: '' },
        state: { loading: false, sent: false, error: '', errors: {} },

        async submit() {
            this.state = { loading: true, sent: false, error: '', errors: {} };
            try {
                await api.post('/contact', this.form);
                this.form.subject = '';
                this.form.message = '';
                this.state.sent = true;
                toast('Mensaje enviado. Gracias por escribir.');
            } catch (error) {
                this.state.errors = error?.response?.data?.errors ?? {};
                this.state.error = Object.keys(this.state.errors).length ? '' : errorMessage(error);
            } finally {
                this.state.loading = false;
            }
        },
    }));
}
