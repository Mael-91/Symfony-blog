<?php

namespace App\Component\Mail;

use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerComponent extends AbstractController {

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function __construct(Swift_Mailer $mailer) {
        $this->mailer = $mailer;
    }

    public function sendLoginMail($time, $ip) {
        $mail = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo('mael.constantin@gmail.com')
            ->setSubject('Test envoi email')
            ->setBody($this->renderView('mail/login_mail.html.twig', [
                'ip' => $ip,
                'time' => $time
            ]), 'text/html');
        $this->mailer->send($mail);
    }

    public function sendRegisterMail($user, $email) {
        $mail = (new \Swift_Message())
            ->setFrom('mael.constantin@gmail.com')
            ->setTo($email)
            ->setSubject('Bienvenue sur le site !')
            ->setBody($this->renderView('mail/register_mail.html.twig', [
                'email' => $email,
                'user' => $user
            ]), 'text/html');
        $this->mailer->send($mail);
    }

    /*public function sendResetPasswordMail() {

    }*/
}