<?php

namespace App\Tests\Entity;

use DateTime;
use App\Entity\User;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    public function testCategoryEntity(): void
    {
        $category = new Category();
        $category->setName('test category');
        $category->setCreatedAt(new DateTime('2023/01/01'));
        $category->setAuthor(new User());

        $this->assertEquals('test category', $category->getName());
        $this->assertEquals(new DateTime('2023/01/01'), $category->getCreatedAt());

        $this->assertInstanceOf(User::class, $category->getAuthor());
    }
}
