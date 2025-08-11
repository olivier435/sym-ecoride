<?php

namespace App\Command;

use App\Repository\TripRepository;
use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:trips:auto-complete',
    description: 'Passe automatiquement les trajets "en cours" à "effectué" si l\'heure d\'arrivée est atteinte',
)]
class CompleteOngoingTripsCommand extends Command
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

        $trips = $this->tripRepository->findTripsToAutoComplete($now);

        foreach ($trips as $trip) {
            $trip->setStatus(Trip::STATUS_COMPLETED);
            // (À faire : notifier les passagers)
            $output->writeln(sprintf('Trajet %d passé à "effectué" automatiquement', $trip->getId()));
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}