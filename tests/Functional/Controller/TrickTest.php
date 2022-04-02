<?php
namespace App\Tests\Functional\Controller;

use App\Repository\TrickRepository;
use App\Repository\TypeTrickRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class TrickTest extends WebTestCase
{
    public function testPageTrickShow()
    {
        $client = static::createClient();
        $trickRepository = $client->getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);
        $slug = '/trick/'.$trick->getSlug();
        $client->request('GET', $slug);
        $this->assertResponseIsSuccessful();
    }

    public function testPageTrickAddWhenLogged()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy([]);
        $client->loginUser($user, 'test');
        
        $crawler = $client->request('GET', "/profile/trick/add");
        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Submit');
        
        $form = $buttonCrawlerNode->form([
            'trick_form[title]'    => 'TestAddTrick',
            'trick_form[content]'    => 'TestAddTrick',           
        ]);
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/', Response::HTTP_FOUND); // 302

        $trickRepository = $client->getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy(['title' => 'TestAddTrick']);
        $trickRepository->remove($trick);

    }

    public function testPageTrickAddWhenNotLogged()
    {
        $client = static::createClient();
        $client->request('GET', "/profile/trick/add");
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPageTrickEditWhenLogged()
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@example.fr']);

        $trickRepository = $client->getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $client->loginUser($user, 'test');
        $trickId = $trick->getId();

        $client->request('GET', "/profile/trick/".$trickId."/edit");
        $this->assertResponseIsSuccessful();

    }

    public function testPageTrickEditWhenNotLogged()
    {
        $client = static::createClient();

        $trickRepository = $client->getContainer()->get(TrickRepository::class);
        $trick = $trickRepository->findOneBy([]);

        $trickId = $trick->getId();

        $client->request('GET', "/profile/trick/".$trickId."/edit");
        $this->assertResponseStatusCodeSame(401);
    }
}
