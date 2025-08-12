<?php

namespace App\Command;

use App\Repository\TripPassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:trip-passengers:auto-validate',
    description: 'Valide automatiquement les passagers si aucune réclamation après 1h du trajet terminé',
)]
class AutoValidateTripPassengersCommand extends Command
{
    public function __construct(
        private readonly TripPassengerRepository $tripPassengerRepo,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTimeImmutable('now', $tz);

        $passengers = $this->tripPassengerRepo->findPendingToAutoValidate($now);

        foreach ($passengers as $tp) {
            $tp->setValidationStatus('validated');
            $tp->setValidationAt($now);

            // Mise à jour du crédit du conducteur
            $trip = $tp->getTrip();
            $driver = $trip->getDriver();
            $driver->setCredit($driver->getCredit() + $trip->getPricePerPerson() - 2);

            $output->writeln(sprintf(
                'TripPassenger #%d validé automatiquement pour Trip #%d (Utilisateur : %s)',
                $tp->getId(),
                $trip?->getId(),
                $tp->getUser()?->getFullName()
            ));
            $this->em->persist($tp);
            $this->em->persist($driver);
        }

        $this->em->flush();

        $output->writeln(sprintf('%d passagers validés automatiquement.', count($passengers)));
        return Command::SUCCESS;
    }
}