<?php

namespace App\Service;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Entity\Quantity;
use App\Repository\QuantityRepository;

class EditQuantity {

    private QuantityRepository $quantityRepository;

    public function __construct(QuantityRepository $quantityRepository)
    {
        $this->quantityRepository = $quantityRepository;
    }

    public function editQuantity(Cellar $cellar, Bottle $bottle, string $action) {
        $quantity = $this->quantityRepository->findOneBy(['cellar' => $cellar, 'bottle' => $bottle]);
        if ($quantity) {
            switch ($action) {
                case 'less':
                    if ($quantity->getQuantity() == 0) {
                        continue;
                    }
                    $quantity->setQuantity(intval($quantity->getQuantity()) - 1);
                    break;
                case 'add':
                    $quantity->setQuantity(intval($quantity->getQuantity()) + 1);
                    break;
                default:
                    break;
            }
            $this->quantityRepository->save($quantity, true);
        } else {
            $quantity = new Quantity();
            $quantity->setCellar($cellar);
            $quantity->setBottle($bottle);
            $quantity->setQuantity('1');

            $this->quantityRepository->save($quantity, true);
        }
        return $quantity->getQuantity();
    }

}