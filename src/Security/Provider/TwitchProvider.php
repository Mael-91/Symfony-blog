<?php

namespace App\Security\Provider;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitchProvider {

    private $twitchSecret;
    private $twitchId;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct($twitchSecret, $twitchId, HttpClientInterface $httpClient, SessionInterface $session, UrlGeneratorInterface $urlGenerator) {

        $this->twitchSecret = $twitchSecret;
        $this->twitchId = $twitchId;
        $this->httpClient = $httpClient;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function loadUserFormTwitch(string $code) {
        $url = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $host = sprintf('https://id.twitch.tv/oauth2/token?client_id=%s&client_secret=%s&code=%s&grant_type=authorization_code&redirect_uri=%s',
                $this->twitchId, $this->twitchSecret, $code, $url);

        $response = $this->httpClient->request('POST', $host, [
           'headers' => [
               'Accept' => 'application/json'
           ]
        ]);

        $token = $response->toArray()['access_token'];

        $response = $this->httpClient->request('GET', 'https://api.twitch.tv/helix/users', [
           'headers' => [
               'Authorization' => 'Bearer ' . $token
           ]
        ]);

        $this->session->set('oauth-twitch-token', $token);
        $data = json_decode($response->getContent());
        $username = $data->data[0]->display_name;
        $email = $data->data[0]->email;

        return [
            'username' => $username,
            'email' => $email
        ];
    }
}