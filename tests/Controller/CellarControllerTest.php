<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\BottleRepository;
use App\Repository\CellarRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CellarControllerTest extends WebTestCase
{
    private $client;
    private CellarRepository $cellarRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->cellarRepository = $this->client->getContainer()->get(CellarRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
    }

    private function _getUser(): User
    {
        return $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testCreateCellar(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/cellar/new';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-cellar')->form();
        $form['cellar[name]'] = 'test_create';

        $this->client->submit($form);
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_create']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertNotEmpty($cellar);
        $this->assertResponseRedirects('/cellar/' . $cellar->getId());
    }

    public function testCreateCellarFailed(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/cellar/new';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-cellar')->form();
        $form['cellar[name]'] = '';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez saisir un nom',
            $this->client->getResponse()->getContent()
        );
    }

    public function testListCellar(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/cellar/';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'test_create',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEditCellar(): void
    {
        $user = $this->_getUser();
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_create']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId() . '/edit';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-cellar')->form();
        $form['cellar[name]'] = 'test_edit';

        $this->client->submit($form);
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertNotEmpty($cellar);
        $this->assertResponseRedirects('/cellar/' . $cellar->getId());
        $this->assertTrue($cellar->getName() === 'test_edit');
    }

    public function testEditCellarFailed(): void
    {
        $user = $this->_getUser();
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId() . '/edit';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-cellar')->form();
        $form['cellar[name]'] = '';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez remplir tous les champs obligatoires',
            $this->client->getResponse()->getContent()
        );
    }

    public function testShowCellar(): void
    {
        $user = $this->_getUser();
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId();

        $this->client->loginUser($user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'TEST_EDIT',
            $this->client->getResponse()->getContent()
        );
    }

    public function testShowCellarFailed(): void
    {
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testAddBottle(): void
    {
        $user = $this->_getUser();
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId();
        $bottleRepository = $this->client->getContainer()->get(BottleRepository::class);
        $bottle = $bottleRepository->findOneBy(['name' => 'test_create_empty']);

        $this->assertEmpty($cellar->getBottles());

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('add-bottle')->form();
        $form['add_bottle[bottles]'] = [$bottle->getId()];

        $this->client->submit($form);
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/cellar/' . $cellar->getId());
        $this->assertNotEmpty($cellar->getBottles());
    }

    public function testRemoveBottle(): void
    {
        $user = $this->_getUser();
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId();

        $this->assertNotEmpty($cellar->getBottles());

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('add-bottle')->form();
        $formData = $form->getPhpValues();
        foreach ($formData['add_bottle']['bottles'] as $key => $value) {
            $formData['add_bottle']['bottles'][$key] = false;
        }

        $this->client->submit($form, $formData);
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/cellar/' . $cellar->getId());
        $this->assertEmpty($cellar->getBottles());
    }

    public function testDeleteCellar(): void
    {
        $user = $this->_getUser();
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/cellar/' . $cellar->getId();

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('delete-cellar')->form();

        $this->client->submit($form);
        $cellar = $this->cellarRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/cellar/');
        $this->assertEmpty($cellar);
    }
}
