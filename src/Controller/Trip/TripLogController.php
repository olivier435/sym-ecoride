<?php

namespace App\Controller\Trip;

use App\Service\MongoLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripLogController extends AbstractController
{
    #[Route('/admin/trip-logs', name: 'app_trip_logs')]
    public function index(Request $request, MongoLogService $mongoLogService): Response
    {
        $startDateStr = $request->query->get('startDate');
        $endDateStr = $request->query->get('endDate');

        $startDate = $startDateStr ? \DateTimeImmutable::createFromFormat('Y-m-d', $startDateStr) : null;
        $endDate   = $endDateStr ? \DateTimeImmutable::createFromFormat('Y-m-d', $endDateStr) : null;

        $driverIdParam = $request->query->get('driverId');
        $driverId = is_numeric($driverIdParam) ? (int)$driverIdParam : null;

        $result = $mongoLogService->getTripReservationsWithStats(50, $startDate, $endDate, $driverId);

        return $this->render('trip/logs.html.twig', [
            'logs' => $result['logs'],
            'totalCredits' => $result['totalCredits'],
            'platformCredits' => $result['platformCredits'],
            'startDate' => $startDateStr,
            'endDate' => $endDateStr,
            'driverId' => $driverId,
        ]);
    }
}