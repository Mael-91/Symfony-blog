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

    /**
     * Envoi un mail lorsqu'un personne se connecte au compte
     * @param $time
     * @param $ip
     */
    public function sendLoginMail($time, $ip) {
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo('mael.constantin@gmail.com')
            ->setSubject('Test envoi email')
            ->setBody($this->renderView('mail/login_mail.html.twig', [
                'ip' => $ip,
                'time' => $time
            ]), 'text/html');
        $this->mailer->send($message);
    }

    /**
     * Envoi le mail de confirmation lors de l'inscription
     * @param $user
     * @param $email
     * @param $id
     * @param $token
     */
    public function sendRegisterMail($user, $email, $id, $token) {
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@gmail.com')
            ->setTo($email)
            ->setSubject('Confirm account')
            ->setBody($this->renderView('mail/register_mail.html.twig', [
                'email' => $email,
                'user' => $user,
                'id' => $id,
                'token' => $token
            ]), 'text/html');
        $this->mailer->send($message);
    }

    /**
     * Envoi le mail permettant de changer de mot de passe
     * @param $user
     * @param $token
     */
    public function sendResetPasswordMail($user, $token) {
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo('mael.constantin@gmail.com')
            ->setSubject('Reset password')
            ->setBody($this->renderView('mail/reset_password.html.twig', [
                'user' => $user,
                'token' => $token
            ]), 'text/html');
        $this->mailer->send($message);
    }
}