<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Trick;
use App\Entity\TypeTrick;
use PHPUnit\Framework\TestCase;

class TypeTrickTest extends TestCase
{
    public function testIsTrue(): void
    {
        $typeTrick = new TypeTrick();
        $typeTrick->setName('Test')
       ->setDescription('Lorem ipsum');

        $this->assertTrue($typeTrick->getName() === 'Test');
        $this->assertTrue($typeTrick->getDescription() === 'Lorem ipsum');
    }

    public function testIsFalse(): void
    {
        $typeTrick = new TypeTrick();
        $typeTrick->setName('Test')
       ->setDescription('Lorem ipsum');

        $this->assertFalse($typeTrick->getName() === 'False Test');
        $this->assertFalse($typeTrick->getDescription() === 'False Lorem ipsum');
    }

    public function testIsEmpty(): void
    {
        $typeTrick = new TypeTrick();

        $this->assertEmpty($typeTrick->getName());
        $this->assertEmpty($typeTrick->getDescription());
        $this->assertEmpty($typeTrick->getId());
    }
    public function testAddGetRemoveTypeTrick(): void
    {
        $typeTrick = new TypeTrick();
        $trick = new Trick();

        $this->assertEmpty($typeTrick->getTricks());

        $typeTrick->addTrick($trick);
        $this->assertContains($trick, $typeTrick->getTricks());

        $typeTrick->removeTrick($trick);
        $this->assertEmpty($typeTrick->getTricks());
    }
}
