<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // 200
    }
    public function testActivationFalse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activation');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // 302
    }
    public function testActivationTrue(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activation',['token' => ' ']);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // 302
    }
}
