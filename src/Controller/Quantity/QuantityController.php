<?php

namespace App\Controller\Quantity;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Service\EditQuantity;
use App\Repository\BottleRepository;
use App\Repository\CellarRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/quantity')]
class QuantityController extends AbstractController
{
    private CellarRepository $cellarRepository;
    private BottleRepository $bottleRepository;
    private EditQuantity     $editQuantity;

    public function __construct(CellarRepository $cellarRepository, BottleRepository $bottleRepository, EditQuantity $editQuantity)
    {
        $this->cellarRepository = $cellarRepository;
        $this->bottleRepository = $bottleRepository;
        $this->editQuantity     = $editQuantity;
    }

    #[Route('/edit/{cellar}/{bottle}/{action}', name: 'quantity_edit', methods: ['POST'])]
    public function edit(Cellar $cellar, Bottle $bottle, REquest $request): Response
    {
        $action = $request->attributes->get('action');
        $bottleQuantity = $this->editQuantity->editQuantity($cellar, $bottle, $action);
        return new JsonResponse([
            'status' => true,
            'quantity' => $bottleQuantity,
        ]);
    }
}
