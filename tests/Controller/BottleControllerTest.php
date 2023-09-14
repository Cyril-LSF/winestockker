<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\BottleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BottleControlleurTest1 extends WebTestCase
{
    private $client;
    private BottleRepository $bottleRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->bottleRepository = $this->client->getContainer()->get(BottleRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
    }

    private function _getUser(): User
    {
        return $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testCreateBottle(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/bottle/new';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-bottle')->form();
        $form['bottle[name]'] = 'test_create';
        $form['bottle[year]'] = '1994';
        $form['bottle[origin]'] = 'FR';
        $form['bottle[vine]'] = 'merlot';
        $form['bottle[enbottler]'] = 'Gérard Bertrand';
        $form['bottle[price]'] = '43';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/bottle/');
    }

    public function testCreateEmptyBottle(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/bottle/new';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-bottle')->form();

        $form['bottle[name]'] = 'test_create_empty';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/bottle/');
    }

    public function testCreateBottleFailed(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/bottle/new';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-bottle')->form();
        $form['bottle[name]'] = '';
        $form['bottle[year]'] = 'mille neuf cent quatre-vingt quatorze';
        $form['bottle[vine]'] = '1234';
        $form['bottle[enbottler]'] = 'Gérard Bertrand1';
        $form['bottle[price]'] = '12345678912';

        $this->client->submit($form);
        $response = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez saisir un nom',
            $response
        );
        $this->assertStringContainsString(
            'Seul les chiffres sont autorisés',
            $response
        );
        $this->assertStringContainsString(
            'Seul les lettres sont autorisées',
            $response
        );
    }

    public function testListBottle(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/bottle/';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $this->client->request('GET', $uri);
        $response = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'TEST_CREATE', $response
        );
        $this->assertStringContainsString(
            'TEST_CREATE_EMPTY', $response
        );
    }

    public function testEditBottle(): void
    {
        $user = $this->_getUser();
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_create']);
        $uri = $this->base_url . '/bottle/' . $bottle->getId() . '/edit';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-bottle')->form();
        $form['bottle[name]'] = 'test_edit';

        $this->client->submit($form);
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/bottle/');
        $this->assertNotEmpty($bottle);
        $this->assertTrue($bottle->getName() === 'test_edit');
    }

    public function testEditBottleFailed(): void
    {
        $user = $this->_getUser();
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/bottle/' . $bottle->getId() . '/edit';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-bottle')->form();
        $form['bottle[name]'] = 'test_edit_failed';
        $form['bottle[year]'] = 'mille neuf cent quatre-vingt quatorze';

        $this->client->submit($form);
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_edit_failed']);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Seul les chiffres sont autorisés',
            $this->client->getResponse()->getContent()
        );
        $this->assertEmpty($bottle);
    }

    public function testDeleteBottle(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/bottle/';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('delete-bottle')->form();

        $this->client->submit($form);
        $bottle = $this->bottleRepository->findOneBy(['name' => 'test_create']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/bottle/');
        $this->assertEmpty($bottle);
    }
}
