<?php

namespace App\Http\Controllers;

use App\Mail\PlatoCreado;
use App\Models\Plato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PlatoController extends Controller
{
    private const TIPOS = ['entrante' => 'Entrantes', 'principal' => 'Principales', 'postre' => 'Postres'];

    /** GET /carta — la carta agrupada por tipo (se guarda 60 s en la caché de Redis) */
    public function index(): View
    {
        // En la caché se guardan arrays, no objetos: desde Laravel 13 la caché no
        // deserializa clases (config/cache.php, serializable_classes) por seguridad.
        // hydrate() vuelve a crear los modelos Plato a partir de los arrays.
        $platos = Plato::hydrate(
            Cache::remember('carta', 60, fn () => Plato::orderBy('nombre')->get()->toArray())
        );

        return view('carta', [
            'tipos'  => self::TIPOS,
            'carta'  => $platos->groupBy('tipo'),
            'visitas' => Cache::increment('visitas-carta'),
        ]);
    }

    /** POST /platos — añade un plato desde el formulario */
    public function store(Request $request): RedirectResponse
    {
        // Si la validación falla, Laravel vuelve al formulario con los errores y los datos
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'unique:platos,nombre'],
            'tipo'   => ['required', 'in:' . implode(',', array_keys(self::TIPOS))],
            'precio' => ['required', 'numeric', 'min:0', 'max:999'],
            'vegano' => ['nullable', 'boolean'],
        ]);
        $datos['vegano'] = $request->boolean('vegano');

        $plato = Plato::create($datos);
        Cache::forget('carta');

        // El correo se pone en la cola (Redis) y lo envía el servicio "queue" a Mailpit
        Mail::to('cocina@barbaio.test')->queue(new PlatoCreado($plato));

        return redirect()->route('carta')->with('aviso', "Añadido: {$plato->nombre}");
    }

    /** GET /api/carta — la carta en JSON */
    public function api(): JsonResponse
    {
        return response()->json(Plato::orderBy('tipo')->orderBy('nombre')->get(['id', 'nombre', 'tipo', 'precio', 'vegano']));
    }
}
