<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testIsTrue(): void
    {
        $comment = new Comment();
        $trick = new Trick();
        $user = new User();
        $date = new \DateTime();
        $comment->setContent('Test')
        ->setCreatedAt($date)
        ->setUser($user)
        ->setTrick($trick);

        $this->assertTrue($comment->getContent() === 'Test');
        $this->assertTrue($comment->getTrick() === $trick);
        $this->assertTrue($comment->getUser() === $user);
        $this->assertTrue($comment->getCreatedAt() === $date);
    }

    public function testIsFalse(): void
    {
        $comment = new Comment();
        $trick = new Trick();
        $trickFalse = new Trick();
        $user = new User();
        $userFalse = new User();
        $date = new \DateTime();
        $dateFalse = new \DateTime('-1 day');
        $comment->setContent('Test')
        ->setCreatedAt($date)
        ->setUser($user)
        ->setTrick($trick);

        $this->assertFalse($comment->getContent() === 'FalseTest');
        $this->assertFalse($comment->getTrick() === $trickFalse);
        $this->assertFalse($comment->getUser() === $userFalse);
        $this->assertFalse($comment->getCreatedAt() === $dateFalse);
    }

    public function testIsEmpty(): void
    {
        $comment = new Comment();

        $this->assertEmpty($comment->getContent());
        $this->assertEmpty($comment->getTrick());
        $this->assertEmpty($comment->getUser());
        $this->assertEmpty($comment->getCreatedAt());
        $this->assertEmpty($comment->getId());
    }
}
