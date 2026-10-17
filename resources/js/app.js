import './bootstrap';
import Alpine from 'alpinejs';
import * as seed from './seed/helpers';
import registerPages from './seed/pages';
import registerAdmin from './seed/admin';
import registerUser from './seed/user';

window.Alpine = Alpine;

/* Helpers disponibles en las vistas como $seed.coverStyle(project), etc. */
Alpine.magic('seed', () => seed);

/* Notificaciones flotantes */
Alpine.store('toasts', {
    items: [],
    push(message, type = 'success', timeout = 4200) {
        if (!message) return;
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => this.dismiss(id), timeout);
    },
    dismiss(id) {
        this.items = this.items.filter(item => item.id !== id);
    },
});

/* Modales por nombre: $store.modal.open('gallery') */
Alpine.store('modal', {
    current: null,
    open(name) {
        this.current = name;
        document.documentElement.classList.add('overflow-hidden');
    },
    close() {
        this.current = null;
        document.documentElement.classList.remove('overflow-hidden');
    },
    is(name) {
        return this.current === name;
    },
});

registerPages(Alpine);
registerAdmin(Alpine);
registerUser(Alpine);

/* Toast desde mensajes flash del servidor */
document.addEventListener('alpine:initialized', () => {
    (window.SystemSeed?.flash ?? []).forEach(([type, message]) => Alpine.store('toasts').push(message, type));
});

Alpine.start();
