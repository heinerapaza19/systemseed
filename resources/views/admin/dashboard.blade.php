@php
    use App\Models\ContactMessage;
    use App\Models\Project;
    use App\Models\Technology;
    use App\Models\User;
    use Illuminate\Support\Str;

    $admin = auth()->user();
    $unread = ContactMessage::where('is_read', false)->count();
    $stats = [
        ['Proyectos', Project::count(), Project::where('status', 'published')->count().' publicados', 'folder', route('admin.projects')],
        ['Tecnologías', Technology::count(), 'Stack actualizado', 'zap', route('admin.technologies')],
        ['Usuarios', User::count(), User::where('role', 'user')->count().' registrados', 'users', route('admin.users')],
        ['Mensajes', ContactMessage::count(), $unread.' sin leer', 'mail', route('admin.messages')],
    ];
    $messages = ContactMessage::latest()->take(5)->get();
    $projects = Project::with('technologies')->latest('updated_at')->take(5)->get();
    $statusLabel = ['published' => 'Publicado', 'draft' => 'Borrador', 'archived' => 'Archivado'];
    $statusTone = ['published' => 'chip-lime', 'draft' => 'chip-gold', 'archived' => 'chip-muted'];
@endphp
<x-layouts.dashboard title="Panel administrativo" heading="Panel administrativo" subtitle="SystemSeed">
<div class="space-y-7">
    {{-- Bienvenida --}}
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-start">
        <div>
            <p class="font-mono text-[10px] font-extrabold uppercase tracking-[.18em] text-seed-teal">Administración</p>
            <h1 class="mt-2 text-3xl font-extrabold sm:text-[37px]">Bienvenido, <em class="not-italic text-seed-lime">{{ Str::of($admin->name)->before(' ') }}</em></h1>
            <p class="mt-2 text-seed-muted">Gestiona el contenido de tu portafolio desde aquí.</p>
        </div>
        <span class="inline-flex shrink-0 items-center gap-2 self-start rounded-full border border-seed-lime/25 px-3.5 py-2 text-[11px] font-semibold text-seed-lime">
            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-seed-lime"></span> Sesión activa
        </span>
    </div>

    {{-- Métricas --}}
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach($stats as [$label, $value, $hint, $icon, $href])
            <a href="{{ $href }}" class="group rounded-2xl border border-seed-teal/20 bg-seed-panel p-5 transition hover:-translate-y-0.5 hover:border-seed-lime/40">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-seed-muted">{{ $label }}</span>
                    <x-icon :name="$icon" :size="17" class="text-seed-dim transition group-hover:text-seed-lime" />
                </div>
                <strong class="mt-2 block font-mono text-[31px] font-extrabold text-seed-lime">{{ $value }}</strong>
                <small class="block text-[11px] {{ $label === 'Mensajes' && $unread ? 'font-bold text-seed-gold' : 'text-seed-muted' }}">{{ $hint }}</small>
            </a>
        @endforeach
    </section>

    <div class="grid gap-5 xl:grid-cols-[1.5fr_.7fr]">
        <div class="min-w-0 space-y-5">
            {{-- Mensajes recientes --}}
            <section class="panel">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="flex items-center gap-2 text-lg font-extrabold">
                        Mensajes recientes
                        @if($unread)<span class="rounded-full bg-seed-gold px-2 py-0.5 font-mono text-[10px] font-extrabold text-seed-900">{{ $unread }} nuevos</span>@endif
                    </h3>
                    <a href="{{ route('admin.messages') }}" class="btn btn-sm btn-primary">Ver todos</a>
                </div>
                <div class="mt-3">
                    @forelse($messages as $message)
                        <a href="{{ route('admin.messages') }}?id={{ $message->id }}" class="flex items-center justify-between gap-4 border-b border-white/5 py-3.5 transition last:border-0 hover:opacity-90">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-xs font-black {{ $message->is_read ? 'bg-seed-500 text-seed-muted' : 'bg-gradient-to-br from-seed-teal to-seed-cyan text-seed-900' }}">{{ Str::upper(Str::substr($message->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <b class="block truncate text-sm {{ $message->is_read ? 'font-semibold text-slate-300' : '' }}">{{ $message->name }} <span class="font-normal text-seed-dim">· {{ $message->subject ?: 'Sin asunto' }}</span></b>
                                    <span class="block truncate text-[11px] text-seed-muted">{{ Str::limit($message->message, 90) }}</span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <em class="block text-[10px] not-italic {{ $message->is_read ? 'text-seed-dim' : 'font-bold text-seed-gold' }}">{{ $message->is_read ? 'Leído' : 'Nuevo' }}</em>
                                <span class="block font-mono text-[10px] text-seed-dim">{{ $message->created_at?->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="py-6 text-sm text-seed-muted">Todavía no llegan mensajes. Aparecerán aquí apenas alguien escriba desde el formulario de contacto.</p>
                    @endforelse
                </div>
            </section>

            {{-- Proyectos recientes --}}
            <section class="panel">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-extrabold">Proyectos recientes</h3>
                    <a href="{{ route('admin.projects') }}" class="btn btn-sm btn-primary">Ver proyectos</a>
                </div>
                <div class="mt-3">
                    @forelse($projects as $project)
                        <div class="flex items-center justify-between gap-4 border-b border-white/5 py-3.5 last:border-0">
                            <div class="min-w-0">
                                <b class="block truncate">{{ $project->title }}</b>
                                <span class="block truncate text-[11px] text-seed-muted">{{ $project->technologies->pluck('name')->take(4)->implode(' · ') ?: 'Sin tecnologías' }}</span>
                            </div>
                            <span class="chip {{ $statusTone[$project->status] ?? 'chip-muted' }} shrink-0">{{ $statusLabel[$project->status] ?? $project->status }}</span>
                        </div>
                    @empty
                        <p class="py-6 text-sm text-seed-muted">Todavía no hay proyectos registrados.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Perfil --}}
        <section class="panel h-fit text-center">
            <x-avatar size="xl" class="mx-auto mt-1" />
            <h3 class="mt-3 text-lg font-extrabold">{{ $admin->name }}</h3>
            <p class="font-semibold text-seed-lime">Administrador</p>
            <small class="text-seed-muted">{{ $admin->email }}</small>
            <code class="mt-4 block rounded-lg bg-seed-950 px-3 py-2 font-mono text-xs text-seed-teal">role: admin</code>
            <code class="mt-2 block rounded-lg bg-seed-950 px-3 py-2 font-mono text-xs text-seed-teal">status: {{ $admin->is_active ? 'active' : 'inactive' }}</code>
            <div class="mt-5 grid gap-2">
                <x-button :href="route('admin.settings')" variant="secondary" size="sm" icon="settings">Configuración</x-button>
                <x-button :href="route('home')" variant="ghost" size="sm" icon="globe" class="border border-white/10">Ver portafolio público</x-button>
            </div>
        </section>
    </div>
</div>
</x-layouts.dashboard>
