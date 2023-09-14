<?php

namespace App\Tests\Controller;

use App\Entity\Bottle;
use App\Entity\User;
use App\Repository\BottleRepository;
use App\Repository\CellarRepository;
use App\Repository\QuantityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QuantityControllerTest extends WebTestCase
{
    private $client;
    private QuantityRepository $quantityRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private CellarRepository $cellarRepository;
    private BottleRepository $bottleRepository;
    private Bottle $bottle;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->quantityRepository = $this->client->getContainer()->get(QuantityRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->cellarRepository = $this->client->getContainer()->get(CellarRepository::class);
        $this->bottleRepository = $this->client->getContainer()->get(BottleRepository::class);
        $this->bottle = $this->bottleRepository->findOneBy(['name' => 'test_create_empty']);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testEditQuantity(): void
    {
        $uri = $this->base_url . '/cellar/new';

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-cellar')->form();
        $form['cellar[name]'] = 'test_quantity';

        $this->client->submit($form);
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_quantity']);

        $uri = $this->base_url . '/cellar/' . $cellar->getId();
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('add-bottle')->form();
        $form['add_bottle[bottles]'] = [$this->bottle->getId()];

        $this->client->submit($form);
        $quantity = $this->quantityRepository->findOneBy(['cellar' => $cellar, 'bottle' => $this->bottle]);

        $this->assertNotEmpty($quantity);

        $uri = $this->base_url . '/quantity/edit/' . $cellar->getId() . '/' . $this->bottle->getId();
        $this->client->request('POST', $uri . '/add');

        $this->assertResponseStatusCodeSame(200);

        $this->client->request('POST', $uri . '/less');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEditBigQuantity(): void
    {
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_quantity']);
        $quantity = $this->quantityRepository->findOneBy(['cellar' => $cellar, 'bottle' => $this->bottle]);
        $uri = $this->base_url . '/quantity/edit/big/' . $quantity->getId();

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('edit-quantity')->form();
        $form['quantity[quantity]'] = 10;

        $this->client->submit($form);
        $quantity = $this->quantityRepository->findOneBy(['id' => $quantity->getId()]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/cellar/' . $cellar->getId());
        $this->assertEquals('10', $quantity->getQuantity());
    }
}
