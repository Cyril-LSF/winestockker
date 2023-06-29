<?php

namespace App\Service\Abstract;

use App\Repository\BottleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CellarRepository;
use App\Repository\QuantityRepository;

abstract class AbstractService {

    protected BottleRepository   $bottleRepository;
    protected CellarRepository   $cellarRepository;
    protected QuantityRepository $quantityRepository;
    protected CategoryRepository $categoryRepository;

    public function __construct(
        BottleRepository   $bottleRepository,
        CellarRepository   $cellarRepository,
        QuantityRepository $quantityRepository,
        CategoryRepository $categorryRepository
    )
    {
        $this->bottleRepository   = $bottleRepository;
        $this->cellarRepository   = $cellarRepository;
        $this->quantityRepository = $quantityRepository;
        $this->categoryRepository = $categorryRepository;
    }

    private function _addOrRemove(){} 
}