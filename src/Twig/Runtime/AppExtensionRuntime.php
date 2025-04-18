<?php

namespace App\Twig\Runtime;

use App\Service\Calendar;
use Twig\Extension\RuntimeExtensionInterface;

readonly class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private Calendar $calendar)
    {
    }

    public function isWorkingDay($value): bool
    {
        if (is_null($value)) {
            return false;
        }

        if (is_string($value)) {
            $value = new \DateTimeImmutable($value);
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException();
        }

        return $this->calendar->isWorkingDay($value);
    }
}
