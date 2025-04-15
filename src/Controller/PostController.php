<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/', methods: 'GET')]
    public function index(PostRepository $postRepository): Response
    {
        // $posts = $postRepository->findAll();
        $posts = $postRepository->findBy([], orderBy: ['createdAt' => 'DESC'], limit: 10);
    }
}
