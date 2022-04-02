<?php
namespace App\Tests\Functional\Controller;

use App\Repository\TypeTrickRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class TypeTest extends WebTestCase
{
    

    public function testPageAdminTypeWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');
        
        $client->request('GET', "/admin/type");
        $this->assertResponseIsSuccessful();
    }

    public function testPageAdminTypeWhenNotAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        
        $client->request('GET', "/admin/type");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageTypeEditWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);

        $typeRepository = $client->getContainer()->get(TypeTrickRepository::class);
        $type = $typeRepository->findOneBy([]);

        $client->loginUser($user, 'test');
        $typeId = $type->getId();

        $client->request('GET', "/admin/type/".$typeId."/edit");
        $this->assertResponseIsSuccessful();
    }

    public function testPageTypeEditWhenNotAdmin()
    {
        $client = static::createClient();

        $typeRepository = $client->getContainer()->get(TypeTrickRepository::class);
        $type = $typeRepository->findOneBy([]);

        $typeId = $type->getId();

        $client->request('GET', "/admin/type/".$typeId."/edit");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageTypeDeleteWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);

        $typeRepository = $client->getContainer()->get(TypeTrickRepository::class);
        $type = $typeRepository->findOneBy([]);

        $client->loginUser($user, 'test');
        $typeId = $type->getId();

        $client->request('GET', "/admin/type/".$typeId."/delete");
        $this->assertResponseIsSuccessful();
    }

    public function testPageTypeDeleteWhenNotAdmin()
    {
        $client = static::createClient();

        $typeRepository = $client->getContainer()->get(TypeTrickRepository::class);
        $type = $typeRepository->findOneBy([]);
        $typeId = $type->getId();

        $client->request('GET', "/admin/type/".$typeId."/delete");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageTypeAddWhenAdmin()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);
        $client->loginUser($user, 'test');

        $client->request('GET', "/admin/type/add");
        $this->assertResponseIsSuccessful();

    }

    public function testPageTypeAddWhenNotAdmin()
    {
        $client = static::createClient();
        $client->request('GET', "/admin/type/add");
        $this->assertResponseStatusCodeSame(401);

    }


   

}