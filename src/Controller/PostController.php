<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post-{id}', requirements: ['id'=>'\d+'], methods: 'GET')]
    public function show(Post $post): Response {
        // Résolution automatique de l'argument, car :
        // * de type Entité Doctrine
        // * le paramètre à convertir est "id"
        // Dans les autres cas, utiliser #[MapEntity(...)]
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
