<?php

namespace App\Service\Bottle;

use App\Entity\Bottle;
use App\Entity\Category;
use App\Repository\BottleRepository;
use App\Repository\CategoryRepository;

class BottleToCategory {

    private BottleRepository $bottleRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(BottleRepository $bottleRepository, CategoryRepository $categoryRepository)
    {
        $this->bottleRepository = $bottleRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function bottleToCategory(Bottle $bottle, $data)
    {
        if (!$bottle->getId()) {
            $this->bottleRepository->save($bottle, true);
            foreach ($data as $formCategory) {
                $category = $this->categoryRepository->findOneBy(['id' => $formCategory->getId()]);
                $category->addBottle($bottle);
                $this->categoryRepository->save($category, true);
            }
        } else {
            // GET CATEGORIES BOTTLE
            $categoriesBottle = $this->categoryRepository->getCategoriesBottle($bottle);
            dd($categoriesBottle);
        }
    }

    public function categoryToBottle(Category $category, $data)
    {
        foreach ($data as $formBottle) {
            $bottle = $this->bottleRepository->findOneBy(['id' => $formBottle->getId()]);
            $bottle->addCategory($category);
            $this->bottleRepository->save($bottle, true);
        }
        $this->categoryRepository->save($category, true);
    }
}