<?php

namespace App\Controller\Author;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/redac')]
class SecurityController extends AbstractController
{
    #[Route('/new-user', methods: ['GET', 'POST'])]
    public function newUser(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager,
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $hasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($encodedPassword);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé.');

            return $this->redirectToRoute('app_author_security_newuser');
        }

        return $this->render('author/security/new_user.html.twig', [
            'form' => $form,
        ]);
    }
}