<?php

namespace App\DataFixtures\Cellar;

use App\Entity\Cellar;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class CellarFixtures extends Fixture implements OrderedFixtureInterface
{    
    public function load(ObjectManager $manager): void
    {
        $cellars = [
            [
                'name' => "cave de John",
                'author' => "john@doe.fr",
            ],
            [
                'name' => "cave de Jane",
                'author' => "jane@doe.fr",
            ],
            [
                'name' => "cave de Bob",
                'author' => "bob@doe.fr",
            ],
            [
                'name' => "cave de Patrick",
                'author' => "patrick@doe.fr",
            ]
        ];

        foreach ($cellars as $item) {
            $cellar = new Cellar();
            $cellar->setName($item['name']);
            $cellar->setAuthor($this->getReference($item['author']));
            $cellar->setCreatedAt(new DateTime());

            $manager->persist($cellar);

            $this->setReference($item['name'], $cellar);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 3;
    }
}
