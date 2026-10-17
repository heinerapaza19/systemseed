import { api, errorMessage, unwrap } from './api';
import { isAuthenticated, openProjectResource, toast, whatsappLink, whatsappNumber } from './helpers';

/** Descarga todas las páginas de un listado paginado de la API. */
const fetchAll = async (path, params = {}, maxPages = 10) => {
    const items = [];
    let page = 1;
    let lastPage = 1;
    do {
        const response = await api.get(path, { params: { ...params, page } });
        items.push(...unwrap(response));
        lastPage = response?.data?.meta?.last_page ?? 1;
        page += 1;
    } while (page <= lastPage && page <= maxPages);
    return items;
};

const settle = async tasks => {
    const keys = Object.keys(tasks);
    const results = await Promise.allSettled(Object.values(tasks));
    return Object.fromEntries(keys.map((key, i) => [key, results[i]]));
};

const value = (result, fallback = []) => (result?.status === 'fulfilled' ? result.value : fallback);

/* Acciones de usuario sobre proyectos: favoritos, plantillas y seguidos. */
const COLLECTIONS = {
    favorites: {
        add: 'Proyecto agregado a tus favoritos.',
        remove: 'Proyecto quitado de favoritos.',
    },
    templates: {
        add: 'Plantilla guardada en tu cuenta.',
        remove: 'Plantilla quitada de tus guardados.',
    },
    following: {
        add: 'Ahora sigues las novedades de este proyecto.',
        remove: 'Dejaste de seguir este proyecto.',
    },
};

