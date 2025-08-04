<?php

namespace App\Service;

class AddressFormatter
{
    public function format(string $input): string
    {
        $input = trim($input);

        // Ex: "12 rue Victor Hugo, 75001 Paris"
        if (!preg_match('/^(.*),\s*(\d{5})\s+(.*)$/', $input, $matches)) {
            return $input;
        }

        $street = ucwords(mb_strtolower($matches[1]));
        $postalCode = $matches[2];
        $city = mb_strtoupper($matches[3]);

        return sprintf('%s, %s %s', $street, $postalCode, $city);
    }

    public function isValid(string $input): bool
    {
        return (bool) preg_match('/^.+,\s*\d{5}\s+[A-Za-zÀ-ÿ -]+$/u', trim($input));
    }
}