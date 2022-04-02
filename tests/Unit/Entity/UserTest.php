<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();
        $date = new \DateTime();
        $user->setEmail('true@test.com')
       ->setFirstname('firstname')
       ->setLastname('lastname')
       ->setPassword('password')
       ->setRoles(['ROLE_USER'])
       ->setStatus(1)
       ->setToken('test')
       ->setRegistrationDate($date)
       ->setLastConnectionDate($date)
       ->setTokenDate($date)
       ->setPicture('picture.jpg');

        $this->assertTrue($user->getEmail() === 'true@test.com');
        $this->assertTrue($user->getFirstname() === 'firstname');
        $this->assertTrue($user->getLastname() === 'lastname');
        $this->assertTrue($user->getPassword() === 'password');
        $this->assertTrue($user->getRoles() === ['ROLE_USER']);
        $this->assertTrue($user->getStatus() === 1);
        $this->assertTrue($user->getToken() === 'test');
        $this->assertTrue($user->getRegistrationDate() === $date);
        $this->assertTrue($user->getLastConnectionDate() === $date);
        $this->assertTrue($user->getTokenDate() === $date);
        $this->assertTrue($user->getPicture() === 'picture.jpg');
    }

    public function testIsFalse(): void
    {
        $user = new User();
        $date = new \DateTime();
        $dateFalse = new \DateTime('-1 day');
        $user->setEmail('true@test.com')
       ->setFirstname('firstname')
       ->setLastname('lastname')
       ->setPassword('password')
       ->setRoles(['ROLE_USER'])
       ->setStatus(1)
       ->setToken('test')
       ->setRegistrationDate($date)
       ->setLastConnectionDate($date)
       ->setTokenDate($date)
       ->setPicture('picture.jpg');

        $this->assertFalse($user->getEmail() === 'false@test.com');
        $this->assertFalse($user->getFirstname() === 'falsefirstname');
        $this->assertFalse($user->getLastname() === 'falselastname');
        $this->assertFalse($user->getPassword() === 'falsepassword');
        $this->assertFalse($user->getRoles() === ['ROLE_ADMIN']);
        $this->assertFalse($user->getStatus() === 2);
        $this->assertFalse($user->getToken() === 'Falsetest');
        $this->assertFalse($user->getRegistrationDate() === $dateFalse);
        $this->assertFalse($user->getLastConnectionDate() === $dateFalse);
        $this->assertFalse($user->getTokenDate() === $dateFalse);
        $this->assertFalse($user->getPicture() === 'falsepicture.jpg');
    }

    public function testIsEmpty(): void
    {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getFirstname());
        $this->assertEmpty($user->getLastname());
        $this->assertEmpty($user->getId());
        $this->assertEmpty($user->getStatus());
        $this->assertEmpty($user->getToken());
        $this->assertEmpty($user->getRegistrationDate());
        $this->assertEmpty($user->getLastConnectionDate());
        $this->assertEmpty($user->getTokenDate());
        $this->assertEmpty($user->getPicture());
    }
    public function testIsNotEmpty(): void
    {
        $user = new User();
        $this->assertNotEmpty($user->generateToken());
    }

    public function testAddGetRemoveComment(): void
    {
        $user = new User();
        $comment = new Comment();

        $this->assertEmpty($user->getComments());

        $user->addComment($comment);
        $this->assertContains($comment, $user->getComments());

        $user->removeComment($comment);
        $this->assertEmpty($user->getComments());
    }

    public function testAddGetRemoveTrick(): void
    {
        $user = new User();
        $trick = new Trick();

        $this->assertEmpty($user->getTricks());

        $user->addTrick($trick);
        $this->assertContains($trick, $user->getTricks());

        $user->removeTrick($trick);
        $this->assertEmpty($user->getTricks());
    }
}
