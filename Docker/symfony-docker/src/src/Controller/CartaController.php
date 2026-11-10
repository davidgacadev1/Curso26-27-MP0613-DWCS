<?php

namespace App\Controller;

use App\Entity\Plato;
use App\Form\PlatoType;
use App\Repository\PlatoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * La carta del Bar Baio: página con formulario y versión JSON.
 * Las dependencias (repositorio, EntityManager, Mailer) llegan como parámetros: autowiring.
 */
class CartaController extends AbstractController
{
    #[Route('/carta', name: 'carta', methods: ['GET', 'POST'])]
    public function index(Request $request, PlatoRepository $platos, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $plato = new Plato();
        $formulario = $this->createForm(PlatoType::class, $plato);
        $formulario->handleRequest($request);

        // isValid() aplica los #[Assert\...] de la entidad y comprueba el token CSRF
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->persist($plato);
            $em->flush();

            // Con Messenger, el correo NO se envía aquí: se guarda en la cola y lo envía el worker
            $mailer->send((new TemplatedEmail())
                ->from('web@barbaio.test')
                ->to('cocina@barbaio.test')
                ->subject('Nuevo plato en la carta: ' . $plato->getNombre())
                ->htmlTemplate('emails/plato_creado.html.twig')
                ->context(['plato' => $plato]));

            $this->addFlash('aviso', 'Añadido: ' . $plato->getNombre());
            return $this->redirectToRoute('carta');          // patrón PRG
        }

        return $this->render('carta/index.html.twig', [
            'carta' => $platos->cartaPorTipo(),
            'formulario' => $formulario,
        ]);
    }

    #[Route('/api/carta', name: 'api_carta', methods: ['GET'])]
    public function api(PlatoRepository $platos): JsonResponse
    {
        $datos = array_map(fn (Plato $p) => [
            'id' => $p->getId(),
            'nombre' => $p->getNombre(),
            'tipo' => $p->getTipo()->value,
            'precio' => (float) $p->getPrecio(),
            'vegano' => $p->isVegano(),
        ], $platos->findBy([], ['tipo' => 'ASC', 'nombre' => 'ASC']));

        return $this->json($datos);
    }
}
