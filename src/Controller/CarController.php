<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CarController extends AbstractController
{
    #[Route('/car/create', name: 'app_car_create')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $car = new Car();
        $form = $this->createForm(CarForm::class, $car);
        $form->handleRequest($request);
        
        return $this->render('car/index.html.twig', [
            'form' => $form,
        ]);
    }
}
