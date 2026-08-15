<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
</head>

<body class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] flex flex-col">
    <header class="w-full flex items-center justify-between px-8 py-6">

        <a href="/" class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="CitaSync" class="h-10 w-10">

            <span class="text-xl font-semibold tracking-tight">
                CitaSync
            </span>
        </a>

        @if (Route::has('login'))
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="rounded-lg border px-4 py-2 text-sm hover:bg-zinc-100 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm hover:text-black transition">
                        Iniciar Sesión
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="rounded-lg border px-4 py-2 text-sm hover:bg-zinc-100 transition">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </nav>
        @endif

    </header>
    <main class="flex flex-1 items-center justify-center">
        <div class="text-center">

            <h1 class="text-6xl font-bold tracking-tight">
                CitaSync
            </h1>

            <p class="mt-5 text-lg text-zinc-500">
                Sistema de gestión de citas médicas.
            </p>

        </div>
    </main>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>

</html>
