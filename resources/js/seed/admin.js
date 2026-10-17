import { api, errorMessage, unwrap } from './api';
import { toast } from './helpers';

/**
 * Utilidades y componentes Alpine del panel administrativo.
 * Todo se comunica con /api/v1/admin/* usando la sesión web (sin tokens en el navegador).
 */

/* ---------- Helpers ---------- */
const clone = value => JSON.parse(JSON.stringify(value ?? null));

export const fetchAllPages = async (path, params = {}, maxPages = 20) => {
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

/**
 * Sube una imagen desde el PC (POST /admin/uploads, ruta web solo para admin)
 * y devuelve la URL pública para guardarla en icon / cover_image / image_url.
 */
export const uploadImage = async (file, folder) => {
    const data = new FormData();
    data.append('image', file);
    data.append('folder', folder);
    const response = await window.axios.post('/admin/uploads', data, {
        headers: { Accept: 'application/json', 'Content-Type': 'multipart/form-data' },
    });
    return response.data.data.url;
};

/** ISO → valor para <input type="datetime-local"> (hora local). */
export const toLocalInput = value => {
    if (!value) return '';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    const pad = n => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

/** ISO o YYYY-MM-DD → valor para <input type="date">. */
export const toDateInput = value => (value ? String(value).slice(0, 10) : '');

const nullIfEmpty = value => (value === '' || value === undefined ? null : value);

/** Arma FormData (para subir archivos). Booleanos como 1/0, null como vacío. */
const toFormData = (payload, method) => {
    const data = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
        if (value === undefined) return;
        if (value instanceof File) data.append(key, value);
        else if (typeof value === 'boolean') data.append(key, value ? '1' : '0');
        else if (value === null) data.append(key, '');
        else data.append(key, value);
    });
    if (method && method !== 'post') data.append('_method', method.toUpperCase());
    return data;
};

/**
 * Componente CRUD genérico.
 *
 * config = {
 *   endpoint: '/admin/skills' | () => string,
 *   paginated: false,             // usa ?page= del backend
 *   defaults: { ... },            // valores del formulario nuevo
 *   toForm: item => form,         // opcional: adapta el registro al formulario
 *   toPayload: form => payload,   // opcional: adapta el formulario al JSON
 *   multipart: false,             // envía FormData (archivos)
 *   search: ['name', 'email'],    // campos para el buscador
 *   filter: (item, value) => bool,// filtro adicional
 *   perPage: 10,
 *   labels: { created, updated, deleted, singular }
 * }
 */
