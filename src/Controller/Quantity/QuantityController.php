<?php

namespace App\Controller\Quantity;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Entity\Quantity;
use App\Form\Quantity\QuantityType;
use App\Service\EditQuantity;
use App\Repository\QuantityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/quantity')]
class QuantityController extends AbstractController
{
    private QuantityRepository $quantityRepository;
    private EditQuantity       $editQuantity;

    public function __construct(QuantityRepository $quantityRepository, EditQuantity $editQuantity)
    {
        $this->quantityRepository = $quantityRepository;
        $this->editQuantity       = $editQuantity;
    }

    #[Route('/edit/{cellar}/{bottle}/{action}', name: 'quantity_edit', methods: ['POST'])]
    public function edit(Cellar $cellar, Bottle $bottle, Request $request): Response
    {
        $action = $request->attributes->get('action');
        $bottleQuantity = $this->editQuantity->editQuantity($cellar, $bottle, $action);
        return new JsonResponse([
            'status' => true,
            'quantity' => $bottleQuantity,
        ]);
    }

    #[IsGranted('QUANTITY_EDIT_BIG', 'quantity')]
    #[Route('/edit/big/{quantity}', name: 'quantity_edit_big', methods: ['GET', 'POST'])]
    public function editBig(Quantity $quantity, Request $request): Response
    {
        $form = $this->createForm(QuantityType::class, $quantity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->quantityRepository->save($quantity, true);

            $this->addFlash('success', "La quantité a été modifiée !");
            return $this->redirectToRoute('cellar_show', ['id' => $quantity->getCellar()->getId()], RESPONSE::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted()) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->render('quantity/editBig.html.twig', [
            'form' => $form->createView(),
            'quantity' => $quantity,
            'cellar' => $quantity->getCellar(),
            'bottle' => $quantity->getBottle(),
        ], $response ?? null);
    }
}
