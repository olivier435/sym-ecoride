<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Event\ContactSuccessEvent;
use App\Form\ContactForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, EventDispatcherInterface $dispatcher): Response
    {
        /** @var User */
        $user = $this->getUser();

        $contact = new Contact;
        if ($user) {
            $contact->setFirstname($user->getFirstname())
                    ->setLastname($user->getLastname())
                    ->setEmail($user->getEmail())
                    ->setPhone($user->getPhone());
            $form = $this->createForm(ContactForm::class, $contact);
        } else {
            $form = $this->createForm(ContactForm::class, $contact);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = ucwords($form->get('firstname')->getData());
            $lastname = mb_strtoupper($form->get('lastname')->getData());
            $contact->setFirstname($firstname)
                    ->setLastname($lastname);

            $em->persist($contact);
            $em->flush();

            $contactEvent = new ContactSuccessEvent($contact);
            $dispatcher->dispatch($contactEvent, 'contact.success');

            $this->addFlash('success', 'Votre demande de contact a été envoyé');
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
