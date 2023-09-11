<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Repository\UserRepository;
use App\Repository\AddressRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AddressControllerTest extends WebTestCase
{
    private $client;
    private AddressRepository $addressRepository;
    private ParameterBagInterface $params;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->addressRepository = $this->client->getContainer()->get(AddressRepository::class);
    }

    private function _getUser(): User
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $encoder = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
        
        $user = $userRepository->findOneBy(['email' => 'john@doe.fr']);
        if (empty($user)) {
            $user = new User();
            $user->setFirstname('john');
            $user->setLastname('doe');
            $user->setEmail('john@doe.fr');
            $user->setBirthday(new DateTime('1994-11-30'));
            $user->setPassword($encoder->hashPassword($user, 'Azerty1!'));

            $userRepository->save($user, true);
        }
        return $user;
    }

    public function testAddressEdit(): void
    {
        $user = $this->_getUser();

        $address = new Address();
        $address->setName('test_edit');
        $address->setStreetNumber('1');
        $address->setStreetType('rue');
        $address->setStreetName('de Paris');
        $address->setPostalcode('01000');
        $address->setCity('Paris');
        $address->setauthor($user);

        $this->addressRepository->save($address, true);

        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/address/' . $address->getId() . '/edit';
        $crawler = $this->client->request('GET', $uri);
        
        $form = $crawler->selectButton('create-address')->form();
        $form['address[name]'] = 'test_edit_success';

        $this->client->submit($form);

        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertTrue($address->getName() === 'test_edit_success');
    }

    public function testAddressEditFailed(): void
    {
        $user = $this->_getUser();
        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/address/' . $address->getId() . '/edit';
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('create-address')->form();
        $form['address[name]'] = 'ko';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Le nom doit contenir au minimum 3 caractères',
            $this->client->getResponse()->getContent()
        );
    }

    public function testAddressSelected(): void
    {
        $user = $this->_getUser();
        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/address/' . $address->getId() . '/selected';
        $this->client->request('POST', $uri);

        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->assertResponseStatusCodeSame(200);
        $this->assertTrue($address->isSelected());
    }

    public function testAddressSelectedFailed(): void
    {
        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $uri = $this->params->get('app.base_url') . '/address/' . $address->getId() . '/selected';
        $this->client->request('POST', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testAddressUnselected(): void
    {
        $user = $this->_getUser();
        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/address/' . $address->getId() . '/selected';
        $this->client->request('POST', $uri);

        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->assertResponseStatusCodeSame(200);
        $this->assertFalse($address->isSelected());
    }

    public function testAddressDelete(): void
    {
        $user = $this->_getUser();
        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->client->loginUser($user);
        $uri = $this->params->get('app.base_url') . '/user/' . $user->getId();
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('delete-address')->form();
        $this->client->submit($form);

        $address = $this->addressRepository->findOneBy(['name' => 'test_edit_success']);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/' . $user->getId());
        $this->assertEmpty($address);
    }
}
