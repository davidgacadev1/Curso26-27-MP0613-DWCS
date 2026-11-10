<?php

namespace Tests\Feature;

use App\Mail\PlatoCreado;
use App\Models\Plato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * docker compose exec app php artisan test
 * (phpunit.xml usa SQLite en memoria: las pruebas no tocan la base de datos MariaDB)
 */
class CartaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();      // las pruebas no necesitan el servidor de Vite
    }

    public function test_la_carta_muestra_los_platos(): void
    {
        Plato::factory()->create(['nombre' => 'Pulpo á feira', 'tipo' => 'principal', 'precio' => 16.5]);

        $this->get('/carta')
            ->assertOk()
            ->assertSee('Pulpo á feira')
            ->assertSee('16,50 €');
    }

    public function test_se_puede_anadir_un_plato_y_se_avisa_a_cocina(): void
    {
        Mail::fake();

        $this->post('/platos', ['nombre' => 'Filloas', 'tipo' => 'postre', 'precio' => '4.80', 'vegano' => '0'])
            ->assertRedirect('/carta');

        $this->assertDatabaseHas('platos', ['nombre' => 'Filloas', 'tipo' => 'postre']);
        Mail::assertQueued(PlatoCreado::class);
    }

    public function test_no_admite_precios_negativos(): void
    {
        $this->post('/platos', ['nombre' => 'Gratis', 'tipo' => 'postre', 'precio' => '-1'])
            ->assertSessionHasErrors('precio');

        $this->assertDatabaseCount('platos', 0);
    }

    public function test_la_api_devuelve_json(): void
    {
        Plato::factory()->count(3)->create();

        $this->getJson('/api/carta')->assertOk()->assertJsonCount(3);
    }
}
