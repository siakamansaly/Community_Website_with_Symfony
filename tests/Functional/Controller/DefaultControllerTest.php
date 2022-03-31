<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200

    }

    public function testHomepageWhenLogged(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        // simulate $user being logged in
        $client->loginUser($user);
        // test e.g. the profile page
        
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('a', 'Logout');
    }
    public function testSingleTrick(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trick/1-Ollie');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200
    }

    public function testAddTrickPage(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        // simulate $user being logged in
        $client->loginUser($user)->request('GET', '/profile/trick/add');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // 302
        $this->assertResponseRedirects('/login');
        $client->followRedirect();

    }


}
