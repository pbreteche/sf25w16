<?php

namespace App\Controller\Author;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/redac', methods: 'GET')]
class PostController extends AbstractController
{
    #[Route('/new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
    ): Response {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
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

        return $this->render('author/post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Post $post,
        Request $request,
        EntityManagerInterface $manager,
    ): Response {
        $form = $this->createForm(PostType::class, $post, [
            'textarea_rows' => 10,
            'controller_action' => 'edit',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', 'Votre publication a bien été mise-à-jour.');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('author/post/edit.html.twig', [
            'form' => $form,
        ]);
    }
}