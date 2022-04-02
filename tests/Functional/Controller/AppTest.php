<?php
namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    public function testPageForgotPassword()
    {
        $client = static::createClient();
        $client->request('GET', '/forgot_password');
        $this->assertResponseIsSuccessful();
    }

    public function testPageResetPassword()
    {
        $client = static::createClient();
        $client->request('GET', '/reset_password/?token=aaaaaa');
        $this->assertResponseStatusCodeSame(301);
    }

    public function testPageActivationWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/activation');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageActivationWithToken()
    {
        $client = static::createClient();
        $client->request('GET', '/activation/?token=aaaaaa');
        $this->assertResponseStatusCodeSame(301);
    }
}
