<?php

namespace App\Tests\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use App\Repository\TransactionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TransactionControllerTest extends WebTestCase
{
    private $client;
    private TransactionRepository $transactionRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->transactionRepository = $this->client->getContainer()->get(TransactionRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    private function _getTransaction(): Transaction
    {
        $subscription = $this->client
                    ->getContainer()
                    ->get(SubscriptionRepository::class)
                    ->findOneBy(['name' => 'test_payment']);
        $transaction = $this->transactionRepository->findOneBy(['paymentId' => 'test_transaction']);
        if (empty($transaction)) {
            $transaction = new Transaction();
            $transaction->setUser($this->user);
            $transaction->setSubscriptionId($subscription->getId());
            $transaction->setPaymentId('test_transaction');
            $transaction->setCreatedAt(new DateTime());
            $transaction->setAmount('10');
            $transaction->setInvoice('factureTest');

            $this->transactionRepository->save($transaction, true);
        }
        return $transaction;
    }

    public function testListTransaction(): void
    {
        $this->_getTransaction();
        $uri = $this->base_url . '/transactions/';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'test_transaction',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDownloadTransaction(): void
    {
        $kernel = $this->client->getContainer()->get('kernel');
        $transaction = $this->_getTransaction();
        $uri = $this->base_url . '/transactions/download/' . $transaction->getId();

        $this->client->loginUser($this->user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertFileEquals(
            $kernel->getProjectDir() . '/var/invoices/' . 'factureTest.pdf',
            $this->client->getResponse()->getFile()
        );
        $this->assertEquals(
            'application/pdf',
            $this->client->getResponse()->headers->get('Content-Type')
        );
    }

    public function testDownloadTransactionFailed(): void
    {
        $transaction = $this->_getTransaction();
        $uri = $this->base_url . '/transactions/download/' . $transaction->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }
}
