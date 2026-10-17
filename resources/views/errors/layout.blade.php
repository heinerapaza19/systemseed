{{-- Páginas de error con estilos propios (no dependen de Vite ni de la base de datos) --}}
@php
    $code = $code ?? 500;
    $tone = $tone ?? 'lime';
    $accent = ['lime' => '#9cff3b', 'gold' => '#ffc83d', 'cyan' => '#2ee6d6'][$tone] ?? '#9cff3b';
@endphp
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<meta name="theme-color" content="#031318">
<title>{{ $code }} · {{ $heading }} | SystemSeed</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='23' fill='%2307171c'/%3E%3Cpath d='M23 21C15 22 10.5 17 10 10.5c7.5-.5 12.5 3.5 13 10.5Z' fill='%23b8ff39'/%3E%3Cpath d='M25 21c8 1 12.5-4 13-10.5-7.5-.5-12.5 3.5-13 10.5Z' fill='%2364ff58'/%3E%3Cellipse cx='24' cy='31' rx='5.6' ry='7' fill='%23ffc83d'/%3E%3C/svg%3E">
<style>
    *{box-sizing:border-box}
    html,body{margin:0;min-height:100%}
    body{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:#e8f3f1;background:#031318;
        background-image:radial-gradient(60rem 30rem at 50% -10%,rgba(156,255,59,.12),transparent 60%),radial-gradient(40rem 25rem at 100% 100%,rgba(46,230,214,.08),transparent 60%);
        min-height:100vh;display:flex;flex-direction:column}
    .grid{position:fixed;inset:0;pointer-events:none;opacity:.35;background-image:linear-gradient(rgba(156,255,59,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(156,255,59,.06) 1px,transparent 1px);background-size:44px 44px;-webkit-mask-image:radial-gradient(circle at 50% 40%,#000,transparent 70%);mask-image:radial-gradient(circle at 50% 40%,#000,transparent 70%)}
    header{position:relative;padding:22px 24px;display:flex;justify-content:center}
    .brand{display:inline-flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:800;font-size:19px;letter-spacing:-.3px}
    .brand b{color:#9cff3b}
    main{position:relative;flex:1;display:grid;place-items:center;padding:24px 16px 48px}
    .card{width:100%;max-width:560px;text-align:center}
    .art{position:relative;width:132px;height:132px;margin:0 auto 18px}
    .art .ring{position:absolute;inset:0;border-radius:50%;border:1px dashed rgba(156,255,59,.25);animation:spin 28s linear infinite}
    .art .core{position:absolute;inset:18px;border-radius:50%;display:grid;place-items:center;background:#07171c;border:1px solid rgba(255,255,255,.08);box-shadow:0 0 60px -10px {{ $accent }}55}
    @keyframes spin{to{transform:rotate(360deg)}}
    .code{font-family:"JetBrains Mono",ui-monospace,Menlo,monospace;font-weight:800;font-size:clamp(64px,16vw,112px);line-height:1;letter-spacing:-4px;margin:0;
        background:linear-gradient(135deg,#9cff3b,#2ee6d6);-webkit-background-clip:text;background-clip:text;color:transparent}
    .eyebrow{font-family:"JetBrains Mono",ui-monospace,monospace;font-size:11px;font-weight:700;letter-spacing:.25em;text-transform:uppercase;color:{{ $accent }};margin:18px 0 0}
    h1{font-size:clamp(24px,5vw,32px);font-weight:800;margin:10px 0 0;letter-spacing:-.5px}
    p.msg{color:#8fb0aa;line-height:1.7;margin:12px auto 0;max-width:440px;font-size:15px}
    .actions{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:28px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:13px 22px;border-radius:14px;font-weight:700;font-size:14px;text-decoration:none;border:0;cursor:pointer;transition:transform .2s,filter .2s,background .2s}
    .btn-primary{background:linear-gradient(135deg,#9cff3b,#5cf07a);color:#062016;box-shadow:0 10px 30px -10px rgba(156,255,59,.6)}
    .btn-primary:hover{transform:translateY(-2px);filter:brightness(1.08)}
    .btn-ghost{background:rgba(255,255,255,.04);color:#e8f3f1;border:1px solid rgba(46,230,214,.35)}
    .btn-ghost:hover{background:rgba(46,230,214,.1)}
    .term{margin:34px auto 0;max-width:420px;text-align:left;font-family:"JetBrains Mono",ui-monospace,monospace;font-size:12px;background:rgba(7,23,28,.8);border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:14px 16px;color:#6f8f8a}
    .term .p{color:#2ee6d6}.term .v{color:{{ $accent }}}
    footer{position:relative;text-align:center;font-size:12px;color:#4f6d68;padding:20px}
    footer a{color:#6f8f8a}
</style>
</head>
<body>
<div class="grid"></div>
<header>
    <a class="brand" href="{{ url('/') }}">
        <svg width="34" height="34" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="23" fill="#07171c" stroke="#1f4a3d"/><path d="M23 21C15 22 10.5 17 10 10.5c7.5-.5 12.5 3.5 13 10.5Z" fill="#b8ff39"/><path d="M25 21c8 1 12.5-4 13-10.5-7.5-.5-12.5 3.5-13 10.5Z" fill="#64ff58"/><ellipse cx="24" cy="31" rx="5.6" ry="7" fill="#ffc83d"/></svg>
        <span>System<b>Seed</b></span>
    </a>
</header>
<main>
    <div class="card">
        <div class="art">
            <span class="ring"></span>
            <span class="core">{!! $icon !!}</span>
        </div>
        <p class="code">{{ $code }}</p>
        <p class="eyebrow">{{ $eyebrow }}</p>
        <h1>{{ $heading }}</h1>
        <p class="msg">{{ $message }}</p>
        <div class="actions">
            @if(! empty($primary))
                <a class="btn btn-primary" href="{{ $primary[1] }}">{{ $primary[0] }}</a>
            @else
                <a class="btn btn-primary" href="{{ url('/') }}">Ir al inicio</a>
            @endif
            @if(empty($hideBack))
                <a class="btn btn-ghost" href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/proyectos') }}" onclick="if (history.length > 1) { history.back(); return false; }">← Volver atrás</a>
            @endif
        </div>
        <div class="term" aria-hidden="true">
            <div><span class="p">$</span> seed status --code {{ $code }}</div>
            <div>→ <span class="v">{{ $status }}</span></div>
        </div>
    </div>
</main>
<footer>© {{ date('Y') }} SystemSeed · <a href="{{ url('/proyectos') }}">Ver proyectos</a></footer>
</body>
</html>
