<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('restotech-standard.package.name') }} POS</title>
    @vite(['resources/js/pos/app.js'])
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #0f172a; color: #e2e8f0; }
        main { min-height: 100vh; padding: 1.5rem; }
        .shell { max-width: 960px; margin: 0 auto; background: #111827; border-radius: 16px; padding: 1.25rem; }
        .grid { display: grid; gap: 1rem; }
        label { display: block; margin-bottom: .5rem; }
        input, button, pre { font: inherit; }
        input { width: 100%; box-sizing: border-box; padding: .75rem; border-radius: 10px; border: 1px solid #334155; background: #0b1220; color: inherit; }
        button { padding: .75rem 1rem; border: 0; border-radius: 10px; background: #38bdf8; color: #082f49; font-weight: 700; cursor: pointer; }
        button:disabled { opacity: .6; cursor: not-allowed; }
        .status { color: #86efac; }
        .error { color: #fca5a5; }
        pre { background: #020617; padding: 1rem; border-radius: 12px; overflow: auto; }
    </style>
</head>
<body>
<main>
    <div
        id="restotech-pos-app"
        data-restotech-pos-app
        data-open-table-session-endpoint="{{ $openTableSessionEndpoint }}"
        data-csrf-token="{{ csrf_token() }}"
    >
        <section class="shell grid">
            <header>
                <h1>Restotech POS</h1>
                <p>Vue/Vite shell wired to the package internal session-opening endpoint.</p>
            </header>

            <div id="restotech-pos-mount"></div>
        </section>
    </div>
</main>
</body>
</html>
