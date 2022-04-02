<?php
namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AppTest extends WebTestCase
{
    public function testPageHomepage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    public function testPageRegister()
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
    }

    public function testPageLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200
    }

    public function testPageLoginWithBadCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'admin@example.fr',
            'password' => 'badpassword']);
        $client->submit($form);
        $this->assertSelectorExists('div', 'alert');
    }

    public function testPageLoginSuccessfull(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $crsfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            'email' => 'admin@example.fr',
            'password' => 'password',
            '_csrf_token' => $crsfToken]);
            $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200

    }

    public function testPageForgotPassword()
    {
        $client = static::createClient();
        $client->request('GET', '/forgot_password');
        $this->assertResponseIsSuccessful();
    }

    public function testPageResetPasswordWithTrueToken()
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $user->setToken('aaaaaa');
        $userRepository->add($user);
        $client->loginUser($user, 'test');
        $client->request('GET', '/reset_password',['token' => 'aaaaaa']);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPageResetPasswordWithBadToken()
    {
        $client = static::createClient();
        $client->request('GET', '/reset_password',['token' => 'bbbbbb']);
        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageResetPasswordWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/reset_password');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageActivationWithBadToken()
    {
        $client = static::createClient();
        $client->request('GET', '/activation',['token' => 'BBBBB']);
        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageActivationWithTrueToken()
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $user->setToken('aaaaaa');
        $client->request('GET', '/activation',['token' => 'aaaaaa']);
        $this->assertResponseStatusCodeSame(302);
        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $user->setToken('aaaaaa');
    }
}
