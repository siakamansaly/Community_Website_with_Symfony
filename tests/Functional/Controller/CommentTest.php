<?php
namespace App\Tests\Functional\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class CommentTest extends WebTestCase
{
    public function testPageAdminCommentWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');
        
        $client->request('GET', "/admin/comments");
        $this->assertResponseIsSuccessful();
    }

    public function testPageAdminCommentWhenNotAdmin()
    {
        $client = static::createClient();
        $client->request('GET', "/admin/comments");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageCommentDeleteWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);

        $commentRepository = $client->getContainer()->get(CommentRepository::class);
        $comment = $commentRepository->findOneBy([]);

        $client->loginUser($user, 'test');
        $commentId = $comment->getId();

        $client->request('GET', "/admin/comment/".$commentId."/delete");
        $this->assertResponseIsSuccessful();
    }

    public function testPageCommentDeleteWhenNotAdmin()
    {
        $client = static::createClient();

        $commentRepository = $client->getContainer()->get(CommentRepository::class);
        $comment = $commentRepository->findOneBy([]);
        $commentId = $comment->getId();

        $client->request('GET', "/admin/comment/".$commentId."/delete");
        $this->assertResponseStatusCodeSame(401);
    }
}
