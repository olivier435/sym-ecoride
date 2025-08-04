<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidAddress extends Constraint
{
    public string $message = 'L\'adresse "{{ string }}" n\'est pas valide. Format attendu : "12 rue Victor Hugo, 75001 Paris".';

    public function __construct(?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        if ($message) {
            $this->message = $message;
        }
    }

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}