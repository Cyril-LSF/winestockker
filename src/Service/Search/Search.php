<?php

namespace App\Service\Search;

use App\Entity\User;
use App\Entity\Cellar;
use App\Service\Abstract\AbstractService;
use Symfony\Component\Form\FormInterface;

class Search extends AbstractService {

    public function search(User $user, string $data): array
    {
        return $results = [
            'bottles' => $this->bottleRepository->findBySearch($user, $data),
            'cellars' => $this->cellarRepository->findBySearch($user, $data),
            'categories' => $this->categoryRepository->findBySearch($user, $data),
        ];
    }

    public function filter(User $user, array $data, Object $entity = null, bool $admin = false): ?array
    {
        $data = [
            'name' => addslashes(trim($data['name'])) ?? null,
            'origin' => addslashes(trim($data['origin'])) ?? null,
            'vine' => addslashes(trim($data['vine'])) ?? null,
            'enbottler' => addslashes(trim($data['enbottler'])) ?? null,
            'categories' => $data['category'] ?? null,
            'year' => addslashes(trim($data['year'])) ?? null,
            'price' => trim($data['price']) != 0 ? addslashes(trim($data['price'])) : null,
        ];

        return $this->bottleRepository->findByFilter($user, array_filter($data), $entity, $admin);
    }

}