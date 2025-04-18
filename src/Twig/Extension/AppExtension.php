<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

// Cette classe s'appuie sur l'auto-configuration
// Le fait d'étendre AbstractExtension permet de configurer
// ce service auprès du moteur de template.
class AppExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('working day', [AppExtensionRuntime::class, 'isWorkingDay']),
        ];
    }
}
