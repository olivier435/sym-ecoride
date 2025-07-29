<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\PasswordResetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(protected RequestStack $requestStack) {}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_pw')]
    public function forgotPw(Request $request, UserRepository $userRepository, PasswordResetService $passwordResetService): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy([
                'email' => $form->get('email')->getData()
            ]);

            if ($user) {
                $passwordResetService->processPasswordReset($user);
                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'formView' => $form,
        ]);
    }

    #[Route('/forgot-password/{token}', name: 'app_reset_pw')]
    public function resetPw($token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher, PasswordResetService $passwordResetService): Response
    {
        // On vérifie si on a ce token dans la bdd
        $user = $passwordResetService->getUserByResetToken($token, $userRepository);

        // Si le token est invalide on redirige vers le login
        if (!$user) {
            $this->addFlash('danger', 'Jeton invalide');
            return $this->redirectToRoute('app_login');
        }

        // On vérifie si le createdTokenAt = now - 1h
        if ($passwordResetService->isTokenExpired($user)) {
            $this->addFlash('warning', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('app_forgot_pw');
        }

        // On modifie le mot de passe
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordResetService->updatePassword($user, $form->get('plainPassword')->getData(), $hasher);
            $this->addFlash('success', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'passForm' => $form
        ]);
    }

    #[Route(path: '/login/success', name: 'app_login_success')]
    public function loginSuccess(): Response
    {
        $session = $this->requestStack->getSession();

        // Récupérer l'URL de redirection stockée
        $targetPath = $session->get('_security.main.target_path', $this->generateUrl('app_home'));

        // Supprimer la valeur de la session pour éviter une redirection non voulue plus tard
        $session->remove('_security.main.target_path');

        return $this->redirect($targetPath);
    }
}
