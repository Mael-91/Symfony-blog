<?php

namespace App\Component\Mail;

use App\Entity\User;
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
     * Envoi le mail permettant de changer de mot de passe
     *
     * @param User|string $user
     * @param User|string $email
     * @param string $token
     */
    public function sendResetPasswordMail($user, $email, string $token) {
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo($email)
            ->setSubject('Reset password')
            ->setBody($this->renderView('mail/reset_password.html.twig', [
                'user' => $user,
                'email' => $email,
                'token' => $token
            ]), 'text/html');
        $this->mailer->send($message);
    }

    /**
     * Notifie de la modification avec succès du changement du mot de passe
     *
     * @param User|string $user
     * @param User|string $email
     * @param \DateTime $dateTime
     */
    public function sendPasswordChangeConf($user, $email, \DateTime $dateTime) {
        $message = (new \Swift_Message())
            ->setFrom('mael.constantin@laposte.net')
            ->setTo($email)
            ->setSubject('Success change password notification')
            ->setBody($this->renderView('mail/reset_password_success.html.twig', [
                'user' => $user,
                'email' => $email,
                'datetime' => $dateTime
            ]), 'text/html');
        $this->mailer->send($message);
    }
}