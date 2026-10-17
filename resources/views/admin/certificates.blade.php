<x-layouts.dashboard title="Certificados" heading="Certificados">
<div x-data="adminCertificates()" class="space-y-6">
    <x-page-header eyebrow="Portafolio" title="Certificados" subtitle="Cursos y credenciales del portafolio. Al crear uno puedes subir el PDF o la imagen del certificado desde tu PC." icon="award">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()"><x-icon name="plus" :size="16" /> Nuevo certificado</button>
        </x-slot:actions>
    </x-page-header>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar por título, institución o categoría…" :filters="['true' => 'Visibles', 'false' => 'Ocultos']" />
        <x-admin.list-state empty="Todavía no hay certificados." icon="award" action="Agregar el primero" />

        <div x-cloak x-show="!loading && paged.length" class="grid gap-3 p-4 md:grid-cols-2">
            <template x-for="c in paged" :key="c.id">
                <article class="flex gap-4 rounded-2xl border border-white/[.07] bg-seed-900/40 p-4 transition hover:border-seed-gold/30">
                    <a :href="c.file_url || null" target="_blank" rel="noopener" class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl border border-seed-gold/30 bg-seed-gold/10 text-seed-gold" :class="!c.file_url && 'pointer-events-none'">
                        <template x-if="c.file_url && isImage(c.file_type)"><img :src="c.file_url" :alt="c.title" class="h-full w-full object-cover"></template>
                        <template x-if="c.file_url && !isImage(c.file_type)"><span class="flex flex-col items-center"><x-icon name="award" :size="20" /><span class="mt-0.5 font-mono text-[9px] font-extrabold">PDF</span></span></template>
                        <template x-if="!c.file_url"><x-icon name="award" :size="22" /></template>
                    </a>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-bold" x-text="c.title"></p>
                            <span x-show="!c.visible" class="chip chip-muted">Oculto</span>
                        </div>
                        <p class="text-sm font-semibold text-seed-teal" x-text="c.institution"></p>
                        <p class="mt-1 flex flex-wrap gap-x-3 gap-y-1 font-mono text-[11px] text-seed-dim">
                            <span x-show="c.issued_at" x-text="$seed.formatDate(c.issued_at)"></span>
                            <span x-show="c.category" x-text="c.category"></span>
                            <span x-show="c.credential_id" x-text="`ID ${c.credential_id}`"></span>
                            <span x-text="`orden ${c.sort_order ?? 0}`"></span>
                        </p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <a x-show="c.file_url" :href="c.file_url" target="_blank" rel="noopener" class="chip chip-cyan hover:brightness-125"><x-icon name="download" :size="11" /> <span x-text="(c.file_type || 'archivo').toUpperCase()"></span></a>
                            <a x-show="c.verification_url" :href="c.verification_url" target="_blank" rel="noopener noreferrer" class="chip chip-lime hover:brightness-125"><x-icon name="external" :size="11" /> Verificar</a>
                            <span class="flex-1"></span>
                            <button type="button" class="btn btn-sm btn-ghost !px-2" @click="patch(c, { visible: !c.visible }, c.visible ? 'Certificado oculto.' : 'Certificado visible.')" :title="c.visible ? 'Ocultar' : 'Mostrar'">
                                <x-icon name="eye-off" :size="15" x-show="c.visible" /><x-icon name="eye" :size="15" x-show="!c.visible" />
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" @click="edit(c)"><x-icon name="settings" :size="14" /> Editar</button>
                            <button type="button" class="btn-icon !h-8 !w-8 hover:!border-seed-coral/40 hover:!text-seed-coral" @click="askDelete(c)" aria-label="Eliminar"><x-icon name="trash" :size="14" /></button>
                        </div>
                    </div>
                </article>
            </template>
        </div>
        <x-admin.pagination />
    </section>

    <x-admin.form-modal create="Nuevo certificado" edit="Editar certificado" submit="Crear certificado">
        {{-- Archivo del certificado: PDF o imagen --}}
        <div class="sm:col-span-2">
            <p class="form-label">Archivo del certificado <span class="font-normal text-seed-dim">(PDF o imagen)</span></p>
            <div class="relative rounded-2xl border-2 border-dashed p-4 transition"
                 :class="dragging ? 'border-seed-lime bg-seed-lime/10' : (file || currentFile ? 'border-seed-teal/40 bg-seed-teal/5' : 'border-white/15 bg-seed-900/40 hover:border-seed-teal/50')"
                 @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dropFile($event)">

                {{-- Sin archivo --}}
                <button type="button" x-show="!file && !currentFile" class="flex w-full flex-col items-center gap-2 py-5 text-center" @click="$refs.fileInput.click()">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-seed-teal/30 bg-seed-teal/10 text-seed-teal"><x-icon name="download" :size="24" class="rotate-180" /></span>
                    <span class="text-sm font-bold">Arrastra aquí el PDF o la imagen</span>
                    <span class="text-xs text-seed-muted">o <span class="font-bold text-seed-cyan underline">elige un archivo desde tu PC</span></span>
                    <span class="mt-1 flex flex-wrap justify-center gap-1.5"><span class="chip chip-muted">PDF</span><span class="chip chip-muted">JPG</span><span class="chip chip-muted">PNG</span><span class="chip chip-muted">WEBP</span><span class="chip chip-muted">máx. 10 MB</span></span>
                </button>

                {{-- Archivo nuevo o actual --}}
                <div x-show="file || currentFile" x-cloak class="flex items-center gap-4">
                    <span class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-xl border border-white/10 bg-seed-900">
                        <template x-if="file && filePreview"><img :src="filePreview" alt="" class="h-full w-full object-cover"></template>
                        <template x-if="!file && currentFile && isImage(currentFile.type)"><img :src="currentFile.url" alt="" class="h-full w-full object-cover"></template>
                        <template x-if="(file && !filePreview) || (!file && currentFile && !isImage(currentFile.type))">
                            <span class="flex flex-col items-center text-seed-coral"><x-icon name="folder" :size="22" /><span class="mt-1 font-mono text-[10px] font-extrabold">PDF</span></span>
                        </template>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold" x-text="file ? file.name : 'Archivo actual'"></p>
                        <p class="text-xs text-seed-muted" x-text="file ? `${(file.size / 1024 / 1024).toFixed(2)} MB · se subirá al guardar` : (currentFile?.type || '').toUpperCase()"></p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-cool" @click="$refs.fileInput.click()"><x-icon name="refresh" :size="13" /> <span x-text="file || currentFile ? 'Cambiar archivo' : 'Elegir archivo'"></span></button>
                            <a x-show="!file && currentFile" :href="currentFile?.url" target="_blank" rel="noopener" class="btn btn-sm btn-secondary"><x-icon name="external" :size="13" /> Ver actual</a>
                            <button type="button" x-show="file" class="btn btn-sm btn-ghost border border-white/10" @click="removeFile()"><x-icon name="x" :size="13" /> Quitar</button>
                        </div>
                    </div>
                </div>
                <input type="file" x-ref="fileInput" class="sr-only" accept="application/pdf,image/jpeg,image/png,image/webp,.pdf,.jpg,.jpeg,.png,.webp" @change="pickFile($event)">
            </div>
            <p class="form-error" x-show="fileError || err('file')" x-text="fileError || err('file')"></p>
        </div>

        <x-admin.field name="title" label="Título" required :full="true" placeholder="Ej.: Laravel Avanzado" />
        <x-admin.field name="institution" label="Institución" required placeholder="Ej.: Platzi" />
        <x-admin.field name="issued_at" label="Fecha de emisión" type="date" />
        <x-admin.field name="category" label="Categoría" placeholder="Ej.: Backend" />
        <x-admin.field name="credential_id" label="ID de credencial" placeholder="Opcional" />
        <x-admin.field name="verification_url" label="URL de verificación" type="url" icon="external" :full="true" placeholder="https://…" />
        <x-admin.field name="description" label="Descripción" type="textarea" :rows="3" :full="true" />

        <x-admin.field name="sort_order" label="Orden" type="number" hint="Menor número = aparece primero." />
        <div class="flex items-end"><x-admin.toggle name="visible" label="Visible en el portafolio" class="w-full" /></div>
    </x-admin.form-modal>

    <x-admin.confirm-delete label="target?.title" what="este certificado" warning="También se eliminará el archivo adjunto." />
</div>
</x-layouts.dashboard>
