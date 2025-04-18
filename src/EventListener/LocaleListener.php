<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LocaleListener
{
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 96)]
    public function handlePreferredLocale(RequestEvent $event): void
    {
        $request = $event->getRequest();
        // Détermine la meilleure locale en croisant des locales disponibles passées en paramètre
        // et les locales préférentielles transmises par le navigateur
        $locale = $request->getPreferredLanguage(['fr', 'en']);
        $request->setLocale($locale);
    }

    #[AsEventListener(event: KernelEvents::REQUEST, priority: 64)]
    public function handleSessionLocale(RequestEvent $event): void
    {
        $request = $event->getRequest();
        // Lecture de la requête HTTP afin de définir une éventuelle nouvelle valeur de la locale.
        $queryLocale = $request->query->get('locale');
        if ($queryLocale) {
            $request->getSession()->set('_locale', $queryLocale);
        }

        // Chargement de la locale à partie de la session.
        $locale = $request->getSession()->get('_locale');
        if ($locale) {
            $request->setLocale($locale);
        }
    }
}
