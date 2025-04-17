<?php

namespace App\Controller\Author;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/redac')]
class PostController extends AbstractController
{
    #[Route('/new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    ): Response {
        // Raccourcis disponibles depuis les contrôleurs
        $this->denyAccessUnlessGranted('ROLE_AUTHOR');
        if (!$this->isGranted('ROLE_AUTHOR')) {
            throw $this->createAccessDeniedException();
        }
        // Possibilité de récupérer le service Security depuis n'importe quel autre service
        if (!$security->isGranted('ROLE_AUTHOR')) {
            throw new AccessDeniedException();
        }
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        // En réalité, c'est l'objet du modèle (embarqué par le formulaire)
        // qui sera validé
        if ($form->isSubmitted() && $form->isValid()) {
            // Définition des données automatiques
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
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
    // Il est possible de faire référence pour le sujet du vote
    // à tout attribut de la route, inclus les arguments déduits (comme MapEntity).
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("IS_AUTHOR", object)'), 'post')]
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