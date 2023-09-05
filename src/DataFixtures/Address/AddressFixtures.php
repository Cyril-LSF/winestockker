<?php

namespace App\DataFixtures\Address;

use App\Entity\Address;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class AddressFixtures extends Fixture implements OrderedFixtureInterface
{    
    public function load(ObjectManager $manager): void
    {
        $addresses = [
            [
                'name' => "adresse de John",
                'author' => "john@doe.fr",
                'streetNumber' => "1",
                'streetType' => "rue",
                'streetName' => "de Paris",
                'postalcode' => "01000",
                'city' => "Paris",
            ],
            [
                'name' => "adresse de Jane",
                'author' => "jane@doe.fr",
                'streetNumber' => "2",
                'streetType' => "impasse",
                'streetName' => "de Paris",
                'postalcode' => "01000",
                'city' => "Paris",
            ],
            [
                'name' => "adresse de Bob",
                'author' => "bob@doe.fr",
                'streetNumber' => "3",
                'streetType' => "boulevard",
                'streetName' => "de Paris",
                'postalcode' => "01000",
                'city' => "Paris",
            ],
            [
                'name' => "adresse de Patrick",
                'author' => "patrick@doe.fr",
                'streetNumber' => "4",
                'streetType' => "quai",
                'streetName' => "de Paris",
                'postalcode' => "01000",
                'city' => "Paris",
            ]
        ];

        foreach ($addresses as $item) {
            $address = new Address();
            $address->setName($item['name']);
            $address->setAuthor($this->getReference($item['author']));
            $address->setStreetNumber($item['streetNumber']);
            $address->setStreetType($item['streetType']);
            $address->setStreetName($item['streetName']);
            $address->setPostalcode($item['postalcode']);
            $address->setCity($item['city']);
            $address->setCreatedAt(new DateTime());
            $address->setSelected(1);

            $manager->persist($address);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 5;
    }
}
