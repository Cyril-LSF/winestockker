<?php

namespace App\DataFixtures\Subscription;

use App\Entity\Subscription;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class SubscriptionFixtures extends Fixture implements OrderedFixtureInterface
{    
    public function load(ObjectManager $manager): void
    {
        $subscriptions = [
            [
                'name' => "abonnement 1",
                'price' => "2",
                'priceInCents' => "200",
                'duration' => "7",
            ],
            [
                'name' => "abonnement 2",
                'price' => "10",
                'priceInCents' => "1000",
                'duration' => "30",
            ],
            [
                'name' => "abonnement 3",
                'price' => "110",
                'priceInCents' => "11000",
                'duration' => "365",
            ],
            [
                'name' => "abonnement 4",
                'price' => "300",
                'priceInCents' => "30000",
                'duration' => "1095",
            ]
        ];

        foreach ($subscriptions as $item) {
            $subscription = new Subscription();
            $subscription->setName($item['name']);
            $subscription->setPrice($item['price']);
            $subscription->setPriceInCents($item['priceInCents']);
            $subscription->setDuration($item['duration']);

            $manager->persist($subscription);

            $this->setReference($item['name'], $subscription);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 7;
    }
}
