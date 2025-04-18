<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LocaleListener
{
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 96)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        // Détermine la meilleure locale en croisant des locales disponibles passées en paramètre
        // et les locales préférentielles transmises par le navigateur
        $locale = $request->getPreferredLanguage(['fr', 'en']);
        $request->setLocale($locale);
    }
}
