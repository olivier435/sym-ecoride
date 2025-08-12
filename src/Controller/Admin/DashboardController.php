<?php

namespace App\Controller\Admin;

use App\Entity\Complaint;
use App\Entity\Testimonial;
use App\Entity\User;
use App\Repository\ComplaintRepository;
use App\Repository\TripPassengerRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private ComplaintRepository $complaintRepository, private TripPassengerRepository $tripPassengerRepository) {}

    public function index(): Response
    {
        $user = $this->getUser();

        $totalCredits = null;
        $openComplaints = [];

        if (in_array('ROLE_EMPLOYE', $user->getRoles(), true)) {
            // On charge les litiges "open" uniquement
            $openComplaints = $this->complaintRepository->findOpen();
        }

        $totalCredits = $this->tripPassengerRepository->countValidated() * 2;

        // Pour un dashboard graphique, passe ici toutes les infos nécessaires au template
        return $this->render('admin/dashboard.html.twig', [
            'user' => $user,
            'openComplaints' => $openComplaints,
            'totalCredits' => $totalCredits,
        ]);
        // return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sym Ecoride');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        }
        yield MenuItem::linkToCrud('Témoignages', 'fa-solid fa-face-smile', Testimonial::class);
        yield MenuItem::linkToCrud('Litiges / trajets signalés', 'fa-solid fa-triangle-exclamation', Complaint::class);
        yield MenuItem::linkToUrl('Retour au site', 'fas fa-home', $this->generateUrl('app_home'));
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}