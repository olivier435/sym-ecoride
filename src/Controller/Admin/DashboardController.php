<?php

namespace App\Controller\Admin;

use App\Entity\Complaint;
use App\Entity\Testimonial;
use App\Entity\User;
use App\Repository\ComplaintRepository;
use App\Repository\TripPassengerRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private ComplaintRepository $complaintRepository, private TripPassengerRepository $tripPassengerRepository) {}

    public function index(): Response
    {
        return $this->redirectToRoute('admin_dashboard_custom');
        // return parent::index();
    }

    #[Route('/admin/dashboard-custom', name: 'admin_dashboard_custom')]
    public function dashboardCustom(ChartBuilderInterface $chartBuilder): Response
    {
        $user = $this->getUser();

        // Statistiques
        $completedByDay = $this->tripPassengerRepository->countCompletedByDay();
        $creditsByDay = $this->tripPassengerRepository->sumCreditsByDay();
        $totalCredits = $this->tripPassengerRepository->countValidated() * 2;

        // Graphique 1 : Covoiturages effectués par jour
        $chartTrips = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chartTrips->setData([
            'labels' => array_keys($completedByDay),
            'datasets' => [[
                'label' => 'Covoiturages effectués par jour',
                'data' => array_values($completedByDay),
                'borderColor' => 'rgb(75, 192, 192)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            ]],
        ]);
        $chartTrips->setOptions([
            'scales' => ['y' => ['beginAtZero' => true]],
        ]);

        // Graphique 2 : Crédits gagnés par la plateforme par jour
        $chartCredits = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chartCredits->setData([
            'labels' => array_keys($creditsByDay),
            'datasets' => [[
                'label' => 'Crédits gagnés par la plateforme',
                'data' => array_values($creditsByDay),
                'borderColor' => 'rgb(255, 205, 86)',
                'backgroundColor' => 'rgba(255, 205, 86, 0.2)',
            ]],
        ]);
        $chartCredits->setOptions([
            'scales' => ['y' => ['beginAtZero' => true]],
        ]);

        // Réclamations "open" pour les employés
        $openComplaints = [];
        if (in_array('ROLE_EMPLOYE', $user->getRoles(), true)) {
            $openComplaints = $this->complaintRepository->findOpen();
        }

        return $this->render('admin/dashboard.html.twig', [
            'user' => $user,
            'openComplaints' => $openComplaints,
            'totalCredits' => $totalCredits,
            'chartTrips' => $chartTrips,
            'chartCredits' => $chartCredits,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sym Ecoride');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        /** @var User $user */
        return parent::configureUserMenu($user)
            ->setName($user->getFullName())
            ->setAvatarUrl($user->getAvatarUrl());
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToRoute('Dashboard', 'fa fa-home', 'admin_dashboard_custom');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        }
        yield MenuItem::linkToCrud('Témoignages', 'fa-solid fa-face-smile', Testimonial::class);
        yield MenuItem::linkToCrud('Litiges / trajets signalés', 'fa-solid fa-triangle-exclamation', Complaint::class);
        yield MenuItem::linkToUrl('Retour au site', 'fas fa-home', $this->generateUrl('app_home'));
    }
}