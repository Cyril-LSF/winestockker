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
    private function _start(): array
    {
        $client = static::createClient();
        $params = $client->getContainer()->get(ParameterBagInterface::class);
        $userRepository = $client->getContainer()->get(UserRepository::class);

        return compact('client', 'params', 'userRepository');
    }

    private function _getUser(UserRepository $userRepository): ?User
    {
        return $userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testCreateUser(): void
    {
        extract($this->_start());
        $uri = $params->get('app.base_url') . '/register';

        $crawler = $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john';
        $form['registration_form[lastname]'] = 'doe';
        $form['registration_form[email]'] = 'john@doe.fr';
        $form['registration_form[birthday]'] = '1994-11-30';
        $form['registration_form[password][first]'] = 'Azerty1!';
        $form['registration_form[password][second]'] = 'Azerty1!';
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testCreateUserFailed(): void
    {
        extract($this->_start());

        $uri = $params->get('app.base_url') . '/register';
        $crawler = $client->request('GET', $uri);

        $date = new DateTime();
        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john';
        $form['registration_form[lastname]'] = 'doe';
        $form['registration_form[email]'] = 'john@doe.fr';
        $form['registration_form[birthday]'] = $date->format('Y-m-d');
        $form['registration_form[password][first]'] = 'Azerty1!';
        $form['registration_form[password][second]'] = 'Azerty1!';
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Cette adresse email est déjà asscoiée à un compte',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Vous devez avoir 18 ans minimum pour vous inscrire',
            $client->getResponse()->getContent()
        );
    }

    public function testShowUser(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);

        $client->loginUser($user);
        $uri = $params->get('app.base_url') . '/user/' . $user->getId();

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString('john D.', $client->getResponse()->getContent());
    }

    public function testShowUserFailed(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);

        $uri = $params->get('app.base_url') . '/user/' . $user->getId();

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testEditUser(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);
        $client->loginUser($user);
        $uri = $params->get('app.base_url') . '/user/' . $user->getId() . '/edit';

        $crawler = $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(200);

        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john-edit';

        $client->submit($form);
        $user = $userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertTrue($user->getFirstname() === 'john-edit');
    }

    public function testEditUserFailed(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);
        $uri = $params->get('app.base_url') . '/user/' . $user->getId() . '/edit';

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $client->loginUser($user);
        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('register')->form();
        $form['registration_form[firstname]'] = 'john_edit_failed';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            "Seul les lettres, les tirets et les espaces sont autorisés",
            $client->getResponse()->getContent());
    }

    public function testEditPassword(): void
    {
        extract($this->_start());
        $encoder = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $user = $this->_getUser($userRepository);
        $client->loginUser($user);

        $uri = $params->get('app.base_url') . '/user/' . $user->getId() . '/edit_password';
        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('edit-password')->form();
        $form['edit_password[oldPassword]'] = 'Azerty1!';
        $form['edit_password[password][first]'] = 'Azerty2!';
        $form['edit_password[password][second]'] = 'Azerty2!';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/' . $user->getId());

        $user = $userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertTrue($encoder->isPasswordValid($user, 'Azerty2!'));
    }

    public function testEditPasswordFailed(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);
        $uri = $params->get('app.base_url') . '/user/' . $user->getId() . '/edit_password';

        $client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $client->loginUser($user);
        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('edit-password')->form();
        $form['edit_password[oldPassword]'] = 'Azerty2!';
        $form['edit_password[password][first]'] = 'Azerty1!';
        $form['edit_password[password][second]'] = 'Azerty3!';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Les mots de passe ne sont pas identique',
            $client->getResponse()->getContent()
        );
    }

    public function testCreateUserAddress(): void
    {
        extract($this->_start());
        $addressRepository = $client->getContainer()->get(AddressRepository::class);

        $user = $this->_getUser($userRepository);
        $uri = $params->get('app.base_url') . '/user/' . $user->getId();

        $client->loginUser($user);
        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('create-address')->form();
        $form['address[name]'] = 'test address';
        $form['address[streetNumber]'] = '1';
        $form['address[streetNumberExtension]'] = 'bis';
        $form['address[streetType]'] = 'rue';
        $form['address[streetName]'] = 'de Paris';
        $form['address[complement]'] = '3e étage';
        $form['address[postalcode]'] = '01000';
        $form['address[city]'] = 'Paris';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(201);

        $address = $addressRepository->findOneBy(['name' => 'test address']);

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
    }

    public function testCreateUserAddressFailed(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);
        $uri = $params->get('app.base_url') . '/user/' . $user->getId();

        $client->loginUser($user);
        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('create-address')->form();
        $form['address[name]'] = 'te';
        $form['address[streetNumber]'] = 'test';
        $form['address[streetNumberExtension]'] = 'bis1';
        $form['address[streetName]'] = 'de';
        $form['address[postalcode]'] = 'test';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Le nom doit contenir au minimum 3 caractères',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Le numéro ne doit contenir que des chiffres',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'L&#039;extension ne doit contenir que des lettres',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Le nom de rue doit contenir au minimum 3 caractères',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Le code postal ne doit contenir que des chiffres',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'Veuillez saisir une ville',
            $client->getResponse()->getContent()
        );
    }

    public function testDeleteUser(): void
    {
        extract($this->_start());

        $user = $this->_getUser($userRepository);
        $client->loginUser($user);

        $uri = $params->get('app.base_url') . '/user/' . $user->getId();
        $crawler = $client->request('GET', $uri);

        $form = $crawler->selectButton('delete-user')->form();

        $client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/login');

        $user = $userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertNull($user);
    }

}
