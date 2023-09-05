<?php

namespace App\Tests\Entity;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Entity\Quantity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class QuantityTest extends KernelTestCase
{
    public function testQuantityEntity(): void
    {
        $quantity = new Quantity();
        $quantity->setCellar(new Cellar());
        $quantity->setBottle(new Bottle());
        $quantity->setQuantity('10');

        $this->assertEquals('10', $quantity->getQuantity());
        
        $this->assertInstanceOf(Cellar::class, $quantity->getCellar());
        $this->assertInstanceOf(Bottle::class, $quantity->getBottle());
    }
}
