<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carta · {{ config('app.name') }}</title>
    {{-- Vite: en desarrollo carga los ficheros del servidor de Vite (puerto 5173) y recarga al guardar --}}
    @vite(['resources/css/app.css'])
</head>
<body class="bg-amber-50 text-slate-800">
<main class="mx-auto max-w-2xl p-6">
    <h1 class="mb-4 text-4xl font-bold text-sky-800">Carta del Bar Baio</h1>

    @if (session('aviso'))
        <p class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('aviso') }}</p>
    @endif

    @foreach ($tipos as $tipo => $titulo)
        @if ($carta->has($tipo))
            <h2 class="mt-6 border-b-2 border-orange-700 text-2xl font-semibold text-orange-800">{{ $titulo }}</h2>
            <ul>
                @foreach ($carta[$tipo] as $plato)
                    <li class="flex justify-between border-b border-dotted py-1">
                        <span>{{ $plato->nombre }} @if ($plato->vegano) 🌱 @endif</span>
                        <strong>{{ $plato->precioFormateado() }}</strong>
                    </li>
                @endforeach
            </ul>
        @endif
    @endforeach

    <h2 class="mt-8 text-xl font-semibold">Añadir un plato</h2>
    <form method="POST" action="{{ route('platos.store') }}" class="mt-2 grid gap-2">
        @csrf {{-- campo oculto contra ataques CSRF: sin él, Laravel responde 419 --}}
        <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" class="rounded border p-2">
        <select name="tipo" class="rounded border p-2">
            @foreach ($tipos as $tipo => $titulo)
                <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $titulo }}</option>
            @endforeach
        </select>
        <input name="precio" value="{{ old('precio') }}" placeholder="Precio" class="rounded border p-2">
        <label><input type="checkbox" name="vegano" value="1" @checked(old('vegano'))> Vegano</label>
        @if ($errors->any())
            <ul class="rounded bg-red-100 p-3 text-red-800">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <button class="rounded bg-sky-800 p-2 font-semibold text-white">Guardar</button>
    </form>

    <p class="mt-8 text-sm text-slate-500">
        Visitas a la carta (contador en Redis): {{ $visitas }} ·
        <a class="underline" href="{{ url('/api/carta') }}">JSON</a>
    </p>
</main>
</body>
</html>
