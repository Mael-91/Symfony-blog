<?php


namespace App\Security\Provider;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleProvider {

    private $googleClient;
    private $googleId;
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

    public function __construct($googleClient, $googleId, HttpClientInterface $httpClient, SessionInterface $session, UrlGeneratorInterface $urlGenerator) {

        $this->googleClient = $googleClient;
        $this->googleId = $googleId;
        $this->httpClient = $httpClient;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function loadUserFromGoogle(string $code) {
        $state = $this->session->get('oauth-google-state');
        $url = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $host = sprintf('https://oauth2.googleapis.com/token?client_id=%s&client_secret=%s&code=%s&grant_type=authorization_code&redirect_uri=%s&state=%s',
                $this->googleId, $this->googleClient, $code, $url, $state);

        $response = $this->httpClient->request('POST', $host, [
           'headers' => [
               'Accept' => 'application/x-www-form-urlencoded'
           ],
        ]);

        $token = $response->toArray()['access_token'];

        $response = $this->httpClient->request('GET', 'https://openidconnect.googleapis.com/v1/userinfo', [
           'headers' => [
               'Authorization' => 'Bearer ' . $token
           ],
        ]);

        $data = $response->toArray();
        $this->session->remove('oauth-google-state');
        $this->session->set('oauth-google-token', $token);

        return [
            'username' => $data['name'],
            'first_name' => $data['given_name'],
            'last_name' => $data['family_name'],
            'email' => $data['email']
        ];
    }
}