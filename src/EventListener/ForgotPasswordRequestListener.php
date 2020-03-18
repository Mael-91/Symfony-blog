<?php

namespace App\EventListener;

use App\Event\SecurityForgotPasswordRequestEvent;
use Twig\Environment;

class ForgotPasswordRequestListener {

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

    public function onSecurityForgotPasswordRequest(SecurityForgotPasswordRequestEvent $event) {
        $user = $event->getUser();
        $email = $event->getEmail();
        $token = $event->getToken();
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo($email)
            ->setSubject('Reset password')
            ->setBody($this->environment->render('mail/reset_password.html.twig', [
                'user' => $user,
                'email' => $email,
                'token' => $token
            ]), 'text/html');
        $this->mailer->send($message);
    }
}