export const adminResource = config => ({
    items: [],
    loading: true,
    error: '',
    query: '',
    filterValue: '',
    page: 1,
    perPage: config.perPage ?? 10,
    form: clone(config.defaults ?? {}),
    editingId: null,
    saving: false,
    errors: {},
    formError: '',
    target: null,
    deleting: false,

    get endpoint() {
        return typeof config.endpoint === 'function' ? config.endpoint.call(this) : config.endpoint;
    },

    async init() {
        if (config.autoload === false) {
            this.loading = false;
            return;
        }
        await this.load();
        this.$watch('query', () => { this.page = 1; });
        this.$watch('filterValue', () => { this.page = 1; });
    },

    async load() {
        if (!this.endpoint) return;
        this.loading = true;
        this.error = '';
        try {
            this.items = config.paginated ? await fetchAllPages(this.endpoint) : unwrap(await api.get(this.endpoint));
        } catch (error) {
            this.error = errorMessage(error);
        } finally {
            this.loading = false;
        }
    },

    /* ----- Listado ----- */
    get filtered() {
        const q = this.query.trim().toLowerCase();
        const keys = config.search ?? ['name', 'title'];
        return this.items.filter(item => {
            if (config.filter && this.filterValue !== '' && !config.filter(item, this.filterValue)) return false;
            if (!q) return true;
            return keys.map(key => item[key] ?? '').join(' ').toLowerCase().includes(q);
        });
    },
    get pages() {
        return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
    },
    get paged() {
        const page = Math.min(this.page, this.pages);
        return this.filtered.slice((page - 1) * this.perPage, page * this.perPage);
    },
    count(predicate) {
        return this.items.filter(predicate).length;
    },

    /* ----- Formulario ----- */
    get isEditing() {
        return this.editingId !== null;
    },

    create() {
        this.editingId = null;
        this.form = clone(config.defaults ?? {});
        this.errors = {};
        this.formError = '';
        this.$store.modal.open(config.modal ?? 'resource-form');
    },

    edit(item) {
        this.editingId = item.id;
        const base = clone(config.defaults ?? {});
        const data = config.toForm ? config.toForm.call(this, clone(item)) : clone(item);
        this.form = { ...base, ...Object.fromEntries(Object.keys(base).map(key => [key, data[key] ?? base[key]])), ...(config.toFormExtra ? config.toFormExtra.call(this, item) : {}) };
        this.errors = {};
        this.formError = '';
        this.$store.modal.open(config.modal ?? 'resource-form');
    },

    err(field) {
        return this.errors?.[field]?.[0] ?? '';
    },

    async save() {
        this.saving = true;
        this.errors = {};
        this.formError = '';
        const payload = config.toPayload ? config.toPayload.call(this, clone(this.form), this.form) : clone(this.form);
        Object.keys(payload).forEach(key => { payload[key] = nullIfEmpty(payload[key]); });
        const method = this.isEditing ? 'put' : 'post';
        const url = this.isEditing ? `${this.endpoint}/${this.editingId}` : this.endpoint;
        try {
            const response = config.multipart
                ? await api.post(url, toFormData({ ...payload, ...(config.files ? config.files.call(this) : {}) }, method), { headers: { 'Content-Type': 'multipart/form-data' } })
                : await api({ method, url, data: payload });
            const saved = response?.data?.data;
            if (saved) {
                this.items = this.isEditing ? this.items.map(item => (item.id === saved.id ? saved : item)) : [saved, ...this.items];
            } else {
                await this.load();
            }
            if (config.afterSave) await config.afterSave.call(this, saved);
            this.$store.modal.close();
            toast(this.isEditing ? (config.labels?.updated ?? 'Cambios guardados.') : (config.labels?.created ?? 'Registro creado.'));
        } catch (error) {
            this.errors = error?.response?.data?.errors ?? {};
            this.formError = Object.keys(this.errors).length ? 'Revisa los campos marcados.' : errorMessage(error);
        } finally {
            this.saving = false;
        }
    },

    /* ----- Cambios rápidos (PATCH parcial) ----- */
    async patch(item, data, message = 'Actualizado.') {
        try {
            const response = await api.patch(`${this.endpoint}/${item.id}`, data);
            const saved = response?.data?.data;
            if (saved) this.items = this.items.map(row => (row.id === saved.id ? saved : row));
            toast(message);
            return saved;
        } catch (error) {
            toast(errorMessage(error), 'error');
            return null;
        }
    },

    /* ----- Eliminar ----- */
    askDelete(item) {
        this.target = item;
        this.$store.modal.open('confirm-delete');
    },

    async remove() {
        if (!this.target) return;
        this.deleting = true;
        try {
            await api.delete(`${this.endpoint}/${this.target.id}`);
            this.items = this.items.filter(item => item.id !== this.target.id);
            this.$store.modal.close();
            toast(config.labels?.deleted ?? 'Registro eliminado.');
            this.target = null;
        } catch (error) {
            toast(errorMessage(error), 'error');
        } finally {
            this.deleting = false;
        }
    },
});

/**
 * Combina objetos conservando getters (el spread "..." los evaluaría y los rompería).
 */
const extend = (...sources) => sources.reduce(
    (target, source) => Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)),
    {},
);

