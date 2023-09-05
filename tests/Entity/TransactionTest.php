<?php

namespace App\Tests\Entity;

use App\Entity\Transaction;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionTest extends KernelTestCase
{
    public function testTransactionEntity(): void
    {
        $transaction = new Transaction();
        $transaction->setUser(new User());
        $transaction->setSubscriptionId(1);
        $transaction->setPaymentId('1');
        $transaction->setCreatedAt(new DateTime('2023/01/01'));
        $transaction->setAmount('2');
        $transaction->setInvoice('factureTest.pdf');

        $this->assertEquals(1, $transaction->getSubscriptionId());
        $this->assertEquals('1', $transaction->getPaymentId());
        $this->assertEquals(new DateTime('2023/01/01'), $transaction->getCreatedAt());
        $this->assertEquals('2', $transaction->getAmount());
        $this->assertEquals('factureTest.pdf', $transaction->getInvoice());

        $this->assertInstanceOf(User::class, $transaction->getUser());
    }
}
