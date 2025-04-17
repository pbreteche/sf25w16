<?php

namespace App\Security\Voter;

use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class PostVoter extends Voter
{
    public const AUTHOR = 'IS_AUTHOR';

    // Détermine si le voteur s'exprime ou s'abstient
    protected function supports(string $attribute, mixed $subject): bool
    {
        return PostVoter::AUTHOR === $attribute && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof Post) {
            throw new \InvalidArgumentException('The post object must be an instance of Post.');
        }
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return $user === $subject->getAuthor();
    }
}