/* ====================================================================== */
/* Registro de componentes por módulo                                     */
/* ====================================================================== */
export default function registerAdmin(Alpine) {
    /* ---------------- Campo de imagen (URL o subir desde el PC) ---------------- */
    Alpine.data('imageInput', (folder, setter, getter) => ({
        uploading: false,
        dragging: false,
        uploadError: '',
        get value() {
            return getter.call(this);
        },
        async upload(file) {
            if (!file) return;
            this.uploadError = '';
            if (!file.type.startsWith('image/')) {
                this.uploadError = 'Selecciona un archivo de imagen (JPG, PNG, WEBP o GIF).';
                return;
            }
            if (file.size > 4 * 1024 * 1024) {
                this.uploadError = 'La imagen no debe pesar más de 4 MB.';
                return;
            }
            this.uploading = true;
            try {
                setter.call(this, await uploadImage(file, folder));
                toast('Imagen subida.');
            } catch (error) {
                this.uploadError = error?.response?.data?.errors?.image?.[0] ?? errorMessage(error);
            } finally {
                this.uploading = false;
                if (this.$refs.file) this.$refs.file.value = '';
            }
        },
        pick(event) {
            return this.upload(event.target.files?.[0]);
        },
        drop(event) {
            this.dragging = false;
            return this.upload(event.dataTransfer?.files?.[0]);
        },
        clear() {
            setter.call(this, '');
        },
    }));

    /* ---------------- Proyectos ---------------- */
    Alpine.data('adminProjects', () => extend(adminResource({
            endpoint: '/admin/projects',
            paginated: true,
            search: ['title', 'slug', 'short_description'],
            filter: (item, value) => item.status === value,
            defaults: {
                title: '', slug: '', short_description: '', description: '', cover_image: '', github_url: '',
                status: 'draft', featured: false, published_at: '',
                demo_mode: 'off', demo_url: '',
                is_template: false, download_enabled: false, download_url: '', source_code_enabled: false, source_code_url: '',
                technologies: [],
            },
            toForm: item => ({
                ...item,
                published_at: toLocalInput(item.published_at),
                demo_mode: !item.demo_enabled ? 'off' : (item.demo_public ? 'public' : 'registered'),
                technologies: (item.technologies ?? []).map(t => t.id),
            }),
            toPayload(form) {
                const payload = {
                    ...form,
                    demo_enabled: form.demo_mode !== 'off',
                    demo_public: form.demo_mode === 'public',
                    technologies: (form.technologies ?? []).map(Number),
                };
                delete payload.demo_mode;
                if (!this.isEditing) delete payload.slug; // el backend genera el slug al crear
                return payload;
            },
            labels: { created: 'Proyecto creado.', updated: 'Proyecto actualizado.', deleted: 'Proyecto eliminado.' },
        }), {
        technologies: [],
        imageForm: { image_url: '', alt_text: '', sort_order: 0 },
        imageErrors: {},
        imageSaving: false,
        current: null,

        async init() {
            api.get('/admin/technologies').then(res => { this.technologies = unwrap(res); }).catch(() => {});
            await this.load();
            this.$watch('query', () => { this.page = 1; });
            this.$watch('filterValue', () => { this.page = 1; });
        },

        toggleTech(id) {
            const list = this.form.technologies ?? [];
            this.form.technologies = list.includes(id) ? list.filter(item => item !== id) : [...list, id];
        },

        async setStatus(project, status) {
            const labels = { published: 'Proyecto publicado.', draft: 'Proyecto movido a borrador.', archived: 'Proyecto archivado.' };
            await this.patch(project, { status, ...(status === 'published' && !project.published_at ? { published_at: new Date().toISOString() } : {}) }, labels[status]);
        },

        async toggleFeatured(project) {
            await this.patch(project, { featured: !project.featured }, project.featured ? 'Ya no es destacado.' : 'Marcado como destacado.');
        },

        /* Galería */
        openImages(project) {
            this.current = project;
            this.imageForm = { image_url: '', alt_text: '', sort_order: (project.images ?? []).length };
            this.imageErrors = {};
            this.$store.modal.open('project-images');
        },

        async addImage() {
            this.imageSaving = true;
            this.imageErrors = {};
            try {
                const image = (await api.post(`/admin/projects/${this.current.id}/images`, this.imageForm)).data.data;
                this.current.images = [...(this.current.images ?? []), image];
                this.items = this.items.map(item => (item.id === this.current.id ? { ...item, images: this.current.images } : item));
                this.imageForm = { image_url: '', alt_text: '', sort_order: this.current.images.length };
                toast('Imagen agregada a la galería.');
            } catch (error) {
                this.imageErrors = error?.response?.data?.errors ?? {};
                if (!Object.keys(this.imageErrors).length) toast(errorMessage(error), 'error');
            } finally {
                this.imageSaving = false;
            }
        },

        async removeImage(image) {
            try {
                await api.delete(`/admin/projects/${this.current.id}/images/${image.id}`);
                this.current.images = this.current.images.filter(item => item.id !== image.id);
                this.items = this.items.map(item => (item.id === this.current.id ? { ...item, images: this.current.images } : item));
                toast('Imagen eliminada.');
            } catch (error) {
                toast(errorMessage(error), 'error');
            }
        },

        async useAsCover(image) {
            const saved = await this.patch(this.current, { cover_image: image.image_url }, 'Portada actualizada.');
            if (saved) this.current = { ...saved, images: this.current.images };
        },
    }));

    /* ---------------- Novedades (por proyecto) ---------------- */
    Alpine.data('adminUpdates', initialProject => extend(adminResource({
            endpoint() { return this.projectId ? `/admin/projects/${this.projectId}/updates` : null; },
            autoload: false,
            search: ['title', 'content'],
            filter: (item, value) => String(item.visible) === value,
            defaults: { title: '', content: '', published_at: '', visible: true },
            toForm: item => ({ ...item, published_at: toLocalInput(item.published_at) }),
            labels: { created: 'Novedad publicada.', updated: 'Novedad actualizada.', deleted: 'Novedad eliminada.' },
        }), {
        projects: [],
        projectId: initialProject ? String(initialProject) : '',

        async init() {
            try {
                this.projects = await fetchAllPages('/admin/projects');
                if (!this.projectId && this.projects.length) this.projectId = String(this.projects[0].id);
            } catch (error) {
                this.error = errorMessage(error);
            }
            await this.load();
            if (!this.projectId) this.loading = false;
            this.$watch('projectId', () => {
                this.page = 1;
                const url = new URL(window.location.href);
                url.searchParams.set('proyecto', this.projectId);
                window.history.replaceState({}, '', url);
                this.load();
            });
        },

        get project() {
            return this.projects.find(p => String(p.id) === String(this.projectId)) ?? null;
        },

        create() {
            this.editingId = null;
            this.form = { title: '', content: '', published_at: toLocalInput(new Date().toISOString()), visible: true };
            this.errors = {};
            this.formError = '';
            this.$store.modal.open('resource-form');
        },
    }));

    /* ---------------- Tecnologías ---------------- */
    Alpine.data('adminTechnologies', () => adminResource({
        endpoint: '/admin/technologies',
        perPage: 24,
        search: ['name', 'slug'],
        filter: (item, value) => (item.category || 'other') === value,
        defaults: { name: '', slug: '', icon: '', category: '' },
        toPayload(form) {
            // Slug vacío: al crear lo genera el backend; al editar se conserva el actual.
            if (!form.slug) delete form.slug;
            return form;
        },
        labels: { created: 'Tecnología creada.', updated: 'Tecnología actualizada.', deleted: 'Tecnología eliminada.' },
    }));

    /* ---------------- Certificados ---------------- */
    Alpine.data('adminCertificates', () => {
        const base = adminResource({
            endpoint: '/admin/certificates',
            multipart: true,
            search: ['title', 'institution', 'category', 'credential_id'],
            filter: (item, value) => String(item.visible) === value,
            defaults: { title: '', institution: '', description: '', issued_at: '', credential_id: '', verification_url: '', category: '', visible: true, sort_order: 0 },
            toForm: item => ({ ...item, issued_at: toDateInput(item.issued_at) }),
            toPayload: form => ({ ...form, sort_order: Number(form.sort_order || 0) }),
            files() { return this.file ? { file: this.file } : {}; },
            afterSave() { this.setFile(null); },
            labels: { created: 'Certificado creado.', updated: 'Certificado actualizado.', deleted: 'Certificado eliminado.' },
        });
        return extend(base, {
            file: null,
            currentFile: null,
            create() {
                this.setFile(null);
                this.currentFile = null;
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                base.create.call(this);
                this.form.sort_order = this.items.length;
            },
            edit(item) {
                this.setFile(null);
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                this.currentFile = item.file_url ? { url: item.file_url, type: item.file_type } : null;
                base.edit.call(this, item);
            },
            fileError: '',
            filePreview: null,
            dragging: false,

            /** Valida y prepara el archivo (PDF o imagen, máx. 10 MB) con vista previa. */
            setFile(file) {
                this.fileError = '';
                if (this.filePreview) URL.revokeObjectURL(this.filePreview);
                this.filePreview = null;
                if (!file) { this.file = null; return; }
                const ok = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'].includes(file.type) || /\.(pdf|jpe?g|png|webp)$/i.test(file.name);
                if (!ok) { this.fileError = 'Solo se permiten PDF, JPG, PNG o WEBP.'; this.file = null; return; }
                if (file.size > 10 * 1024 * 1024) { this.fileError = 'El archivo no debe pesar más de 10 MB.'; this.file = null; return; }
                this.file = file;
                if (file.type.startsWith('image/')) this.filePreview = URL.createObjectURL(file);
            },
            pickFile(event) {
                this.setFile(event.target.files?.[0] ?? null);
            },
            dropFile(event) {
                this.dragging = false;
                this.setFile(event.dataTransfer?.files?.[0] ?? null);
            },
            removeFile() {
                this.setFile(null);
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
            },
            isImage(type) {
                return ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(String(type ?? '').toLowerCase());
            },
        });
    });

    /* ---------------- Experiencia ---------------- */
    Alpine.data('adminExperiences', () => adminResource({
        endpoint: '/admin/experiences',
        search: ['company', 'position', 'location'],
        filter: (item, value) => String(item.visible) === value,
        defaults: { company: '', position: '', description: '', location: '', start_date: '', end_date: '', is_current: false, visible: true, sort_order: 0 },
        toForm: item => ({ ...item, start_date: toDateInput(item.start_date), end_date: toDateInput(item.end_date) }),
        toPayload: form => ({ ...form, end_date: form.is_current ? null : form.end_date, sort_order: Number(form.sort_order || 0) }),
        labels: { created: 'Experiencia agregada.', updated: 'Experiencia actualizada.', deleted: 'Experiencia eliminada.' },
    }));

    /* ---------------- Educación ---------------- */
    Alpine.data('adminEducation', () => adminResource({
        endpoint: '/admin/education',
        search: ['institution', 'degree', 'field_of_study'],
        filter: (item, value) => String(item.visible) === value,
        defaults: { institution: '', degree: '', field_of_study: '', description: '', location: '', start_date: '', end_date: '', is_current: false, visible: true, sort_order: 0 },
        toForm: item => ({ ...item, start_date: toDateInput(item.start_date), end_date: toDateInput(item.end_date) }),
        toPayload: form => ({ ...form, end_date: form.is_current ? null : form.end_date, sort_order: Number(form.sort_order || 0) }),
        labels: { created: 'Formación agregada.', updated: 'Formación actualizada.', deleted: 'Formación eliminada.' },
    }));

    /* ---------------- Skills ---------------- */
    Alpine.data('adminSkills', () => adminResource({
        endpoint: '/admin/skills',
        perPage: 12,
        search: ['name', 'level'],
        filter: (item, value) => (item.category || 'other') === value,
        defaults: { name: '', category: '', level: '', icon: '', visible: true, sort_order: 0 },
        toPayload: form => ({ ...form, sort_order: Number(form.sort_order || 0) }),
        labels: { created: 'Skill creado.', updated: 'Skill actualizado.', deleted: 'Skill eliminado.' },
    }));

    /* ---------------- Perfil y CV ---------------- */
    Alpine.data('adminProfile', () => ({
        loading: true,
        saving: false,
        errors: {},
        formError: '',
        form: { full_name: '', professional_title: '', bio: '', location: '', github_url: '', linkedin_url: '', website_url: '', tiktok_url: '', youtube_url: '', instagram_url: '', whatsapp_url: '' },
        cvUrl: null,
        cvFile: null,
        cvBusy: false,

        async init() {
            try {
                const profile = (await api.get('/admin/profile')).data.data ?? {};
                Object.keys(this.form).forEach(key => { this.form[key] = profile[key] ?? ''; });
                this.cvUrl = profile.cv_url ?? null;
            } catch (error) {
                this.formError = errorMessage(error);
            } finally {
                this.loading = false;
            }
        },

        err(field) {
            return this.errors?.[field]?.[0] ?? '';
        },

        async save() {
            this.saving = true;
            this.errors = {};
            this.formError = '';
            const payload = Object.fromEntries(Object.entries(this.form).map(([key, value]) => [key, value === '' ? null : value]));
            try {
                await api.put('/admin/profile', payload);
                toast('Perfil actualizado.');
            } catch (error) {
                this.errors = error?.response?.data?.errors ?? {};
                this.formError = Object.keys(this.errors).length ? 'Revisa los campos marcados.' : errorMessage(error);
            } finally {
                this.saving = false;
            }
        },

        pickCv(event) {
            this.cvFile = event.target.files?.[0] ?? null;
        },

        async uploadCv() {
            if (!this.cvFile) return;
            this.cvBusy = true;
            try {
                const data = new FormData();
                data.append('cv', this.cvFile);
                const profile = (await api.post('/admin/profile/cv', data, { headers: { 'Content-Type': 'multipart/form-data' } })).data.data;
                this.cvUrl = profile?.cv_url ?? null;
                this.cvFile = null;
                this.$refs.cvInput.value = '';
                toast('CV actualizado.');
            } catch (error) {
                toast(errorMessage(error), 'error');
            } finally {
                this.cvBusy = false;
            }
        },

        async deleteCv() {
            this.cvBusy = true;
            try {
                await api.delete('/admin/profile/cv');
                this.cvUrl = null;
                this.$store.modal.close();
                toast('CV eliminado.');
            } catch (error) {
                toast(errorMessage(error), 'error');
            } finally {
                this.cvBusy = false;
            }
        },
    }));

    /* ---------------- Usuarios ---------------- */
    Alpine.data('adminUsers', currentId => extend(adminResource({
            endpoint: '/admin/users',
            paginated: true,
            search: ['name', 'email'],
            filter: (item, value) => (value === 'inactive' ? !item.is_active : item.role === value),
            labels: { deleted: 'Usuario eliminado.' },
        }), {
        currentId,
        selected: null,
        busy: null,

        isSelf(user) {
            return user?.id === this.currentId;
        },
        get activeAdmins() {
            return this.items.filter(u => u.role === 'admin' && u.is_active).length;
        },
        isLastAdmin(user) {
            return user?.role === 'admin' && user?.is_active && this.activeAdmins <= 1;
        },

        show(user) {
            this.selected = user;
            this.$store.modal.open('user-detail');
        },

        async update(user, data, message) {
            this.busy = user.id;
            const saved = await this.patch(user, data, message);
            if (saved && this.selected?.id === saved.id) this.selected = saved;
            this.busy = null;
        },

        toggleActive(user) {
            return this.update(user, { is_active: !user.is_active }, user.is_active ? 'Usuario desactivado.' : 'Usuario activado.');
        },

        toggleRole(user) {
            const role = user.role === 'admin' ? 'user' : 'admin';
            return this.update(user, { role }, role === 'admin' ? 'Ahora es administrador.' : 'Ahora es usuario.');
        },
    }));
}
