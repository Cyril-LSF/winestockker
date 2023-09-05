<?php

namespace App\Tests\Entity;

use App\Entity\Cellar;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CellarTest extends KernelTestCase
{
    public function testCellarEntity(): void
    {
        $cellar = new Cellar();
        $cellar->setName('test cellar');
        $cellar->setCreatedAt(new DateTime('2023/01/01'));
        $cellar->setAuthor(new User());

        $this->assertEquals('test cellar', $cellar->getName());
        $this->assertEquals(new DateTime('2023/01/01'), $cellar->getCreatedAt());

        $this->assertInstanceOf(User::class, $cellar->getAuthor());

    }
}
