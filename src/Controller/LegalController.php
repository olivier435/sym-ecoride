<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    public function __construct(protected CompanyRepository $companyRepository) {}

    #[Route('/mentions-legales', name: 'app_legal')]
    public function index(): Response
    {
        $company = $this->companyRepository->find(1);

        return $this->render('legal/legal.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/politique-de-confidentialite', name: 'app_privacy')]
    public function privacy(): Response
    {
        $company = $this->companyRepository->find(1);

        return $this->render('legal/privacy.html.twig', [
            'company' => $company,
        ]);
    }
}
