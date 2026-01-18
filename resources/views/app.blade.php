<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="color-scheme" content="light dark" />
    <style>
        html,
        body {
            background-color: #ecfeff;
        }

        @media (prefers-color-scheme: dark) {
            html,
            body {
                background-color: #020617;
            }
        }
    </style>
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @inertiaHead
</head>
<body class="min-h-screen text-slate-900 dark:text-slate-100 antialiased bg-gradient-to-br from-teal-50 via-slate-50 to-cyan-100 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 transition-colors relative overflow-x-hidden nativephp-safe-area">
    @inertia
</body>
</html>
