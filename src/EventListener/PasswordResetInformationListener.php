<?php


namespace App\EventListener;

use App\Event\SecurityPasswordInformationEvent;
use Twig\Environment;

class PasswordResetInformationListener {

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

    public function onSecurityResetPasswordInformation(SecurityPasswordInformationEvent $event) {
        $user = $event->getUser();
        $email = $event->getEmail();
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo($email)
            ->setSubject('Success change password notification')
            ->setBody($this->environment->render('mails/security/reset_password_success.html.twig', [
                'user' => $user,
                'email' => $email,
                'datetime' => new \DateTime()
            ]), 'text/html');
        $this->mailer->send($message);
    }

}