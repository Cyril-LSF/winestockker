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

    public function filter(User $user, FormInterface $data, Object $entity = null): ?array
    {
        $data = [
            'name' => addslashes(trim($data->getData()['name'])),
            'origin' => addslashes(trim($data->getData()['origin'])),
            'vine' => addslashes(trim($data->getData()['vine'])),
            'enbottler' => addslashes(trim($data->getData()['enbottler'])),
            'categories' => $data->getData()['category'] ?? null ,
            'year' => addslashes(trim($data->getData()['year'])),
            'price' => trim($data->getData()['price']) != 0 ? addslashes(trim($data->getData()['price'])) : null,
        ];

        return $this->bottleRepository->findByFilter($user, array_filter($data), $entity);
    }

}