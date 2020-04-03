<?php

namespace App\EventSubscriber;

use App\Event\LoginAfterRegistrationEvent;
use App\Event\PasswordTokenValidityEvent;
use App\Event\RequestChangePasswordEvent;
use App\Event\SecurityForgotPasswordRequestEvent;
use App\Event\SecurityPasswordInformationEvent;
use App\Event\SecurityRegistrationEvent;
use App\Repository\PasswordTokenRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Twig\Environment;

class SecuritySubscriber implements EventSubscriberInterface {
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var PasswordTokenRepository
     */
    private $passwordTokenRepository;
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        EntityManagerInterface $manager,
        Environment $environment,
        PasswordTokenRepository $passwordTokenRepository,
        MailerService $mailerService,
        ContainerInterface $container) {
        $this->environment = $environment;
        $this->passwordTokenRepository = $passwordTokenRepository;
        $this->mailerService = $mailerService;
        $this->manager = $manager;
        $this->container = $container;
    }

    public static function getSubscribedEvents() {
        return [
            'security.interactive_login' => 'onSecurityInteractiveLogin',
            RequestChangePasswordEvent::class => 'onRequestChangePasswordEvent',
            SecurityForgotPasswordRequestEvent::class => 'onSecurityForgotPasswordRequestEvent',
            SecurityPasswordInformationEvent::class => 'onSecurityPasswordInformationEvent',
            SecurityRegistrationEvent::class => 'onSecurityRegistrationEvent',
            LoginAfterRegistrationEvent::class => 'onLoginAfterRegistrationEvent'
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLogin(new \DateTime());
        $this->manager->flush();
    }

    public function onRequestChangePasswordEvent(RequestChangePasswordEvent $event): void {
        $this->mailerService->sendMail(null,
            $event->getToken()->getUser()->getEmail(),
            \Swift_Message::PRIORITY_HIGH,
            'Password change request',
            'mails/security/change_password.html.twig',[
                'user' => $event->getToken()->getUser(),
                'token' => $event->getToken()->getToken()
            ]);
    }

    public function onSecurityForgotPasswordRequestEvent(SecurityForgotPasswordRequestEvent $event) {
        $this->mailerService->sendMail(null,
            $event->getUser()->getEmail(),
            \Swift_Message::PRIORITY_HIGH,
            'Reset Password',
            'mails/security/reset_password.html.twig', [
                'user' => $event->getUser()->getUsername(),
                'email' => $event->getUser()->getEmail(),
                'token' => $event->getUser()->getPasswordToken()
            ]);
    }

    public function onSecurityPasswordInformationEvent(SecurityPasswordInformationEvent $event) {
        $this->mailerService->sendMail(null,
            $event->getUser()->getEmail(),
            \Swift_Message::PRIORITY_HIGH,
            'Success change password notification',
            'mails/security/reset_password_success.html.twig', [
                'user' => $event->getUser()->getUsername(),
                'email' => $event->getUser()->getEmail(),
                'datetime' => new \DateTime()
            ]);
    }

    public function onSecurityRegistrationEvent(SecurityRegistrationEvent $event) {
        $this->mailerService->sendMail(null,
            $event->getUser()->getEmail(),
            \Swift_Message::PRIORITY_NORMAL,
            'Confirm account',
            'mails/security/register_mail.html.twig', [
                'email' => $event->getUser()->getEmail(),
                'user' => $event->getUser()->getUsername(),
                'id' => $event->getUser()->getId(),
                'token' => $event->getToken()->getToken()
            ]);
    }

    public function onLoginAfterRegistrationEvent(LoginAfterRegistrationEvent $event): void {
        $login = new PostAuthenticationGuardToken($event->getUser(), 'security.user.provider.concrete.from_database', $event->getUser()->getRoles());
        $this->container->get('security.token_storage')->setToken($login);
        $this->container->get('session')->set('_security_main', serialize($login));
    }
}
