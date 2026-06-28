<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('restotech-standard.package.name'))</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f6f7fb; color: #1f2937; }
        header, main { max-width: 1120px; margin: 0 auto; padding: 1rem; }
        header { display: flex; justify-content: space-between; align-items: center; }
        nav a { margin-right: 1rem; }
        .card { background: #fff; border-radius: 12px; padding: 1rem; box-shadow: 0 1px 3px rgba(0, 0, 0, .08); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .75rem; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
        input[type="text"], input[type="number"], textarea { width: 100%; box-sizing: border-box; padding: .6rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; }
        textarea { min-height: 120px; }
        .stack > * + * { margin-top: 1rem; }
        .actions a, .actions button { margin-right: .5rem; }
        .flash { background: #ecfeff; border: 1px solid #06b6d4; padding: .75rem 1rem; border-radius: 8px; }
        .errors { background: #fef2f2; border: 1px solid #ef4444; padding: .75rem 1rem; border-radius: 8px; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; font-weight: 600; margin-bottom: .35rem; }
        .field small { display: block; color: #6b7280; margin-top: .25rem; }
    </style>
</head>
<body>
<header>
    <strong>{{ config('restotech-standard.package.name') }}</strong>
    <nav>
        <a href="{{ route('restotech.standard.back_office.dining-areas.index') }}">Dining Areas</a>
    </nav>
</header>
<main>
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
