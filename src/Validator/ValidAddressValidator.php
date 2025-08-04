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

        // Format attendu : [quelque chose], [code postal] [ville]
        // Exemple valide : "Chemin des Vignes, 34000 Montpellier"
        if (!is_string($value) || !preg_match('/^.+,\s*\d{5}\s+[A-Za-zÀ-ÿ\'"éèàç\s-]+$/u', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', (string) $value)
                ->addViolation();
        }
    }
}