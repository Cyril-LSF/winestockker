<?php

namespace App\Tests\Entity;

use DateTime;
use App\Entity\User;
use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddressTest extends KernelTestCase
{
    public function testAddressEntity(): void
    {
        $address = new Address();
        $address->setName('test address');
        $address->setauthor(new User());
        $address->setStreetNumber('1');
        $address->setStreetNumberExtension('bis');
        $address->setStreetType('rue');
        $address->setStreetName('de paris');
        $address->setComplement('3e etage');
        $address->setPostalcode('01000');
        $address->setCity('paris');
        $address->setCreatedAt();

        $this->assertEquals('test address', $address->getName());
        $this->assertEquals('1', $address->getStreetNumber());
        $this->assertEquals('bis', $address->getStreetNumberExtension());
        $this->assertEquals('rue', $address->getStreetType());
        $this->assertEquals('de paris', $address->getStreetName());
        $this->assertEquals('3e etage', $address->getComplement());
        $this->assertEquals('01000', $address->getPostalcode());
        $this->assertEquals('paris', $address->getCity());

        $this->assertInstanceOf(User::class, $address->getAuthor());
        $this->assertInstanceOf(DateTime::class, $address->getCreatedAt());
    }
}
