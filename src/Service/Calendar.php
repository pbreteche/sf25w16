<?php

namespace App\Service;

// Cette classe va être utilisée en tant que service.
// Par défaut, le conteneur de service l'instancie en tant que Singleton.
// Pour cette raison, le service doit être "sans-état" (stateless).
class Calendar
{
    public function isWorkingDay(\DateTimeInterface $date): bool
    {
        return $date->format('N') < 6;
    }
}