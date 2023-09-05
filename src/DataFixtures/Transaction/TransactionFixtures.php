<?php

namespace App\DataFixtures\Transaction;

use App\Entity\Transaction;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TransactionFixtures extends Fixture implements OrderedFixtureInterface
{  
    public function __construct(private ParameterBagInterface $params)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $transactions = [
            [
                'user' => "john@doe.fr",
                'subscription' => "abonnement 1",
                'paymentId' => "1",
                'duration' => "7",
                'amount' => "2",
            ],
            [
                'user' => "jane@doe.fr",
                'subscription' => "abonnement 2",
                'paymentId' => "2",
                'duration' => "30",
                'amount' => "10",
            ],
            [
                'user' => "bob@doe.fr",
                'subscription' => "abonnement 3",
                'paymentId' => "3",
                'duration' => "365",
                'amount' => "110",
            ],
            [
                'user' => "patrick@doe.fr",
                'subscription' => "abonnement 4",
                'paymentId' => "4",
                'duration' => "1095",
                'amount' => "300",
            ]
        ];

        foreach ($transactions as $item) {
            $transaction = new Transaction();
            $transaction->setUser($this->getReference($item['user']));
            $transaction->setSubscriptionId($this->getReference($item['subscription'])->getId());
            $transaction->setPaymentId($item['paymentId']);
            $transaction->setCreatedAt(new DateTime());
            $transaction->setAmount($item['amount']);
            $transaction->setInvoice($this->params->get('app.invoice_root') . 'factureTest.pdf');

            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 8;
    }
}
