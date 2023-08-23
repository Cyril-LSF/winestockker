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

}