export default function registerPages(Alpine) {
    /* ------------------------------------------------------------------ */
    /* Home pública                                                        */
    /* ------------------------------------------------------------------ */
    Alpine.data('homePage', () => ({
        loading: true,
        profile: {},
        projects: [],
        technologies: [],
        experiences: [],
        education: [],
        skills: [],
        certificates: [],
        failed: false,
        contact: { name: '', email: '', subject: '', message: '' },
        contactState: { loading: false, sent: false, error: '', errors: {}, waUrl: null },

        async init() {
            const r = await settle({
                profile: api.get('/profile').then(res => res.data.data),
                projects: fetchAll('/projects', { per_page: 100 }, 3),
                technologies: api.get('/technologies').then(unwrap),
                experiences: api.get('/experiences').then(unwrap),
                education: api.get('/education').then(unwrap),
                skills: api.get('/skills').then(unwrap),
                certificates: api.get('/certificates').then(unwrap),
            });
            this.profile = value(r.profile, {}) ?? {};
            this.projects = value(r.projects);
            this.technologies = value(r.technologies);
            this.experiences = value(r.experiences);
            this.education = value(r.education);
            this.skills = value(r.skills);
            this.certificates = value(r.certificates);
            this.failed = Object.values(r).every(result => result.status === 'rejected');
            this.loading = false;
        },

        get featured() {
            // Primero los destacados; si son menos de 3 se completa con los más recientes.
            const featured = this.projects.filter(project => project.featured);
            const others = this.projects.filter(project => !project.featured);
            return [...featured, ...others.slice(0, Math.max(0, 3 - featured.length))].slice(0, 6);
        },
        get stats() {
            return {
                projects: this.projects.length,
                demos: this.projects.filter(project => project.demo_enabled).length,
                templates: this.projects.filter(project => project.is_template).length,
                technologies: this.technologies.length,
            };
        },
        get skillGroups() {
            const groups = {};
            this.skills.forEach(skill => {
                const key = skill.category || 'General';
                (groups[key] ??= []).push(skill);
            });
            return Object.entries(groups).map(([name, items]) => ({ name, items }));
        },
        get firstName() {
            return (this.profile.full_name || 'Heiner').split(' ')[0];
        },
        get socials() {
            const p = this.profile;
            return [
                { key: 'github', label: 'GitHub', url: p.github_url },
                { key: 'linkedin', label: 'LinkedIn', url: p.linkedin_url },
                { key: 'globe', label: 'Sitio web', url: p.website_url },
                { key: 'instagram', label: 'Instagram', url: p.instagram_url },
                { key: 'youtube', label: 'YouTube', url: p.youtube_url },
                { key: 'whatsapp', label: 'WhatsApp', url: p.whatsapp_url },
            ].filter(item => item.url);
        },

        get whatsapp() {
            return whatsappNumber(this.profile.whatsapp_url);
        },

        whatsappText(data) {
            const lines = [
                `Hola ${this.firstName}, te escribo desde SystemSeed.`,
                '',
                `*Nombre:* ${data.name}`,
                `*Correo:* ${data.email}`,
            ];
            if (data.subject) lines.push(`*Asunto:* ${data.subject}`);
            lines.push('', '*Mensaje:*', data.message);
            return lines.join('\n');
        },

        /**
         * 1) Guarda el mensaje en la base de datos (POST /api/v1/contact) para que el admin lo vea.
         * 2) Si el perfil tiene WhatsApp, abre WhatsApp con el mensaje ya escrito hacia ese número.
         */
        async sendContact() {
            this.contactState = { loading: true, sent: false, error: '', errors: {}, waUrl: null };
            const data = { ...this.contact };
            const number = this.whatsapp;
            // La pestaña se abre en el mismo clic para que el navegador no la bloquee.
            const tab = number ? window.open('about:blank', '_blank') : null;
            try {
                await api.post('/contact', data);
                const url = number ? whatsappLink(number, this.whatsappText(data)) : null;
                if (tab && url) {
                    tab.opener = null;
                    tab.location.replace(url);
                }
                this.contact = { name: '', email: '', subject: '', message: '' };
                this.contactState.waUrl = url;
                this.contactState.sent = true;
            } catch (error) {
                tab?.close();
                this.contactState.errors = error?.response?.data?.errors ?? {};
                this.contactState.error = errorMessage(error);
            } finally {
                this.contactState.loading = false;
            }
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Listado de proyectos                                                */
    /* ------------------------------------------------------------------ */
    Alpine.data('projectsPage', () => ({
        loading: true,
        error: '',
        projects: [],
        technologies: [],
        query: '',
        tech: '',
        kind: 'all',
        kinds: [
            { key: 'all', label: 'Todos' },
            { key: 'featured', label: 'Destacados' },
            { key: 'demo', label: 'Con demo' },
            { key: 'templates', label: 'Plantillas' },
            { key: 'resources', label: 'Recursos' },
        ],

        async init() {
            const params = new URLSearchParams(window.location.search);
            this.tech = params.get('tecnologia') ?? '';
            this.kind = params.get('tipo') ?? 'all';
            this.query = params.get('q') ?? '';
            api.get('/technologies').then(res => { this.technologies = unwrap(res); }).catch(() => {});
            await this.load();
            this.$watch('tech', () => { this.syncUrl(); this.load(); });
            this.$watch('kind', () => this.syncUrl());
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

        syncUrl() {
            const params = new URLSearchParams();
            if (this.tech) params.set('tecnologia', this.tech);
            if (this.kind !== 'all') params.set('tipo', this.kind);
            if (this.query.trim()) params.set('q', this.query.trim());
            const qs = params.toString();
            window.history.replaceState({}, '', `${window.location.pathname}${qs ? `?${qs}` : ''}`);
        },

        get filtered() {
            const q = this.query.trim().toLowerCase();
            return this.projects.filter(project => {
                if (this.kind === 'featured' && !project.featured) return false;
                if (this.kind === 'demo' && !project.demo_enabled) return false;
                if (this.kind === 'templates' && !project.is_template) return false;
                if (this.kind === 'resources' && !(project.download_enabled || project.source_code_enabled)) return false;
                if (!q) return true;
                const haystack = [project.title, project.short_description, ...(project.technologies ?? []).map(t => t.name)].join(' ').toLowerCase();
                return haystack.includes(q);
            });
        },

        reset() {
            this.query = '';
            this.kind = 'all';
            this.tech = '';
            this.syncUrl();
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Detalle de proyecto                                                 */
    /* ------------------------------------------------------------------ */
    Alpine.data('projectPage', slug => ({
        slug,
        loading: true,
        notFound: false,
        error: '',
        project: null,
        updates: [],
        related: [],
        membership: { favorites: false, templates: false, following: false },
        busy: { favorites: false, templates: false, following: false },
        lightbox: 0,

        async init() {
            try {
                this.project = (await api.get(`/projects/${encodeURIComponent(this.slug)}`)).data.data;
                document.title = `${this.project.title} | SystemSeed`;
            } catch (error) {
                this.notFound = error?.response?.status === 404;
                this.error = errorMessage(error);
                this.loading = false;
                return;
            }

            const id = this.project.id;
            const firstTech = this.project.technologies?.[0]?.slug;
            const r = await settle({
                updates: api.get(`/projects/${id}/updates`).then(unwrap),
                related: firstTech ? api.get('/projects', { params: { technology: firstTech, per_page: 4 } }).then(unwrap) : Promise.resolve([]),
                ...(this.authenticated ? {
                    favorites: api.get('/user/favorites').then(unwrap),
                    templates: api.get('/user/templates').then(unwrap),
                    following: api.get('/user/following').then(unwrap),
                } : {}),
            });
            this.updates = value(r.updates);
            this.related = value(r.related).filter(project => project.id !== id).slice(0, 3);
            ['favorites', 'templates', 'following'].forEach(kind => {
                this.membership[kind] = value(r[kind]).some(project => project.id === id);
            });
            this.loading = false;
        },

        get authenticated() {
            return isAuthenticated();
        },
        get gallery() {
            return [...(this.project?.images ?? [])].sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        },
        get canDemo() {
            return this.project?.demo_enabled && (this.project.demo_public || this.authenticated);
        },
        get hasResources() {
            return Boolean(this.project?.demo_enabled || this.project?.download_enabled || this.project?.source_code_enabled);
        },

        open(type) {
            if (type !== 'demo' && !this.authenticated) return this.requireAuth();
            if (type === 'demo' && !this.canDemo) return this.requireAuth();
            return openProjectResource(this.project, type);
        },

        requireAuth() {
            Alpine.store('modal').open('auth-required');
        },

        async toggle(kind) {
            if (!this.authenticated) return this.requireAuth();
            if (this.busy[kind]) return;
            this.busy[kind] = true;
            const active = this.membership[kind];
            try {
                await api({ method: active ? 'delete' : 'post', url: `/user/${kind}/${this.project.id}` });
                this.membership[kind] = !active;
                toast(COLLECTIONS[kind][active ? 'remove' : 'add']);
            } catch (error) {
                toast(errorMessage(error), 'error');
            } finally {
                this.busy[kind] = false;
            }
        },

        showImage(index) {
            this.lightbox = index;
            Alpine.store('modal').open('gallery');
        },
        step(delta) {
            const total = this.gallery.length;
            if (total) this.lightbox = (this.lightbox + delta + total) % total;
        },
    }));

    /* ------------------------------------------------------------------ */
    /* Admin: mensajes de contacto (/api/v1/admin/messages)                */
    /* ------------------------------------------------------------------ */
    Alpine.data('adminMessagesPage', () => ({
        loading: true,
        error: '',
        messages: [],
        filter: 'all',
        query: '',
        selectedId: null,
        deleting: false,

        async init() {
            await this.load();
            this.broadcast();
            this.$watch('messages', () => this.broadcast());
            // Abre directamente el mensaje elegido desde el dashboard (?id=)
            const id = Number(new URLSearchParams(window.location.search).get('id'));
            const message = this.messages.find(m => m.id === id);
            if (message) this.open(message);
        },

        async load() {
            this.loading = true;
            this.error = '';
            try {
                this.messages = await fetchAll('/admin/messages', { per_page: 100 });
            } catch (error) {
                this.error = errorMessage(error);
            } finally {
                this.loading = false;
            }
        },

        get unreadCount() {
            return this.messages.filter(m => !m.is_read).length;
        },

        /** Avisa al sidebar y a la barra superior para actualizar el contador. */
        broadcast() {
            window.dispatchEvent(new CustomEvent('unread-count', { detail: this.unreadCount }));
        },
        get filtered() {
            const q = this.query.trim().toLowerCase();
            return this.messages.filter(m => {
                if (this.filter === 'unread' && m.is_read) return false;
                if (this.filter === 'read' && !m.is_read) return false;
                if (!q) return true;
                return `${m.name} ${m.email} ${m.subject ?? ''} ${m.message}`.toLowerCase().includes(q);
            });
        },
        get selected() {
            return this.messages.find(m => m.id === this.selectedId) ?? null;
        },

        replace(updated) {
            this.messages = this.messages.map(m => (m.id === updated.id ? updated : m));
        },

        async open(message) {
            this.selectedId = message.id;
            if (!message.is_read) {
                try {
                    this.replace((await api.patch(`/admin/messages/${message.id}/read`)).data.data);
                } catch (error) {
                    toast(errorMessage(error), 'error');
                }
            }
        },

        async markUnread(message) {
            try {
                this.replace((await api.patch(`/admin/messages/${message.id}/unread`)).data.data);
                this.selectedId = null;
                toast('Mensaje marcado como no leído.', 'info');
            } catch (error) {
                toast(errorMessage(error), 'error');
            }
        },

        askDelete() {
            Alpine.store('modal').open('delete-message');
        },

        async remove() {
            const message = this.selected;
            if (!message) return;
            this.deleting = true;
            try {
                await api.delete(`/admin/messages/${message.id}`);
                this.messages = this.messages.filter(m => m.id !== message.id);
                this.selectedId = null;
                Alpine.store('modal').close();
                toast('Mensaje eliminado.');
            } catch (error) {
                toast(errorMessage(error), 'error');
            } finally {
                this.deleting = false;
            }
        },

        replyMail(message) {
            const subject = `Re: ${message.subject || 'Tu mensaje en SystemSeed'}`;
            const body = `Hola ${message.name},\n\nGracias por escribir a SystemSeed.\n\n\n---\n${message.message}`;
            return `mailto:${message.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        },

        copy(text) {
            navigator.clipboard?.writeText(text).then(() => toast('Copiado al portapapeles.', 'info'));
        },
    }));
}
