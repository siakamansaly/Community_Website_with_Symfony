<?php

namespace App\Tests\Unit\Entity;

use App\Entity\MediaPicture;
use App\Entity\Trick;
use PHPUnit\Framework\TestCase;

class MediaPictureTest extends TestCase
{
    public function testIsTrue(): void
    {
        $picture = new MediaPicture();
        $trick = new Trick();
        $picture->setName('Test')
       ->setTrick($trick);

        $this->assertTrue($picture->getName() === 'Test');
        $this->assertTrue($picture->getTrick() === $trick);
    }

    public function testIsFalse(): void
    {
        $picture = new MediaPicture();
        $trick = new Trick();
        $trickFalse = new Trick();
        $picture->setName('Test')
       ->setTrick($trick);

        $this->assertFalse($picture->getName() === 'FalseTest');
        $this->assertFalse($picture->getTrick() === $trickFalse);
    }

    public function testIsEmpty(): void
    {
        $picture = new Mediapicture();

        $this->assertEmpty($picture->getName());
        $this->assertEmpty($picture->getTrick());
        $this->assertEmpty($picture->getId());
    }
}
