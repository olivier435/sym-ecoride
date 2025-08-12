<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class MustChangePasswordRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private RouterInterface $router) {}

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }

    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        // Ne traite que les routes nécessitant un utilisateur connecté (hors login/reset/logout…)
        $currentRoute = $request->attributes->get('_route');
        if (in_array($currentRoute, [
            'app_login',
            'app_forgot_pw',
            'app_reset_pw',
            '2fa_login',
            '2fa_login_check',
            'app_logout',
            'app_force_pw_change',
            'app_home'
        ])) {
            return;
        }

        /** @var User|null $user */
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        if (method_exists($user, 'isMustChangePassword') && $user->isMustChangePassword()) {
            // Redirige vers la route de changement de mot de passe
            $event->setResponse(new RedirectResponse($this->router->generate('app_force_pw_change')));
        }
    }
}