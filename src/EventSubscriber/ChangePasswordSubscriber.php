<?php

namespace App\EventSubscriber;

use App\Event\PasswordTokenValidityEvent;
use App\Event\RequestChangePasswordEvent;
use App\Repository\PasswordTokenRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class ChangePasswordSubscriber implements EventSubscriberInterface {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var PasswordTokenRepository
     */
    private $passwordTokenRepository;

    public function __construct(\Swift_Mailer $mailer, Environment $environment, PasswordTokenRepository $passwordTokenRepository) {
        $this->mailer = $mailer;
        $this->environment = $environment;
        $this->passwordTokenRepository = $passwordTokenRepository;
    }

    public static function getSubscribedEvents() {
        return [
            RequestChangePasswordEvent::class => 'onRequestChangePasswordEvent',
        ];
    }

    // TODO Mettre cette event dans le MailSubscriber
    public function onRequestChangePasswordEvent(RequestChangePasswordEvent $event): void {
        $message = (new \Swift_Message())
            ->setFrom('no-reply@symfony-project-blog.com')
            ->setTo($event->getToken()->getUser()->getEmail())
            ->setSubject('Password change request')
            ->setBody($this->environment->render('mails/security/change_password.html.twig', [
                'user' => $event->getToken()->getUser(),
                'token' => $event->getToken()->getToken(),
            ]), 'text/html');
        $this->mailer->send($message);
    }
}
