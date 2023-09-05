<?php

namespace App\Tests\Entity;

use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubscriptionTest extends KernelTestCase
{
    public function testSubscriptionEntity(): void
    {
        $subscription = new Subscription();
        $subscription->setName('test subscription');
        $subscription->setPrice('2');
        $subscription->setPriceInCents('200');
        $subscription->setDuration(7);

        $this->assertEquals('test subscription', $subscription->getName());
        $this->assertEquals('2', $subscription->getPrice());
        $this->assertEquals('200', $subscription->getPriceInCents());
        $this->assertEquals(7, $subscription->getDuration());
    }
}
