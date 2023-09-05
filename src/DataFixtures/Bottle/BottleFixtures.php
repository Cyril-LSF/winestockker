<?php

namespace App\DataFixtures\Bottle;

use App\Entity\Bottle;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class BottleFixtures extends Fixture implements OrderedFixtureInterface
{    
    public function load(ObjectManager $manager): void
    {
        $bottles = [
            [
                'name' => "Beaujolais nouveau",
                'year' => "2001",
                'origin' => "FR",
                'vine' => "Merlot",
                'enbottler' => "Gérard Bertrand",
                'author' => "john@doe.fr",
                'price' => "12",
            ],
            [
                'name' => "Chablis",
                'year' => "2002",
                'origin' => "BE",
                'vine' => "Merlot",
                'enbottler' => "Gérard Bertrand",
                'author' => "jane@doe.fr",
                'price' => "32",
            ],
            [
                'name' => "Chinon",
                'year' => "2003",
                'origin' => "DE",
                'vine' => "Merlot",
                'enbottler' => "Gérard Bertrand",
                'author' => "bob@doe.fr",
                'price' => "24",
            ],
            [
                'name' => "Banuyls",
                'year' => "2004",
                'origin' => "ES",
                'vine' => "Merlot",
                'enbottler' => "Gérard Bertrand",
                'author' => "patrick@doe.fr",
                'price' => "56",
            ]
        ];

        foreach ($bottles as $item) {
            $bottle = new Bottle();
            $bottle->setName($item['name']);
            $bottle->setYear($item['year']);
            $bottle->setOrigin($item['origin']);
            $bottle->setVine($item['vine']);
            $bottle->setEnbottler($item['enbottler']);
            $bottle->setAuthor($this->getReference($item['author']));
            $bottle->setPrice($item['price']);

            $manager->persist($bottle);

            $this->setReference($item['name'], $bottle);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 2;
    }
}
