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

    public function filter(User $user, Cellar $cellar, FormInterface $data): ?array
    {
        $data = [
            'origin' => addslashes(trim($data->get('origin')->getData())),
            'vine' => addslashes(trim($data->get('vine')->getData())),
            'enbottler' => addslashes(trim($data->get('enbottler')->getData())),
            'categories' => $data->get('category')->getData(),
            'year' => addslashes(trim($data->get('year')->getData())),
            'price' => trim($data->get('price')->getData()) != 0 ? addslashes(trim($data->get('price')->getData())) : null,
        ];
        //dd($data);
        return $this->bottleRepository->findByFilter($user, $cellar, array_filter($data));
    }

}