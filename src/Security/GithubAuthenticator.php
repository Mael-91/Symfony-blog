<?php

namespace App\Security;

use App\Controller\Auth\GithubAuthController;
use App\Entity\User;
use App\Security\Provider\GithubProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

class GithubAuthenticator extends AbstractGuardAuthenticator {

    /**
     * @var GithubProvider
     */
    private $provider;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var GithubAuthController
     */
    private $controller;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(GithubProvider $provider, EntityManagerInterface $manager, GithubAuthController $controller, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator) {
        $this->provider = $provider;
        $this->manager = $manager;
        $this->controller = $controller;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function supports(Request $request)
    {
        return $request->query->get('code');
    }

    public function getCredentials(Request $request)
    {
        return [
            'code' => $request->query->get('code')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $loadUser = $this->provider->loadUserFromGithub($credentials['code']);
        $user = $this->manager->getRepository(User::class)->findOneBy(['email' => $loadUser['email']]);
        if (!$user) {
            $user = $this->controller->generateAccount($loadUser['username'], $loadUser['email']);
            return $user;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse('/');
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('github_connect');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
