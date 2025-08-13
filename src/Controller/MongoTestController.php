<?php

namespace App\Controller;

use App\Service\MongoLogService;
use MongoDB\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MongoTestController extends AbstractController
{
    #[Route('/mongo-test', name: 'app_mongo_test')]
    public function index(MongoLogService $mongoLogService): Response
    {
        // Écriture d'un log de test
        // $mongoLogService->log(
        //     'test',
        //     ['message' => 'Ceci est un test depuis MongoTestController'],
        //     $this->getUser()
        // );

        // Lecture des derniers logs
        $logs = $mongoLogService->recent(10);

        return $this->render('mongo_test/index.html.twig', [
            'logs' => $logs,
        ]);
    }

    #[Route('/mongo-test/add', name: 'app_mongo_test_add')]
    public function add(MongoLogService $mongoLogService): RedirectResponse
    {
        $mongoLogService->log(
            'test',
            ['message' => 'Ajout manuel depuis le bouton'],
            $this->getUser()
        );

        $this->addFlash('success', 'Log ajouté avec succès.');
        return $this->redirectToRoute('app_mongo_test');
    }

    #[Route('/mongo-test/clear', name: 'app_mongo_test_clear')]
    public function clear(MongoLogService $mongoLogService): RedirectResponse
    {
        // On supprime tous les documents de la collection
        $mongoLogService->clearAll();

        $this->addFlash('success', 'Tous les logs ont été supprimés.');
        return $this->redirectToRoute('app_mongo_test');
    }
}
