import { api, errorMessage, unwrap } from './api';
import { toast } from './helpers';

const MAX_FILE_SIZE = 10 * 1024 * 1024;

const fileTools = () => ({
    file: null,
    fileError: '',

    selectFile(event) {
        const file = event.target.files?.[0] ?? null;
        this.fileError = '';
        if (file && file.size > MAX_FILE_SIZE) {
            this.fileError = 'El archivo no puede superar los 10 MB.';
            event.target.value = '';
            this.file = null;
            return;
        }
        this.file = file;
    },

    clearFile() {
        this.file = null;
        this.fileError = '';
        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
    },

    fileSize(bytes) {
        if (!bytes) return '0 KB';
        if (bytes < 1024 * 1024) return `${Math.max(1, Math.round(bytes / 1024))} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    },

    scrollToEnd() {
        this.$nextTick(() => {
            if (this.$refs.messages) this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
        });
    },
});

const combine = (...sources) => sources.reduce(
    (target, source) => Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)),
    {},
);

export default function registerChat(Alpine) {
    Alpine.data('userChat', () => combine(fileTools(), {
        loading: true,
        refreshing: false,
        sending: false,
        error: '',
        body: '',
        conversation: null,
        poller: null,
        lastSyncedAt: null,

        async init() {
            await this.load();
            this.poller = window.setInterval(() => {
                if (!document.hidden) this.load(true);
            }, 3000);
        },

        destroy() {
            window.clearInterval(this.poller);
        },

        async load(silent = false) {
            if (this.refreshing) return;
            this.refreshing = true;
            if (!silent) this.loading = true;
            const previousLast = this.messages.at(-1)?.id;
            try {
                this.conversation = unwrap(await api.get('/user/chat'));
                this.error = '';
                this.lastSyncedAt = new Date();
                this.$dispatch('chat-unread-count', 0);
                if (!silent || previousLast !== this.messages.at(-1)?.id) this.scrollToEnd();
            } catch (error) {
                if (!silent) this.error = errorMessage(error);
            } finally {
                this.loading = false;
                this.refreshing = false;
            }
        },

        get messages() {
            return this.conversation?.messages ?? [];
        },

        async send() {
            if (this.sending || (!this.body.trim() && !this.file)) return;
            this.sending = true;
            this.fileError = '';
            const form = new FormData();
            if (this.body.trim()) form.append('body', this.body.trim());
            if (this.file) form.append('attachment', this.file);
            try {
                const message = unwrap(await api.post('/user/chat/messages', form));
                this.body = '';
                this.clearFile();
                if (this.conversation && !this.messages.some(item => item.id === message?.id)) this.conversation.messages.push(message);
                this.scrollToEnd();
                window.setTimeout(() => this.load(true), 250);
            } catch (error) {
                this.fileError = errorMessage(error);
            } finally {
                this.sending = false;
            }
        },
    }));

    Alpine.data('adminChat', () => combine(fileTools(), {
        loading: true,
        threadLoading: false,
        sending: false,
        refreshing: false,
        error: '',
        body: '',
        query: '',
        conversations: [],
        selected: null,
        selectedId: null,
        listPoller: null,
        threadPoller: null,
        lastSyncedAt: null,

        async init() {
            await this.loadConversations();
            this.listPoller = window.setInterval(() => {
                if (!document.hidden) this.loadConversations(true);
            }, 4000);
            this.threadPoller = window.setInterval(() => {
                if (!document.hidden && this.selectedId) this.loadThread(this.selectedId, true);
            }, 3000);
        },

        destroy() {
            window.clearInterval(this.listPoller);
            window.clearInterval(this.threadPoller);
        },

        get filtered() {
            const query = this.query.trim().toLowerCase();
            if (!query) return this.conversations;
            return this.conversations.filter(item => `${item.user?.name ?? ''} ${item.user?.email ?? ''}`.toLowerCase().includes(query));
        },

        get messages() {
            return this.selected?.messages ?? [];
        },

        async loadConversations(silent = false) {
            if (this.refreshing) return;
            this.refreshing = true;
            if (!silent) this.loading = true;
            try {
                this.conversations = unwrap(await api.get('/admin/chats', { params: { per_page: 100 } }));
                this.$dispatch('chat-unread-count', this.conversations.reduce((sum, item) => sum + Number(item.unread_count || 0), 0));
                this.error = '';
                this.lastSyncedAt = new Date();
            } catch (error) {
                if (!silent) this.error = errorMessage(error);
            } finally {
                this.loading = false;
                this.refreshing = false;
            }
        },

        async open(conversation) {
            this.selectedId = conversation.id;
            await this.loadThread(conversation.id);
            await this.loadConversations(true);
        },

        async loadThread(id, silent = false) {
            if (!silent) this.threadLoading = true;
            const previousLast = this.messages.at(-1)?.id;
            try {
                this.selected = unwrap(await api.get(`/admin/chats/${id}`));
                if (!silent || previousLast !== this.messages.at(-1)?.id) this.scrollToEnd();
            } catch (error) {
                if (!silent) toast(errorMessage(error), 'error');
            } finally {
                this.threadLoading = false;
            }
        },

        close() {
            this.selectedId = null;
            this.selected = null;
            this.body = '';
            this.clearFile();
        },

        async send() {
            if (!this.selectedId || this.sending || (!this.body.trim() && !this.file)) return;
            this.sending = true;
            this.fileError = '';
            const form = new FormData();
            if (this.body.trim()) form.append('body', this.body.trim());
            if (this.file) form.append('attachment', this.file);
            try {
                const message = unwrap(await api.post(`/admin/chats/${this.selectedId}/messages`, form));
                this.body = '';
                this.clearFile();
                if (this.selected && !this.messages.some(item => item.id === message?.id)) this.selected.messages.push(message);
                this.scrollToEnd();
                await this.loadConversations(true);
            } catch (error) {
                this.fileError = errorMessage(error);
            } finally {
                this.sending = false;
            }
        },
    }));
}
