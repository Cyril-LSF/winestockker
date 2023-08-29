<?php

namespace App\Service\Abstract;

use App\Repository\BottleRepository;
use App\Repository\CellarRepository;
use App\Repository\CategoryRepository;
use App\Repository\QuantityRepository;
use App\Repository\DataCryptRepository;
use App\Repository\CreditCardRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractService {

    protected BottleRepository      $bottleRepository;
    protected CellarRepository      $cellarRepository;
    protected QuantityRepository    $quantityRepository;
    protected CategoryRepository    $categoryRepository;
    protected CreditCardRepository  $creditCardRepository;
    protected ParameterBagInterface $params;
    protected DataCryptRepository   $dataCryptRepository;

    public function __construct(
        BottleRepository      $bottleRepository,
        CellarRepository      $cellarRepository,
        QuantityRepository    $quantityRepository,
        CategoryRepository    $categorryRepository,
        CreditCardRepository  $creditCardRepository,
        ParameterBagInterface $params,
        DataCryptRepository   $dataCryptRepository
    )
    {
        $this->bottleRepository     = $bottleRepository;
        $this->cellarRepository     = $cellarRepository;
        $this->quantityRepository   = $quantityRepository;
        $this->categoryRepository   = $categorryRepository;
        $this->creditCardRepository = $creditCardRepository;
        $this->params               = $params;
        $this->dataCryptRepository  = $dataCryptRepository;
    }

    private function _addOrRemove(){} 
}