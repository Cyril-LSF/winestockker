<?php

namespace App\Service\Bottle;

use App\Entity\Bottle;
use App\Entity\Category;
use App\Service\Abstract\AbstractService;

class BottleToCategory extends AbstractService {

    public function bottleToCategory(Bottle $bottle, $data): void
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
            $categoriesBottleId = [];
            foreach ($categoriesBottle as $category) {
                $categoriesBottleId[] = $category->getId();
            }
            // GET CATEGORIES FORM
            $categoriesFormId = [];
            foreach ($data as $formCategory) {
                $categoriesFormId[] = $formCategory->getId();
            }
            // ADD OR REMOVE CATEGORIES
            if ($categories = array_diff($categoriesFormId, $categoriesBottleId)) {
                $this->_addOrRemove(true, $categories, $bottle);
            }
            if ($categories = array_diff($categoriesBottleId, $categoriesFormId)) {
                $this->_addOrRemove(false, $categories, $bottle);
            }
            $this->bottleRepository->save($bottle, true);
        }
    }

    public function categoryToBottle(Category $category, $data): void
    {
        foreach ($data as $formBottle) {
            $bottle = $this->bottleRepository->findOneBy(['id' => $formBottle->getId()]);
            $bottle->addCategory($category);
            $this->bottleRepository->save($bottle, true);
        }
        $this->categoryRepository->save($category, true);
    }

    private function _addOrRemove(bool $add, array $categories, Bottle $bottle): void
    {
        foreach ($categories as $category) {
            $newCategory = $this->categoryRepository->findOneBy(['id' => $category]);
            $add === true ? $newCategory->addBottle($bottle) : $newCategory->removeBottle($bottle);
            $this->categoryRepository->save($newCategory, true);
        }
    }
}