<?php

namespace App\Validator;

use App\Service\Calendar;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

final class WorkingDayValidator extends ConstraintValidator
{
    // Notre contrainte de validation s'appuie sur un objet tiers pour effectuer le traitement.
    // Ce type d'objet est appelé "service", il est automatiquement instancié et injecté dans le constructeur.
    // Ce mécanisme est appelé "Injection de Dépendances" (DI).
    public function __construct(private readonly Calendar $calendar)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var WorkingDay $constraint */
        if (null === $value) {
            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new InvalidArgumentException('Value should be a DateTimeInterface instance.');
        }

        // Utilisation du service.
        $isWorkingDay = $this->calendar->isWorkingDay($value);
        // Logique de validation s'appuyant sur une option.
        if (
            (!$isWorkingDay && WorkingDay::MODE_HOLIDAY === $constraint->mode)
            || ($isWorkingDay && WorkingDay::MODE_WORKING === $constraint->mode)
        ) {
            return;
        }

        $this->context->buildViolation(match ($constraint->mode) {
            WorkingDay::MODE_WORKING => $constraint->workingMessage,
            default => $constraint->holidayMessage,
        })
            ->addViolation()
        ;
    }
}
