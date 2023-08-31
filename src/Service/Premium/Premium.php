<?php

namespace App\Service\Premium;

use App\Entity\User;
use App\Service\Abstract\AbstractService;

class Premium extends AbstractService {

    public function is_premium(User $user, string $entity): bool
    {
        if (!$user->isPremium()) {
            switch ($entity) {
                case 'cellar':
                    if (count($this->cellarRepository->findBy(['author' => $user])) >= 1) {
                        return false;
                    }
                    break;
                case 'category':
                    if (count($this->categoryRepository->findBy(['author' => $user])) >= 1) {
                        return false;
                    }
                    break;
                case 'bottle';
                    if (count($this->bottleRepository->findBy(['author' => $user])) >= 5) {
                        return false;
                    }
                    break;
                default:
                    break;
            }
        }
        return true;
    }

    public function restriction(User $user, bool $search = false, mixed $array = [], string $entity = null): mixed
    {
        if (!$user->isPremium()) {
            switch ($entity) {
                case 'cellar':
                case 'category':
                    $repo = $entity . 'Repository';
                    $limit = 1;
                    break;
                default:
                    $repo = 'bottleRepository';
                    $limit = 5;
                    break;
            }
            // GET FREE ELEMENTS IDS
            $elements = $this->$repo->findBy(['author' => $user], [], $limit);        
            $freeElementsIds = [];
            foreach ($elements as $element) {
                $freeElementsIds[] = $element->getId();
            }
            // GET ALL ELEMENTS AND DISABLED THIS IF NOT IN FREE ELEMENTS IDS ARRAY
            $elements = !$search && empty($array) ? $this->$repo->findBy(['author' => $user]) : $array;
            foreach ($elements as $element) {
                if (!in_array($element->getId(), $freeElementsIds)) {
                    $element->setDisabled(true);
                }
            }
        } else {
            switch ($entity) {
                case 'cellar':
                    $elements = $this->cellarRepository->findBy(['author' => $user]);
                    break;
                case 'category':
                    $elements = $this->categoryRepository->findBy(['author' => $user]);
                    break;
                default:
                $elements = !$search&& empty($array) ? $this->bottleRepository->findBy(['author' => $user]) : $array;
                    break;
            }
        }
        
        return $elements;
    }

}