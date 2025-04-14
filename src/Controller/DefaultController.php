<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/{name}',
        requirements: ['name' => '[[:alpha:]]+'],
        defaults: ['name' => 'world'],
        methods: 'GET',
    )]
    public function index(string $name): Response
    {
        return new Response("<html lang=\"en\"><body><h1>Hello $name!</h1></body></html>");
    }
}
