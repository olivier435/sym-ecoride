<?php

namespace App\Command;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:trips:auto-start',
    description: 'Passe automatiquement les trajets à "en cours" si l\'heure de départ est atteinte',
)]
final class StartUpcomingTripsCommand extends Command
{
    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);

        // On va chercher les trips à venir dont la date/heure de départ est passée (dans une fenêtre)
        $trips = $this->tripRepository->findTripsToAutoStart($now);

        foreach ($trips as $trip) {
            $trip->setStatus(Trip::STATUS_ONGOING);
            // Tu peux ajouter ici l'envoi d'un mail ou d'une notif si besoin
            $output->writeln(sprintf('Trajet %d démarré automatiquement', $trip->getId()));
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}