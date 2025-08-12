<?php

namespace App\Controller\Admin;

use App\Entity\Complaint;
use App\Enum\ComplaintType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ComplaintCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Complaint::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Réclamations :')
            ->setPageTitle('edit', fn(Complaint $complaint) => 'Réclamation n°' . $complaint->getId())
            ->setPageTitle('detail', fn(Complaint $complaint) => 'Réclamation n°' . $complaint->getId())
            ->setEntityLabelInSingular('une réclamation')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_DETAIL) {
            return [
                IdField::new('id', 'N° du covoiturage')->setFormTypeOption('disabled', true),

                // Type de plainte (figé, badge)
                ChoiceField::new('type', 'Type de plainte')
                    ->setChoices([
                        'Trajet non effectué' => ComplaintType::TRIP_NOT_PERFORMED,
                        'Impossible d\'annuler' => ComplaintType::IMPOSSIBLE_TO_CANCEL,
                        'Problème survenu' => ComplaintType::PROBLEM_ON_TRIP,
                    ])
                    ->renderAsBadges()
                    ->setFormTypeOption('disabled', true)
                    ->formatValue(fn($value, $entity) => $entity->getType()?->label() ?? $entity->getType()?->value ?? ''),

                // Commentaire (figé)
                TextareaField::new('comment', 'Description du litige')
                    ->setFormTypeOption('disabled', true),

                TextareaField::new('contacts', 'Contacts')
                    ->renderAsHtml()
                    ->setFormTypeOption('disabled', true),

                // Cases à cocher éditables
                BooleanField::new('ticketClosed', 'Ticket clôturé (pas de paiement driver)'),
                BooleanField::new('ticketResolved', 'Ticket résolu (paiement driver)'),
            ];
        }

        // Champs pour la liste (index)
        return [
            IdField::new('id', 'N° du covoiturage')
                ->formatValue(function ($value, $entity) {
                    return $entity->getTripPassenger()?->getTrip()?->getId() ?? 'inconnu';
                }),
            AssociationField::new('tripPassenger', 'Passager')
                ->formatValue(function ($value, $entity) {
                    $user = $entity->getTripPassenger()?->getUser();
                    return $user
                        ? $user->getPseudo() . ' (' . $user->getEmail() . ')'
                        : 'N/A';
                }),
            AssociationField::new('tripPassenger', 'Conducteur')
                ->formatValue(function ($value, $entity) {
                    $trip = $entity->getTripPassenger()?->getTrip();
                    $driver = $trip?->getDriver();
                    return $driver
                        ? $driver->getPseudo() . ' (' . $driver->getEmail() . ')'
                        : 'N/A';
                }),
            ChoiceField::new('type', 'Type de plainte')
                ->setChoices([
                    'Trajet non effectué' => ComplaintType::TRIP_NOT_PERFORMED,
                    'Impossible d\'annuler' => ComplaintType::IMPOSSIBLE_TO_CANCEL,
                    'Problème survenu' => ComplaintType::PROBLEM_ON_TRIP,
                ])
                ->renderAsBadges()
                ->formatValue(fn($value, $entity) => $entity->getType()?->label() ?? $entity->getType()?->value ?? ''),
            DateTimeField::new('createdAt', 'Date du signalement'),

            // Départ et arrivée, format lisible
            AssociationField::new('tripPassenger', 'Départ')
                ->formatValue(function ($value, $entity) {
                    $trip = $entity->getTripPassenger()?->getTrip();
                    return $trip
                        ? $trip->getDepartureCity()?->getName()
                        . ' - ' . $trip->getDepartureAddress()
                        . ' le ' . $trip->getDepartureDate()?->format('d/m/Y')
                        : 'N/A';
                })
                ->onlyOnIndex(),

            AssociationField::new('tripPassenger', 'Arrivée')
                ->formatValue(function ($value, $entity) {
                    $trip = $entity->getTripPassenger()?->getTrip();
                    return $trip
                        ? $trip->getArrivalCity()?->getName()
                        . ' - ' . $trip->getArrivalAddress()
                        . ' le ' . $trip->getArrivalDate()?->format('d/m/Y')
                        : 'N/A';
                })
                ->onlyOnIndex(),

            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Résolu' => 'resolved',
                    'Clôturé' => 'closed',
                    'Ouvert' => 'open',
                ])
                ->renderAsBadges([
                    'resolved' => 'success',
                    'closed' => 'success',
                    'open' => 'danger',
                ]),
        ];
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        // $entityInstance est l'instance de Complaint
        $complaint = $entityInstance;
        $tripPassenger = $complaint->getTripPassenger();

        // Ticket clôturé (aucun paiement au driver)
        if ($complaint->isTicketClosed()) {
            $tripPassenger->setValidationStatus('validated');
            $tripPassenger->setValidationAt(new \DateTimeImmutable());
        }

        // Ticket résolu (paiement driver)
        if ($complaint->isTicketResolved()) {
            $tripPassenger->setValidationStatus('validated');
            $tripPassenger->setValidationAt(new \DateTimeImmutable());

            $trip = $tripPassenger->getTrip();
            $driver = $trip?->getDriver();

            if ($driver && $trip) {
                $driver->setCredit($driver->getCredit() + $trip->getPricePerPerson());
                $em->persist($driver);
            }
        }

        $em->persist($tripPassenger);
        $em->flush();
    }
}