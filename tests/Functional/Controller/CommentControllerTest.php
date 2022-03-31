<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class CommentControllerTest extends WebTestCase
{


    public function testCommentPage(): void
    {   
        $client = static::createClient();
        $client->request('GET', '/admin/comments');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // 302
    }

   

}
