<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidAddressValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidAddress) {
            throw new \LogicException(sprintf('%s expects %s', __CLASS__, ValidAddress::class));
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value) || !preg_match('/^.+,\s*\d{5}\s+[A-Za-zÀ-ÿ -]+$/u', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', (string) $value)
                ->addViolation();
        }
    }
}