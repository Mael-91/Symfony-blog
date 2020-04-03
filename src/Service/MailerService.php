<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

class MailerService {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(\Swift_Mailer $mailer, Environment $environment, ContainerInterface $container) {
        $this->mailer = $mailer;
        $this->environment = $environment;
        $this->container = $container;
    }

    /**
     * @param string|null $from
     * @param string $to
     * @param $priority
     * @param string $subject
     * @param string $template
     * @param array $params
     * @param string $contentType
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendMail(?string $from, string $to, $priority , string $subject, string $template, array $params = [], string $contentType = 'text/html'): void {
        if (is_null($from)) {
            $from = $this->container->getParameter('default_mail');
        }
        $message = (new \Swift_Message())
            ->setFrom($from)
            ->setTo($to)
            ->setPriority($priority)
            ->setSubject($subject)
            ->setBody($this->environment->render($template, $params), $contentType);
        $this->mailer->send($message);
    }
}