<?php

namespace App\DataFixtures\Quantity;

use App\Entity\Quantity;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class QuantityFixtures extends Fixture implements OrderedFixtureInterface
{    
    public function load(ObjectManager $manager): void
    {
        $quantities = [
            [
                'cellar' => "cave de John",
                'bottle' => "Beaujolais nouveau",
                'quantity' => "1",
            ],
            [
                'cellar' => "cave de Jane",
                'bottle' => "Chablis",
                'quantity' => "2",
            ],
            [
                'cellar' => "cave de Bob",
                'bottle' => "Chinon",
                'quantity' => "3",
            ],
            [
                'cellar' => "cave de Patrick",
                'bottle' => "Banuyls",
                'quantity' => "4",
            ]
        ];

        foreach ($quantities as $item) {
            $quantity = new Quantity();
            $quantity->setCellar($this->getReference($item['cellar']));
            $quantity->setBottle($this->getReference($item['bottle']));
            $quantity->setQuantity($item['quantity']);

            $manager->persist($quantity);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 6;
    }
}
