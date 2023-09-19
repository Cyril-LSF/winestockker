<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContactControllerTest extends WebTestCase
{
    private $client;
    private ParameterBagInterface $params;
    private string $base_url;
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->params = $this->client->getContainer()->get(ParameterBagInterface::class);
        $this->base_url = $this->params->get('app.base_url');
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneBy(['email' => 'john@doe.fr']);
    }

    public function testContact(): void
    {
        $uri = $this->base_url . '/contact';

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('contact-send')->form();
        $form['contact[subject]'] = 'test_contact_subject';
        $form['contact[content]'] = 'test_contact_content';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/' . $this->user->getId());
    }

    public function testContactFailed(): void
    {
        $uri = $this->base_url . '/contact';

        $this->client->request('GET', $uri);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');

        $this->client->loginUser($this->user);
        $crawler = $this->client->request('GET', $uri);

        $form = $crawler->selectButton('contact-send')->form();
        $form['contact[subject]'] = '';
        $form['contact[content]'] = 'test_contact_content';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString(
            'Veuillez saisir un objet',
            $this->client->getResponse()->getContent()
        );
    }
}
