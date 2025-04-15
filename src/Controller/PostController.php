<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
    ): Response {
        $post = new Post();
        $form = $this->createFormBuilder($post)
            ->add('title') // Le form builder configure automatiquement les champs
            ->add('body')  // en fonction de ce qu'il connait de l'objet métier
            ->getForm()
        ;
        $form->handleRequest($request);
        // En réalité, c'est l'objet du modèle (embarqué par le formulaire)
        // qui sera validé
        if ($form->isSubmitted() && $form->isValid()) {
            // Définition des données automatiques
            $post->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($post); // signale le nouvel objet comme devant être enregistré
            $manager->flush(); // Effectue l'ensemble des opérations d'écriture en attente
            // enregistre un message en session qui sera effacé dès le premier accès
            $this->addFlash('success', 'Votre publication a bien été enregistrée.');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Post $post,
        Request $request,
        EntityManagerInterface $manager,
    ): Response {
        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('body')
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', 'Votre publication a bien été mise-à-jour.');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
