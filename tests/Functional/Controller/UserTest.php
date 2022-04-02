<?php
namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class UserTest extends WebTestCase
{
    public function testPageAdminUserWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');
        
        $client->request('GET', "/admin/users");
        $this->assertResponseIsSuccessful();
    }

    public function testPageAdminUserWhenNotAdmin()
    {
        $client = static::createClient();
        $client->request('GET', "/admin/users");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageUserEditWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');
        $userId = $user->getId();
        
        $client->request('GET', "/profile/user/".$userId."/edit");
        $this->assertResponseIsSuccessful();
    }

    public function testPageUserEditWhenNotAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $userId = $user->getId();
        
        $client->request('GET', "/profile/user/".$userId."/edit");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageUserDeleteWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');
        $userId = $user->getId();
        $client->request('GET', "/admin/user/".$userId."/delete");
        $this->assertResponseIsSuccessful();
    }

    public function testPageUserDeleteWhenNotAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $userId = $user->getId();
        $client->request('GET', "/admin/user/".$userId."/delete");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageUserEditRoleWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');
        $userId = $user->getId();

        $client->request('GET', "/admin/user/".$userId."/edit_role");
        $this->assertResponseIsSuccessful();
    }

    public function testPageUserEditRoleWhenNotAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $userId = $user->getId();

        $client->request('GET', "/admin/user/".$userId."/edit_role");
        $this->assertResponseStatusCodeSame(401);
    }
}
