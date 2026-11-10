<?php

namespace App\Tests\Controller;

use App\Entity\Plato;
use App\Enum\TipoPlato;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * docker compose exec -e XDEBUG_MODE=off php bin/phpunit
 * Usan la base de datos app_test (preparar.sh la crea con sus tablas).
 */
class CartaControllerTest extends WebTestCase
{
    private KernelBrowser $cliente;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->cliente = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        // Cada prueba empieza con la tabla vacía
        $this->em->createQuery('DELETE FROM ' . Plato::class)->execute();
    }

    private function crearPlato(string $nombre, TipoPlato $tipo, string $precio): void
    {
        $this->em->persist((new Plato())->setNombre($nombre)->setTipo($tipo)->setPrecio($precio));
        $this->em->flush();
    }

    public function testLaCartaMuestraLosPlatos(): void
    {
        $this->crearPlato('Pulpo á feira', TipoPlato::Principal, '16.50');

        $this->cliente->request('GET', '/carta');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Principales');
        $this->assertSelectorTextContains('li', 'Pulpo á feira');
        $this->assertSelectorTextContains('li strong', '16,50 €');
    }

    public function testSePuedeAnadirUnPlatoYSeAvisaACocina(): void
    {
        $this->cliente->request('GET', '/carta');
        $this->cliente->submitForm('Guardar', [
            'plato[nombre]' => 'Filloas',
            'plato[tipo]' => 'postre',
            'plato[precio]' => '4.80',
        ]);

        $this->assertResponseRedirects('/carta');
        $this->assertQueuedEmailCount(1);           // el correo va a la cola de Messenger
        $this->cliente->followRedirect();
        $this->assertSelectorTextContains('.aviso', 'Añadido: Filloas');
    }

    public function testNoAdmitePreciosNegativos(): void
    {
        $this->cliente->request('GET', '/carta');
        $this->cliente->submitForm('Guardar', [
            'plato[nombre]' => 'Gratis',
            'plato[tipo]' => 'postre',
            'plato[precio]' => '-1',
        ]);

        $this->assertResponseStatusCodeSame(422);  // formulario con errores
        $this->assertSelectorTextContains('.formulario', 'El precio no puede ser negativo.');
    }

    public function testLaApiDevuelveJson(): void
    {
        $this->crearPlato('Caldo gallego', TipoPlato::Entrante, '7.20');

        $this->cliente->request('GET', '/api/carta');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertSame([['id' => $this->em->getRepository(Plato::class)->findOneBy([])->getId(),
            'nombre' => 'Caldo gallego', 'tipo' => 'entrante', 'precio' => 7.2, 'vegano' => false]],
            json_decode($this->cliente->getResponse()->getContent(), true));
    }
}
