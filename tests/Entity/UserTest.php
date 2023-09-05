<?php

namespace App\Tests\Entity;

use DateTime;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends KernelTestCase
{
    public function testUserEntity(): void
    {
        $encoder = static::getContainer()->get(UserPasswordHasherInterface::class);
        $user = new User();
        $user->setFirstname('john');
        $user->setLastname('doe');
        $user->setEmail('john@doe.fr');
        $user->setPassword($encoder->hashPassword($user, 'azerty'));
        $user->setRegisteredAt(new DateTime('2023/1/1'));
        $user->setBirthday(new DateTime('1994/11/30'));
        $user->setScreenname();

        $this->assertEquals('john', $user->getFirstname());
        $this->assertEquals('doe', $user->getLastname());
        $this->assertEquals('john@doe.fr', $user->getEmail());
        $this->assertEquals(new DateTime('1994/11/30'), $user->getBirthday());
        $this->assertEquals('john D.', $user->getScreenname());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $this->assertInstanceOf(DateTime::class, $user->getRegisteredAt());
        $this->assertTrue($encoder->isPasswordValid($user, 'azerty'), $user->getPassword());
    }
}
