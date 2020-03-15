<?php

namespace App\Security\Provider;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubProvider {

    private $githubClient;
    private $githubId;
    /**
     * @var HttpClient
     */
    private $httpClient;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct($githubClient, $githubId, HttpClientInterface $httpClient, SessionInterface $session) {
        $this->githubClient = $githubClient;
        $this->githubId = $githubId;
        $this->httpClient = $httpClient;
        $this->session = $session;
    }

    public function loadUserFromGithub(string $code) {
        $state = $this->session->get('oauth');
        $url = sprintf('https://github.com/login/oauth/access_token?client_id=%s&client_secret=%s&code=%s&state=%s',
            $this->githubId, $this->githubClient, $code, $state);

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $token = $response->toArray()['access_token'];

        $response = $this->httpClient->request('GET', 'https://api.github.com/user', [
            'headers' => [
                'Authorization' => 'token ' . $token
            ]
        ]);

        $data = $response->toArray();
        $this->session->remove('oauth');

        return [
            'username' => $data['login'],
            'email' => $data['email']
        ];
    }
}