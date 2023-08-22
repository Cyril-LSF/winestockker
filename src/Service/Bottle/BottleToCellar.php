<?php

namespace App\Service\Bottle;

use App\Entity\Cellar;
use App\Entity\Quantity;
use App\Service\Abstract\AbstractService;

class BottleToCellar extends AbstractService {

    public function bottleToCellar(Cellar $cellar, $data): void
    {
        // GET CELLAR BOTTLES
        $cellarBottles = $this->bottleRepository->getCellarBotlles($cellar);
        $cellarBottlesId = [];
        foreach ($cellarBottles as $bottle) {
            $cellarBottlesId[] = $bottle->getId();
        }
        //GET NEW BOTTLES FROM FORM
        $newBottlesId = [];
        foreach ($data as $bottles) {
            $newBottlesId[] = $bottles->getId();
        }
        // ADD OR REMOVE BOTTLES IN CELLAR
        if ($bottles = array_diff($newBottlesId, $cellarBottlesId)) {
            $this->_addOrRemove(true, $bottles, $cellar);
        }
        if ($bottles = array_diff($cellarBottlesId, $newBottlesId)) {
            $this->_addOrRemove(false, $bottles, $cellar);
        }
        $this->cellarRepository->save($cellar, true);
    }

    private function _addOrRemove(bool $add, array $bottles, Cellar $cellar): void
    {
        $bottles = $this->bottleRepository->findBy(['id' => $bottles]);
        foreach ($bottles as $bottle) {
            $add === true ? $bottle->addCellar($cellar) : $bottle->removeCellar($cellar);
            $this->bottleRepository->save($bottle, true);
            if ($add === true) {
                $quantity = new Quantity();
                $quantity->setCellar($cellar)->setBottle($bottle)->setQuantity('0');
                $this->quantityRepository->save($quantity, true);
            } else {
                $quantity = $this->quantityRepository->findOneBy(['cellar' => $cellar, 'bottle' => $bottle]);
                $this->quantityRepository->remove($quantity, true);
            }
        }
    }
}