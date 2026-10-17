<x-layouts.dashboard title="Perfil y CV" heading="Perfil y CV">
<div x-data="adminProfile()" class="space-y-6">
    <x-page-header eyebrow="Portafolio" title="Perfil y CV" subtitle="Tus datos públicos: aparecen en el inicio, la sección «Sobre mí» y el contacto." icon="user">
        <x-slot:actions>
            <a href="{{ route('home') }}#sobre-mi" target="_blank" class="btn btn-md btn-secondary"><x-icon name="external" :size="16" /> Ver en el sitio</a>
        </x-slot:actions>
    </x-page-header>

    <div x-show="loading"><x-loading-state type="list" :count="3" /></div>

    <div x-cloak x-show="!loading" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <form class="panel space-y-6" @submit.prevent="save()" novalidate>
            <div x-show="formError" x-cloak><x-alert type="error"><span x-text="formError"></span></x-alert></div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.section-title icon="user" title="Datos principales" />
                <x-admin.field name="full_name" label="Nombre" required />
                <x-admin.field name="professional_title" label="Título profesional" placeholder="Ej.: Desarrollador Full Stack" />
                <x-admin.field name="location" label="Ubicación" icon="map-pin" placeholder="Ej.: Juliaca, Perú" :full="true" />
                <x-admin.field name="bio" label="Bio" type="textarea" :rows="5" :full="true" placeholder="Cuéntale a los visitantes quién eres y qué haces…" />

                <x-admin.section-title icon="globe" title="Redes y enlaces" description="Deja vacío lo que no quieras mostrar." />
                <x-admin.field name="github_url" label="GitHub" type="url" icon="github" placeholder="https://github.com/usuario" />
                <x-admin.field name="linkedin_url" label="LinkedIn" type="url" icon="linkedin" placeholder="https://linkedin.com/in/usuario" />
                <x-admin.field name="website_url" label="Sitio web" type="url" icon="globe" placeholder="https://…" />
                <x-admin.field name="tiktok_url" label="TikTok" type="url" icon="play" placeholder="https://tiktok.com/@usuario" />
                <x-admin.field name="youtube_url" label="YouTube" type="url" icon="youtube" placeholder="https://youtube.com/@canal" />
                <x-admin.field name="instagram_url" label="Instagram" type="url" icon="instagram" placeholder="https://instagram.com/usuario" />
                <x-admin.field name="whatsapp_url" label="WhatsApp" icon="whatsapp" placeholder="https://wa.me/51980814183" hint="El formulario de contacto abre WhatsApp hacia este número." :full="true" />
            </div>

            <div class="flex justify-end border-t border-white/5 pt-5">
                <button type="submit" class="btn btn-md btn-primary" :disabled="saving">
                    <span x-show="!saving" class="inline-flex items-center gap-2"><x-icon name="check" :size="16" /> Guardar perfil</span>
                    <span x-show="saving" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Guardando…</span>
                </button>
            </div>
        </form>

        {{-- CV --}}
        <section class="panel h-fit">
            <h2 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="download" :size="19" class="text-seed-teal" /> Currículum (CV)</h2>
            <p class="mt-1 text-xs text-seed-muted">PDF de hasta 10 MB. Se muestra el botón «Descargar CV» en el portafolio.</p>

            <div class="mt-5 rounded-xl border p-4" :class="cvUrl ? 'border-seed-lime/30 bg-seed-lime/5' : 'border-dashed border-white/15'">
                <template x-if="cvUrl">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-seed-lime/15 font-mono text-[11px] font-extrabold text-seed-lime">PDF</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold">CV publicado</p>
                            <a :href="cvUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-bold text-seed-cyan hover:underline">Abrir <x-icon name="external" :size="11" /></a>
                        </div>
                    </div>
                </template>
                <p x-show="!cvUrl" class="text-center text-sm text-seed-muted">Aún no has subido tu CV.</p>
            </div>

            <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-xl border border-white/10 bg-seed-900/40 p-3 transition hover:border-seed-teal/50">
                <x-icon name="plus" :size="18" class="text-seed-teal" />
                <span class="min-w-0 flex-1 truncate text-sm" x-text="cvFile ? cvFile.name : (cvUrl ? 'Elegir nuevo PDF para reemplazar' : 'Elegir PDF')"></span>
                <input type="file" x-ref="cvInput" accept="application/pdf,.pdf" class="sr-only" @change="pickCv($event)">
            </label>
            <div class="mt-3 grid gap-2">
                <button type="button" class="btn btn-md btn-primary" @click="uploadCv()" :disabled="!cvFile || cvBusy">
                    <x-icon name="check" :size="16" /> <span x-text="cvBusy ? 'Subiendo…' : (cvUrl ? 'Reemplazar CV' : 'Subir CV')"></span>
                </button>
                <button type="button" x-show="cvUrl" class="btn btn-md btn-danger" @click="$store.modal.open('delete-cv')" :disabled="cvBusy"><x-icon name="trash" :size="16" /> Eliminar CV</button>
            </div>
        </section>
    </div>

    <x-modal name="delete-cv" max-width="max-w-md" title="¿Eliminar el CV?">
        <p class="mt-3 text-sm leading-6 text-seed-muted">El botón «Descargar CV» dejará de mostrarse en el portafolio.</p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="btn btn-md btn-ghost" @click="$store.modal.close()">Cancelar</button>
            <button type="button" class="btn btn-md btn-danger" @click="deleteCv()" :disabled="cvBusy"><x-icon name="trash" :size="16" /> Eliminar</button>
        </div>
    </x-modal>
</div>
</x-layouts.dashboard>
