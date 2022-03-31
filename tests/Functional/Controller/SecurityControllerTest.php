<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200
    }

    public function testForgotPasswordPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/forgot_password');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200
    }

    public function testResetPasswordPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reset_password',['token' => 'aaaaaa']);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 302
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'admin@example.fr',
            'password' => 'badpassword']);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('div', 'alert');
    }

    public function testLoginSuccessfull(): void
    {
        $client = static::createClient();
        $crsfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            'email' => 'admin@example.fr',
            'password' => 'password',
            '_csrf_token' => $crsfToken]);
        $this->assertResponseRedirects('/');
    }
}
