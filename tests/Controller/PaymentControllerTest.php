<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Subscription;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentControllerTest extends WebTestCase
{
    private $client;
    private SubscriptionRepository $subscriptionRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;
    private User $user;
    private AddressRepository $addressRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->subscriptionRepository = $this->client->getContainer()->get(SubscriptionRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
        $this->addressRepository = $this->client->getContainer()->get(AddressRepository::class);
    }

    private function _getSubscription(): Subscription
    {
        $subscription = new Subscription();
        $subscription->setName('test_payment');
        $subscription->setPrice('10');
        $subscription->setPriceInCents('1000');
        $subscription->setDuration(30);

        $this->subscriptionRepository->save($subscription, true);
        return $subscription;
    }

    private function _getAddress(): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
        $address = new Address();
        $address->setName('test_payment');
        $address->setStreetNumber('1');
        $address->setStreetType('rue');
        $address->setStreetName('de Paris');
        $address->setPostalcode('01000');
        $address->setCity('Paris');
        $address->setCreatedAt();
        $address->setSelected(1);
        $address->setauthor($user);

        $this->addressRepository->save($address, true);
    }

    public function testPayment(): void
    {
        $subscription = $this->_getSubscription();
        $uri = $this->base_url . '/payment/payment/' . $subscription->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/subscription/');

        $this->_getAddress();
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertStringContainsString(
            'https://checkout.stripe.com',
            $this->client->getResponse()->getContent()
        );
    }
}
