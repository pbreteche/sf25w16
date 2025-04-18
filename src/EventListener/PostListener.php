<?php

namespace App\EventListener;

use App\Entity\Post;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class PostListener
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function postPersist(Post $post, PostPersistEventArgs $args): void
    {
        $message = (new Email())
            ->from('john.doe@example.com')
            ->to('jane.doe@example.com')
            ->subject('Nouvelle publication')
            ->text('Une nouvelle publication a été ajoutée.')
            ->html('<h1>Une nouvelle publication a été ajoutée</h1>')
        ;

        $this->mailer->send($message);
    }
}
