<?php

namespace App\EventListener;

use App\Event\SecurityRegistrationEvent;
use Twig\Environment;

class RegistrationListener {
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(\Swift_Mailer $mailer, Environment $environment) {
        $this->mailer = $mailer;
        $this->environment = $environment;
    }

    public function onSecurityRegistration(SecurityRegistrationEvent $event) {
        $id = $event->getId();
        $user = $event->getUser();
        $email = $event->getEmail();
        $token = $event->getConfirmationToken();
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@gmail.com')
            ->setTo($email)
            ->setSubject('Confirm account')
            ->setBody($this->environment->render('mails/security/register_mail.html.twig', [
                'email' => $email,
                'user' => $user,
                'id' => $id,
                'token' => $token
            ]), 'text/html');
        $this->mailer->send($message);
    }
}