<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Agendar cita - {{ config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
</head>

<body class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] flex flex-col">

    <header class="w-full flex items-center px-8 py-6">

        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="CitaSync" class="h-10 w-10">
            <span class="text-xl font-semibold tracking-tight">
                CitaSync
            </span>
        </a>

    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-2xl rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight">
                    Agendar cita médica
                </h1>

                <p class="mt-2 text-zinc-500">
                    Complete la siguiente información para solicitar una cita.
                </p>

            </div>

            <form class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:input label="Nombres" placeholder="Juan Carlos" />
                    <flux:input label="Apellidos" placeholder="Pérez Gómez" />
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <flux:input label="DNI" maxlength="8" placeholder="12345678" />
                    <flux:input label="Teléfono" placeholder="999888777" />
                </div>

                <flux:select label="Tipo de consulta">
                    <option value="">
                        Seleccione una opción
                    </option>

                    {{-- @foreach ($consultationTypes as $type) --}}
                    {{-- <option value="{{ $type->id }}">{{ $type->name }}</option> --}}
                    {{-- @endforeach --}}

                </flux:select>

                <div class="grid gap-6 md:grid-cols-2">
                    <flux:input type="date" label="Fecha deseada" />
                    <flux:input type="time" label="Hora deseada" />
                </div>

                <div class="pt-2">
                    <flux:button variant="primary" class="w-full">
                        Solicitar cita
                    </flux:button>

                </div>
            </form>
        </div>
    </main>
</body>

</html>
