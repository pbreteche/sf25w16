<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

final class PlainTextValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var PlainText $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('Value must be a string');
        }

        if (!preg_match('/(<\w+\/?>|<\/\w+>)/i', $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
