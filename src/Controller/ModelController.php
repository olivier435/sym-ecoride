<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Repository\ModelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ModelController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/models/by-brand/{id}', name: 'models_by_brand', methods: ['GET'])]
    public function byBrand(Brand $brand, ModelRepository $modelRepository): JsonResponse
    {
        $models = $modelRepository->findBy(['brand' => $brand]);

        $data = array_map(fn($model) => [
            'id' => $model->getId(),
            'name' => $model->getName(),
        ], $models);

        return $this->json($data);
    }
}