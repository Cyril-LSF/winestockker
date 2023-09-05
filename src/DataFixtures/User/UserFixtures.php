<?php

namespace App\DataFixtures\User;

use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => "john@doe.fr",
                'password' => "azerty",
                'firstname' => "john",
                'lastname' => "doe",
                'birthday' => "1994/11/30",
            ],
            [
                'email' => "jane@doe.fr",
                'password' => "azerty",
                'firstname' => "jane",
                'lastname' => "doe",
                'birthday' => "1995/06/01",
            ],
            [
                'email' => "bob@doe.fr",
                'password' => "azerty",
                'firstname' => "bob",
                'lastname' => "doe",
                'birthday' => "1982/03/22",
            ],
            [
                'email' => "patrick@doe.fr",
                'password' => "azerty",
                'firstname' => "patrick",
                'lastname' => "doe",
                'birthday' => "1990/08/15",
            ]
        ];

        foreach ($users as $item) {
            $user = new User();
            $user->setEmail($item['email']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $item['password']));
            $user->setFirstname($item['firstname']);
            $user->setLastname($item['lastname']);
            $user->setScreenname();
            $user->setRegisteredAt();
            $user->setBirthday(new DateTime($item['birthday']));
            $user->setIsVerified(1);

            $manager->persist($user);

            $this->setReference($item['email'], $user);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 1;
    }
}
