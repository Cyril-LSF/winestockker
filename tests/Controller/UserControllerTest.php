<?php

namespace App\Tests\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private $client;
    private UserRepository $userRepository;
    private ParameterBagInterface $params;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
    }

    private function _getUser(): ?User
    {
        return $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testCreateUser(): void
    {
        $uri = $this->params->get('app.base_url') . '/register';

        $crawler = $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john';
        $form['registration_form[lastname]'] = 'doe';
        $form['registration_form[email]'] = 'john@doe.fr';
        $form['registration_form[birthday]'] = '1994-11-30';
        $form['registration_form[password][first]'] = 'Azerty1!';
        $form['registration_form[password][second]'] = 'Azerty1!';
        $form['registration_form[agreeTerms]'] = true;

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testCreateUserFailed(): void
    {
        $uri = $this->params->get('app.base_url') . '/register';
        $crawler = $this->client->request('GET', $uri);

        $date = new DateTime();
        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john';
        $form['registration_form[lastname]'] = 'doe';
        $form['registration_form[email]'] = 'john@doe.fr';
        $form['registration_form[birthday]'] = $date->format('Y-m-d');
        $form['registration_form[password][first]'] = 'Azerty1!';
        $form['registration_form[password][second]'] = 'Azerty1!';
        $form['registration_form[agreeTerms]'] = true;

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Cette adresse email est déjà asscoiée à un compte',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Vous devez avoir 18 ans minimum pour vous inscrire',
            $this->client->getResponse()->getContent()
        );
    }

    public function testShowUser(): void
    {
        $user = $this->_getUser();

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);

        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString('john D.', $this->client->getResponse()->getContent());
    }

    public function testShowUserFailed(): void
    {
        $user = $this->_getUser();

        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId();

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testEditUser(): void
    {
        $user = $this->_getUser();
        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId() . '/edit';

        $crawler = $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john-edit';

        $this->client->submit($form);
        $user = $this->userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertTrue($user->getFirstname() === 'john-edit');
    }

    public function testEditUserFailed(): void
    {
        $user = $this->_getUser();
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId() . '/edit';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john_edit_failed';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            "Seul les lettres, les tirets et les espaces sont autorisés",
            $this->client->getResponse()->getContent());
    }

    public function testEditPassword(): void
    {
        $encoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);

        $user = $this->_getUser();
        $this->client->loginUser($user);

        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId() . '/edit_password';
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('edit-password')->form();
        $form['edit_password[oldPassword]'] = 'Azerty1!';
        $form['edit_password[password][first]'] = 'Azerty2!';
        $form['edit_password[password][second]'] = 'Azerty2!';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/' . $user->getId());

        $user = $this->userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertTrue($encoder->isPasswordValid($user, 'Azerty2!'));
    }

    public function testEditPasswordFailed(): void
    {
        $user = $this->_getUser();
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId() . '/edit_password';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('edit-password')->form();
        $form['edit_password[oldPassword]'] = 'Azerty2!';
        $form['edit_password[password][first]'] = 'Azerty1!';
        $form['edit_password[password][second]'] = 'Azerty3!';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Les mots de passe ne sont pas identique',
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreateUserAddress(): void
    {
        $addressRepository = $this->client->getContainer()->get(AddressRepository::class);

        $user = $this->_getUser();
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId();

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-address')->form();
        $form['address[name]'] = 'test address';
        $form['address[streetNumber]'] = '1';
        $form['address[streetNumberExtension]'] = 'bis';
        $form['address[streetType]'] = 'rue';
        $form['address[streetName]'] = 'de Paris';
        $form['address[complement]'] = '3e étage';
        $form['address[postalcode]'] = '01000';
        $form['address[city]'] = 'Paris';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(201);

        $address = $addressRepository->findOneBy(['name' => 'test address']);

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
    }

    public function testCreateUserAddressFailed(): void
    {
        $user = $this->_getUser();
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId();

        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-address')->form();
        $form['address[name]'] = 'te';
        $form['address[streetNumber]'] = 'test';
        $form['address[streetNumberExtension]'] = 'bis1';
        $form['address[streetName]'] = 'de';
        $form['address[postalcode]'] = 'test';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Le nom doit contenir au minimum 3 caractères',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Le numéro ne doit contenir que des chiffres',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'L&#039;extension ne doit contenir que des lettres',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Le nom de rue doit contenir au minimum 3 caractères',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Le code postal ne doit contenir que des chiffres',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Veuillez saisir une ville',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteUser(): void
    {
        $user = $this->_getUser();
        $this->client->loginUser($user);

        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId();
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('delete-user')->form();

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/login');

        $user = $this->userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertNull($user);
    }

}
