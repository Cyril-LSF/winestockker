<?php

namespace App\Tests\Entity;

use App\Entity\Bottle;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BottleTest extends KernelTestCase
{
    public function testBottleEntity(): void
    {
        $bottle = new Bottle();
        $bottle->setName('chablis');
        $bottle->setYear('1994');
        $bottle->setOrigin('FR');
        $bottle->setVine('merlot');
        $bottle->setEnbottler('gerard bertrand');
        $bottle->setPrice('12');
        $bottle->setAuthor(new User());

        $this->assertEquals('chablis', $bottle->getName());
        $this->assertEquals('1994', $bottle->getYear());
        $this->assertEquals('FR', $bottle->getOrigin());
        $this->assertEquals('merlot', $bottle->getVine());
        $this->assertEquals('gerard bertrand', $bottle->getEnbottler());
        $this->assertEquals('12', $bottle->getPrice());

        $this->assertInstanceOf(User::class, $bottle->getAuthor());
    }

}
