<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait LoginTrait {

    public function login(KernelBrowser $client, User $user, string $firewall = 'main') {
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $session->get('_security_main', $token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

}