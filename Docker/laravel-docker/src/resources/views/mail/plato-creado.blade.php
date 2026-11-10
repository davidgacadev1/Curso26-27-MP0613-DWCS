<h1>¡Plato nuevo!</h1>
<p>Se ha añadido <strong>{{ $plato->nombre }}</strong> ({{ $plato->tipo }}) a {{ $plato->precioFormateado() }}.</p>
@if ($plato->vegano)
    <p>🌱 Es vegano.</p>
@endif
