<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Comment;
use App\Entity\MediaPicture;
use App\Entity\MediaVideo;
use App\Entity\Trick;
use App\Entity\TypeTrick;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TrickTest extends TestCase
{
    public function testIsTrue(): void
    {
        $date = new \DateTime();
        $type = new TypeTrick();
        $user = new User();
        $trick = new Trick();
        $trick->setContent('content')
        ->setCreatedAt($date)
        ->setFeaturedPicture('picture.jpg')
        ->setSlug('picture')
        ->setTitle('title')
        ->setType($type)
        ->setUser($user)
        ->setUpdatedTo($date);

        $this->assertTrue($trick->getContent() === 'content');
        $this->assertTrue($trick->getCreatedAt() === $date);
        $this->assertTrue($trick->getFeaturedPicture() === 'picture.jpg');
        $this->assertTrue($trick->getSlug() === 'picture');
        $this->assertTrue($trick->getTitle() === 'title');
        $this->assertTrue($trick->getType() === $type);
        $this->assertTrue($trick->getUser() === $user);
        $this->assertTrue($trick->getUpdatedTo() === $date);
    }

    public function testIsFalse(): void
    {
        $date = new \DateTime();
        $dateFalse = new \DateTime('-1 day');
        $type = new TypeTrick();
        $typeFalse = new TypeTrick();
        $user = new User();
        $userFalse = new User();
        $trick = new Trick();
        $trick->setContent('content')
        ->setCreatedAt($date)
        ->setFeaturedPicture('picture.jpg')
        ->setSlug('picture')
        ->setTitle('title')
        ->setType($type)
        ->setUser($user)
        ->setUpdatedTo($date);

        $this->assertFalse($trick->getContent() === 'Falsecontent');
        $this->assertFalse($trick->getCreatedAt() === $dateFalse);
        $this->assertFalse($trick->getFeaturedPicture() === 'video.jpg');
        $this->assertFalse($trick->getSlug() === 'video');
        $this->assertFalse($trick->getTitle() === 'Falsetitle');
        $this->assertFalse($trick->getType() === $typeFalse);
        $this->assertFalse($trick->getUser() === $userFalse);
        $this->assertFalse($trick->getUpdatedTo() === $dateFalse);
    }

    public function testIsEmpty(): void
    {
        $trick = new Trick();

        $this->assertEmpty($trick->getContent());
        $this->assertEmpty($trick->getCreatedAt());
        $this->assertEmpty($trick->getFeaturedPicture());
        $this->assertEmpty($trick->getSlug());
        $this->assertEmpty($trick->getTitle());
        $this->assertEmpty($trick->getType());
        $this->assertEmpty($trick->getUser());
        $this->assertEmpty($trick->getId());
        $this->assertEmpty($trick->getUpdatedTo());
    }

    public function testAddGetRemoveComment(): void
    {
        $comment = new Comment();
        $trick = new Trick();

        $this->assertEmpty($trick->getComments());

        $trick->addComment($comment);
        $this->assertContains($comment, $trick->getComments());

        $trick->removeComment($comment);
        $this->assertEmpty($trick->getComments());
    }

    public function testAddGetRemovePicture(): void
    {
        $picture = new MediaPicture();
        $trick = new Trick();

        $this->assertEmpty($trick->getMediasPicture());

        $trick->addMediasPicture($picture);
        $this->assertContains($picture, $trick->getMediasPicture());

        $trick->removeMediasPicture($picture);
        $this->assertEmpty($trick->getMediasPicture());
    }

    public function testAddGetRemoveVideo(): void
    {
        $video = new MediaVideo();
        $trick = new Trick();

        $this->assertEmpty($trick->getMediasVideos());

        $trick->addMediasVideo($video);
        $this->assertContains($video, $trick->getMediasVideos());

        $trick->removeMediasVideo($video);
        $this->assertEmpty($trick->getMediasVideos());
    }
}
