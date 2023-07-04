<?php

namespace App\Service\Search;

use App\Entity\User;
use App\Service\Abstract\AbstractService;

class Search extends AbstractService {

    public function search(User $user, string $data): array
    {
        return $results = [
            'bottles' => $this->bottleRepository->findBySearch($user, $data),
            'cellars' => $this->cellarRepository->findBySearch($user, $data),
            'categories' => $this->categoryRepository->findBySearch($user, $data),
        ];
    }

}