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

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post-{id}', requirements: ['id'=>'\d+'], methods: 'GET')]
    public function show(int $id, PostRepository $repository): Response
    {
        $post = $repository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Mais où est donc passée cette publication ???');
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
