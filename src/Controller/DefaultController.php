<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/{name}',
        requirements: ['name' => '[[:alpha:]]+'],
        defaults: ['name' => 'world'],
        host: 'localhost',
        methods: 'GET',
        schemes: 'https',
        priority: 1245, // définit l'ordre dans lequel les routes sont testées
    )]
    public function index(string $name): Response
    {
        return new Response("<html lang=\"en\"><body><h1>Hello $name!</h1></body></html>");
    }

    #[Route('/index2', methods: ['GET'])]
    public function index2(Request $request): Response
    {
        // Accès à la query string, possibilité d'avoir une valeur par défaut
        // construit au moment de l'amorce à partir de $_GET
        // On évite d'utiliser les super-globales
        $status = $request->query->get('status');
        // Idem avec $_POST
        $request->request->get('lang', 'fr');
        // $_SESSION (fait appel à session_start si besoin)
        $request->getSession()->get('lang', 'fr');
        $request->headers->get('accept-language', 'text/html');

        // Comme l'ensemble de la lecture de l'entrée passe via l'objet Request
        // On effectue toute opération impactant la sortie via l'objet Response
        // On évite "echo", "print", "var_dump", "http_response_code", "headers", "die", "exit", etc.
        $response = new Response();
        $response
            ->setContent('Hello !')
            ->setStatusCode(Response::HTTP_OK)
        ;
        $response->headers->set('content-type', 'text/plain');
        $response->headers->addCacheControlDirective('must-revalidate', true);

        // Pour quitter l'exécution, passer par une exception :
        switch ($status) {
            case '404':
                throw $this->createNotFoundException('produit une 404');
            case '403':
                throw $this->createAccessDeniedException('produit une 403');
            case '405':
                throw new MethodNotAllowedHttpException(['POST', 'PUT'], 'produit une 405');
            case '500':
                throw new \Exception();
        }

        return $response;
    }
}
