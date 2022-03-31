<?php

namespace App\Tests\Unit\Entity;

use App\Entity\MediaVideo;
use App\Entity\Trick;
use PHPUnit\Framework\TestCase;

class MediaVideoTest extends TestCase
{
    public function testIsTrue(): void
    {
        $video = new MediaVideo();
        $trick = new Trick();
        $video->setName('Test')
       ->setTrick($trick);

        $this->assertTrue($video->getName() === 'Test');
        $this->assertTrue($video->getTrick() === $trick);
    }

    public function testIsFalse(): void
    {
        $video = new MediaVideo();
        $trick = new Trick();
        $trickFalse = new Trick();
        $video->setName('Test')
       ->setTrick($trick);

        $this->assertFalse($video->getName() === 'FalseTest');
        $this->assertFalse($video->getTrick() === $trickFalse);
    }

    public function testIsEmpty(): void
    {
        $video = new MediaVideo();

        $this->assertEmpty($video->getName());
        $this->assertEmpty($video->getTrick());
        $this->assertEmpty($video->getId());
    }
}
