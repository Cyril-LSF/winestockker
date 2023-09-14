<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\BottleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CategoryControllerTest extends WebTestCase
{
    private $client;
    private CategoryRepository $categoryRepository;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->categoryRepository = $this->client->getContainer()->get(CategoryRepository::class);
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
    }

    private function _getUser(): User
    {
        return $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testCreateCategory(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/category/new';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-category')->form();
        $form['category[name]'] = 'test_create';

        $this->client->submit($form);
        $category = $this->categoryRepository->findOneBy(['name' => 'test_create']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertNotEmpty($category);
        $this->assertResponseRedirects('/category/' . $category->getId());
    }

    public function testCreateCategoryFailed(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/category/new';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-category')->form();
        $form['category[name]'] = '';

        $this->client->submit($form);
        $category = $this->categoryRepository->findOneBy(['name' => '']);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez saisir un nom',
            $this->client->getResponse()->getContent()
        );
        $this->assertEmpty($category);
    }

    public function testListCategory(): void
    {
        $user = $this->_getUser();
        $uri = $this->base_url . '/category/';

        $this->client->loginUser($user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'test_create',
            $this->client->getResponse()->getContent()
        );
    }

    public function testListCategoryFailed(): void
    {
        $uri = $this->base_url . '/category/';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testEditCategory(): void
    {
        $user = $this->_getUser();
        $category = $this->categoryRepository->findOneBy(['name' => 'test_create']);
        $uri = $this->base_url . '/category/' . $category->getId() . '/edit';

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-category')->form();
        $form['category[name]'] = 'test_edit';

        $this->client->submit($form);
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertNotEmpty($category);
        $this->assertResponseRedirects('/category/' . $category->getId());
        $this->assertTrue($category->getName() === 'test_edit');
    }

    public function testEditCategoryFailed(): void
    {
        $user = $this->_getUser();
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/category/' . $category->getId() . '/edit';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-category')->form();
        $form['category[name]'] = '';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez remplir tous les champs obligatoires',
            $this->client->getResponse()->getContent()
        );
    }

    public function testShowCategory(): void
    {
        $user = $this->_getUser();
        $category = $this->categoryRepository->findoneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/category/' . $category->getId();

        $this->client->loginUser($user);
        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString(
            'test_edit',
            $this->client->getResponse()->getContent()
        );
    }

    public function testShowCategoryFailed(): void
    {
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/category/' . $category->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testAddBottle(): void
    {
        $user = $this->_getUser();
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/category/' . $category->getId();
        $bottleRepository = $this->client->getContainer()->get(BottleRepository::class);
        $bottle = $bottleRepository->findOneBy(['name' => 'test_create_empty']);

        $this->assertEmpty($category->getBottles());

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('add-bottle')->form();
        $form['add_bottle[bottles]'] = [$bottle->getId()];

        $this->client->submit($form);
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/category/' . $category->getId());
        $this->assertNotEmpty($category->getBottles());
    }

    public function testRemoveBottle(): void
    {
        $user = $this->_getUser();
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/category/' . $category->getId();

        $this->assertNotEmpty($category->getBottles());

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('add-bottle')->form();
        $formData = $form->getPhpValues();
        foreach ($formData['add_bottle']['bottles'] as $key => $value) {
            $formData['add_bottle']['bottles'][$key] = false;
        }

        $this->client->submit($form, $formData);
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/category/' . $category->getId());
        $this->assertEmpty($category->getBottles());
    }

    public function testDeleteCategory(): void
    {
        $user = $this->_getUser();
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);
        $uri = $this->base_url . '/category/' . $category->getId();

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('delete-category')->form();

        $this->client->submit($form);
        $category = $this->categoryRepository->findOneBy(['name' => 'test_edit']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/category/');
        $this->assertEmpty($category);
    }
}
