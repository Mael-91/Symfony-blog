<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase {

    use FixturesTrait;
    use LoginTrait;

    public function testDisplayRegistrationPage() {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * TODO Désactiver ce teste lors du commit
     * Désactiver le captcha lors des testes
     */
    /*public function testUserRegistration() {

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('S\'inscire')->form([
            'registration[username]' => 'NewUser',
            'registration[first_name]' => 'John',
            'registration[last_name]' => 'Doe',
            'registration[email]' => 'john.doe@gmail.com',
            'registration[password][first]' => '1234',
            'registration[password][second]' => '1234',
            'registration[birthday]' => '2020-04-12',
            'registration[sexe]' => '1'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }*/

    public function testDisplayLogin() {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginWithBadCredentials() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'MyUser',
            '_password' => 'FakePassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfullLoginWithUsername() {
        $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/User.yaml'
        ]);
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'Mael',
            '_password' => '1234'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testSuccessfullLoginWithEmail() {
        $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/User.yaml'
        ]);
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'mael@gmail.com',
            '_password' => '1234'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }
}
