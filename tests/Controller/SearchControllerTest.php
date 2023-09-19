<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\BottleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SearchControllerTest extends WebTestCase
{
    private $client;
    private BottleRepository $bottleRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->bottleRepository = $this->client->getContainer()->get(BottleRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testSearch(): void
    {
        $uri = $this->base_url . '/user/' . $this->user->getId();

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('POST', $uri);

        $form = $crawler->selectButton('search')->form();
        $form['query'] = 'test_create_empty';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'test_create_empty',
            $this->client->getResponse()->getContent()
        );
    }

    public function testSearchFailed(): void
    {
        $uri = $this->base_url . '/user/' . $this->user->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('search')->form();
        $form['query'] = 'test_failed';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'Aucun résultat',
            $this->client->getResponse()->getContent()
        );
    }

    public function testSearchVine(): void
    {
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_create_empty']);
        $bottle->setVine('Merlot');
        $this->bottleRepository->save($bottle, true);

        $uri = $this->base_url . '/search/' . $bottle->getId() . '/vine';

        $this->client->loginUser($this->user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'https://fr.wikipedia.org/wiki/Merlot',
            $this->client->getResponse()->getContent()
        );
    }

    public function testSearchVineFailed(): void
    {
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_create_empty']);
        $uri = $this->base_url . '/search/' . $bottle->getId() . '/vine';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }
}
