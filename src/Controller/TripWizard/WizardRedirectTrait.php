<?php

namespace App\Controller\TripWizard;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait WizardRedirectTrait
{
    private function redirectAfterStep(string $normalNextRoute, Request $request, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $route = $request->query->getBoolean('fromRecap') ? 'app_trip_wizard_recap' : $normalNextRoute;

        return new RedirectResponse($urlGenerator->generate($route));
    }
}