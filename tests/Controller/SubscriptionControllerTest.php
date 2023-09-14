<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SubscriptionControllerTest extends WebTestCase
{
    private $client;
    private SubscriptionRepository $subscriptionRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->subscriptionRepository = $this->client->getContainer()->get(SubscriptionRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    private function _isAdmin(): void
    {
        if (!in_array('ROLE_ADMIN', $this->user->getRoles())) {
            $this->user->setRoles(["ROLE_USER", "ROLE_ADMIN"]);
            $this->userRepository->save($this->user, true);
        }
    }

    public function testCreateSubscription(): void
    {
        $uri = $this->base_url . '/subscription/new';
        $this->_isAdmin();

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-subscription')->form();
        $form['subscription[name]'] = 'test_create';
        $form['subscription[price]'] = '10';
        $form['subscription[priceInCents]'] = '1000';
        $form['subscription[duration]'] = 30;

        $this->client->submit($form);
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'test_create']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertNotEmpty($subscription);
        $this->assertResponseRedirects('/subscription/');
    }

    public function testCreateSubscriptionFailed(): void
    {
        $uri = $this->base_url . '/subscription/new';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-subscription')->form();
        $form['subscription[name]'] = 'ko';
        $form['subscription[price]'] = 'dix';
        $form['subscription[priceInCents]'] = '';
        $form['subscription[duration]'] = 'trente';

        $this->client->submit($form);
        $response = $this->client->getResponse()->getContent();
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'ko']);
        
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Le nom doit contenir minimum 3 caractères', $response
        );
        $this->assertStringContainsString(
            'Le prix ne doit contenir que des chiffres, caractères min.1 - max.50',
            $response
        );
        $this->assertStringContainsString(
            'Veuillez saisir un prix en centime',
            $response
        );
        $this->assertStringContainsString(
            'La durée ne doit contenir que des chiffres',
            $response
        );
        $this->assertEmpty($subscription);
    }

    public function testListSubscription(): void
    {
        $uri = $this->base_url . '/subscription/';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'test_create',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEditSubscription(): void
    {
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'test_create']);
        $uri = $this->base_url . '/subscription/' . $subscription->getId() . '/edit';

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-subscription')->form();
        $form['subscription[name]'] = 'test_edit';

        $this->client->submit($form);
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/subscription/');
        $this->assertNotEmpty($subscription);
    }

    public function testEditSubscriptionFailed(): void
    {
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/subscription/' . $subscription->getId() . '/edit';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-subscription')->form();
        $form['subscription[name]'] = 'test_edit_failed';
        $form['subscription[duration]'] = '';

        $this->client->submit($form);
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'test_edit_failed']);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez remplir tous les champs obligatoires',
            $this->client->getResponse()->getContent()
        );
        $this->assertEmpty($subscription);
    }

    public function testDeleteSubscription(): void
    {
        $uri = $this->base_url . '/subscription/';

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('delete-subscription')->form();

        $this->client->submit($form);
        $subscription = $this->subscriptionRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/subscription/');
        $this->assertEmpty($subscription);
    }

}
