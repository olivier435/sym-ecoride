<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class TripCreationStorage
{
    private const SESSION_KEY = 'trip_creation';

    public function __construct(private RequestStack $requestStack) {}

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function saveStepData(array $data): void
    {
        $session = $this->getSession();
        $existingData = $session->get(self::SESSION_KEY, []);
        $session->set(self::SESSION_KEY, array_merge($existingData, $data));
    }

    public function getData(): array
    {
        return $this->getSession()->get(self::SESSION_KEY, []);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::SESSION_KEY);
    }
}