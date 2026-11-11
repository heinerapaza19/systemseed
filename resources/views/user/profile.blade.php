@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $stats = [
        ['Favoritos', $user->favoriteProjects()->count(), 'heart', 'text-seed-lime', $isAdmin ? null : route('user.favorites')],
        ['Plantillas', $user->savedTemplates()->count(), 'layers', 'text-seed-gold', $isAdmin ? null : route('user.templates')],
        ['Siguiendo', $user->followedProjects()->count(), 'radar', 'text-seed-cyan', $isAdmin ? null : route('user.following')],
    ];
    $avatarUrl = $user->avatar
        ? (str_starts_with($user->avatar, 'http://') || str_starts_with($user->avatar, 'https://')
            ? $user->avatar
            : \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar))
        : null;
@endphp
<x-layouts.dashboard :title="$isAdmin ? 'Configuración' : 'Mi perfil'">
<div class="space-y-6">
    <x-page-header eyebrow="Cuenta" :title="$isAdmin ? 'Configuración' : 'Mi perfil'" subtitle="Los datos de tu cuenta en SystemSeed." :icon="$isAdmin ? 'settings' : 'user'">
        <x-slot:actions>
            <a href="{{ $isAdmin ? route('user.security') : route('user.security') }}" class="btn btn-md btn-secondary"><x-icon name="shield" :size="16" /> Seguridad</a>
        </x-slot:actions>
    </x-page-header>

    {{-- Portada del perfil --}}
    <section class="relative overflow-hidden rounded-3xl border border-seed-lime/20 bg-gradient-to-br from-seed-700 via-seed-800 to-seed-900 shadow-seed">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-50 mask-radial"></div>
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-seed-cyan/10 blur-3xl"></div>
        <div class="relative flex flex-col items-center gap-5 p-6 text-center sm:flex-row sm:items-start sm:p-8 sm:text-left">
            <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data"
                  class="flex shrink-0 flex-col items-center gap-3"
                  x-data="accountAvatar(@js($avatarUrl))" x-init="sending = false" @submit="sending = true">
                @csrf
                @method('PATCH')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <div class="group relative h-24 w-24">
                    <img x-show="preview" :src="preview" alt="Vista previa de tu foto" class="h-24 w-24 rounded-full object-cover ring-4 ring-seed-teal/25">
                    <div x-show="!preview"><x-avatar size="xl" class="!h-24 !w-24 !text-3xl" /></div>
                    <label for="avatar" class="absolute inset-0 grid cursor-pointer place-items-center rounded-full bg-seed-950/0 text-transparent transition duration-200 group-hover:bg-seed-950/75 group-hover:text-white" title="Cambiar foto">
                        <span class="flex flex-col items-center gap-1 text-[10px] font-bold"><x-icon name="image" :size="20" /> Cambiar</span>
                    </label>
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" x-ref="avatarInput"
                           @change="pickAvatar($event)">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary" :disabled="sending || !$refs.avatarInput?.files?.length">
                        <x-icon name="check" :size="13" /> <span x-text="sending ? 'Guardando…' : 'Guardar foto'"></span>
                    </button>
                    @if($user->avatar)
                        <button type="submit" name="remove_avatar" value="1" class="btn btn-sm btn-danger" :disabled="sending"
                                @click="$refs.avatarInput.value = ''; preview = null" aria-label="Eliminar foto" title="Eliminar foto">
                            <x-icon name="trash" :size="13" />
                        </button>
                    @endif
                </div>
                @error('avatar')<p class="max-w-48 text-center text-xs text-seed-coral">{{ $message }}</p>@enderror
                <p class="max-w-48 text-center text-[10px] leading-4 text-seed-muted">JPG, PNG o WEBP · máximo 4 MB</p>

                <div x-cloak x-show="cropOpen" x-transition.opacity class="fixed inset-0 z-[300] grid place-items-center bg-seed-950/90 p-4 backdrop-blur-md" @keydown.escape.window="cropOpen && cancelCrop()">
                    <section class="w-full max-w-md rounded-3xl border border-seed-teal/25 bg-seed-800 p-5 shadow-seed sm:p-6" @click.stop>
                        <div class="flex items-start justify-between gap-4">
                            <div><h3 class="text-lg font-extrabold">Ajustar foto</h3><p class="mt-1 text-xs text-seed-muted">Arrastra para encuadrar y usa el control de zoom.</p></div>
                            <button type="button" class="btn-icon !h-9 !w-9" @click="cancelCrop()" aria-label="Cerrar editor"><x-icon name="x" :size="16" /></button>
                        </div>
                        <div class="mx-auto mt-6 aspect-square w-full max-w-[300px] overflow-hidden rounded-full border-4 border-seed-lime/30 bg-seed-950 shadow-glow touch-none">
                            <canvas x-ref="cropCanvas" width="640" height="640" class="h-full w-full cursor-move select-none"
                                    @pointerdown="startDrag($event)" @pointermove="drag($event)" @pointerup="stopDrag()" @pointercancel="stopDrag()"></canvas>
                        </div>
                        <label class="mt-6 block">
                            <span class="form-label flex items-center justify-between"><span>Zoom</span><span class="font-mono text-seed-lime" x-text="`${Math.round(zoom * 100)}%`"></span></span>
                            <input type="range" min="1" max="3" step="0.01" x-model.number="zoom" @input="drawCrop()" class="avatar-zoom w-full">
                        </label>
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button type="button" class="btn btn-md btn-secondary" @click="cancelCrop()">Cancelar</button>
                            <button type="button" class="btn btn-md btn-primary" @click="applyCrop()"><x-icon name="check" :size="16" /> Usar foto</button>
                        </div>
                    </section>
                </div>
            </form>
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-black sm:text-3xl">{{ $user->name }}</h2>
                <p class="mt-1 text-seed-muted">{{ $user->email }}</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2 sm:justify-start">
                    @if($isAdmin)
                        <span class="chip chip-lime !px-3 !py-1.5"><x-icon name="shield" :size="12" /> Administrador</span>
                    @else
                        <span class="chip chip-cyan !px-3 !py-1.5"><x-icon name="user" :size="12" /> Miembro</span>
                    @endif
                    @if($user->email_verified_at)
                        <span class="chip chip-teal !px-3 !py-1.5"><x-icon name="check-circle" :size="12" /> Correo verificado</span>
                    @else
                        <a href="{{ route('user.security') }}" class="chip chip-gold !px-3 !py-1.5 hover:brightness-125"><x-icon name="mail" :size="12" /> Correo sin verificar · verificar</a>
                    @endif
                    @if($user->google_id)
                        <span class="chip chip-muted !px-3 !py-1.5">Google conectado</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <section class="panel">
            <h3 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="info" :size="19" class="text-seed-teal" /> Datos de la cuenta</h3>

            {{-- Editar nombre --}}
            <form method="POST" action="{{ route('user.profile.update') }}" class="mt-5" x-data="{ name: @js(old('name', $user->name)), original: @js($user->name), sending: false }" @submit="sending = true">
                @csrf
                @method('PATCH')
                <label class="form-label" for="name">Nombre completo</label>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="relative flex-1">
                        <x-icon name="user" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                        <input id="name" name="name" x-model="name" maxlength="255" autocomplete="name" required
                               class="form-input form-input-icon @error('name') !border-seed-coral/60 @enderror" placeholder="Tu nombre y apellido">
                    </div>
                    <button type="submit" class="btn btn-md btn-primary shrink-0" :disabled="sending || !name.trim() || name.trim() === original">
                        <x-icon name="check" :size="16" /> <span x-text="sending ? 'Guardando…' : 'Guardar nombre'"></span>
                    </button>
                </div>
                @error('name')<p class="form-error">{{ $message }}</p>@else<p class="form-hint">Así te verán en tu panel y en tus mensajes.</p>@enderror
            </form>

            <dl class="mt-5 divide-y divide-white/5 border-t border-white/5 text-sm">
                @foreach([
                    ['Correo', $user->email],
                    ['Rol', $isAdmin ? 'Administrador' : 'Usuario'],
                    ['Estado', $user->is_active ? 'Activa' : 'Inactiva'],
                    ['Correo verificado', $user->email_verified_at ? $user->email_verified_at->translatedFormat('d M Y') : 'Pendiente'],
                    ['Miembro desde', $user->created_at?->translatedFormat('d \d\e F \d\e Y')],
                ] as [$label, $value])
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-seed-muted">{{ $label }}</dt>
                        <dd class="truncate text-right font-semibold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="mt-4 flex items-start gap-2 rounded-xl border border-white/5 bg-seed-900/50 p-3 text-xs leading-5 text-seed-muted">
                <x-icon name="info" :size="14" class="mt-0.5 shrink-0 text-seed-cyan" />
                El correo no se puede cambiar desde aquí porque se usa para iniciar sesión. Si lo necesitas, escríbenos desde Contacto.
            </p>
        </section>

        <div class="space-y-6">
            @unless($isAdmin)
                <section class="panel">
                    <h3 class="text-lg font-extrabold">Tu actividad</h3>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        @foreach($stats as [$label, $value, $icon, $tone, $href])
                            <a href="{{ $href }}" class="rounded-xl border border-white/[.06] bg-seed-900/40 p-4 text-center transition hover:-translate-y-0.5 hover:border-seed-cyan/30">
                                <x-icon :name="$icon" :size="20" class="mx-auto {{ $tone }}" />
                                <p class="mt-2 font-mono text-2xl font-extrabold {{ $tone }}">{{ $value }}</p>
                                <p class="text-[11px] text-seed-muted">{{ $label }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endunless
            <section class="panel">
                <h3 class="text-lg font-extrabold">Accesos de cuenta</h3>
                <div class="mt-4 grid gap-2">
                    <a href="{{ route('user.security') }}" class="side-link side-link-cyan border border-white/[.06] !py-3"><x-icon name="key" :size="17" /> Cambiar contraseña <x-icon name="chevron-right" :size="15" class="ml-auto" /></a>
                    @unless($isAdmin)
                        <a href="{{ route('user.contact') }}" class="side-link side-link-cyan border border-white/[.06] !py-3"><x-icon name="mail" :size="17" /> Contactar a SystemSeed <x-icon name="chevron-right" :size="15" class="ml-auto" /></a>
                    @endunless
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-md btn-danger mt-2 w-full"><x-icon name="logout" :size="16" /> Cerrar sesión</button></form>
                </div>
            </section>
        </div>
    </div>
</div>
</x-layouts.dashboard>
