<?php

namespace App\DataFixtures\Category;

use App\Entity\Category;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{    
    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => "catégorie de John",
                'author' => "john@doe.fr",
            ],
            [
                'name' => "catégorie de Jane",
                'author' => "jane@doe.fr",
            ],
            [
                'name' => "catégorie de Bob",
                'author' => "bob@doe.fr",
            ],
            [
                'name' => "catégorie de Patrick",
                'author' => "patrick@doe.fr",
            ]
        ];

        foreach ($categories as $item) {
            $category = new Category();
            $category->setName($item['name']);
            $category->setAuthor($this->getReference($item['author']));
            $category->setCreatedAt(new DateTime());

            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getOrder(): int {
        return 4;
    }
}
