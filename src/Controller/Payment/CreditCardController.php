<?php

namespace App\Controller\Payment;

use App\Entity\CreditCard;
use App\Form\Payment\CreditCardType;
use App\Repository\CreditCardRepository;
use App\Repository\DataCryptRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/credit_card')]
class CreditCardController extends AbstractController
{
    private CreditCardRepository $creditCardRepository;

    public function __construct(CreditCardRepository $creditCardRepository)
    {
        $this->creditCardRepository = $creditCardRepository;
    }

    // #[Route('/', name: 'credit_card_index', methods: ['GET'])]
    // public function index(CreditCardRepository $creditCardRepository): Response
    // {
    //     return $this->render('credit_card/index.html.twig', [
    //         'credit_cards' => $creditCardRepository->findAll(),
    //     ]);
    // }

    #[Route('/new', name: 'credit_card_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $creditCard = new CreditCard();
        $form = $this->createForm(CreditCardType::class, $creditCard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $creditCard->setAuthor($this->getUser());
            $this->creditCardRepository->save($creditCard, true);

            return $this->redirectToRoute('credit_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('credit_card/new.html.twig', [
            'credit_card' => $creditCard,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'credit_card_show', methods: ['GET'])]
    // public function show(CreditCard $creditCard): Response
    // {
    //     return $this->render('credit_card/show.html.twig', [
    //         'credit_card' => $creditCard,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'credit_card_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, CreditCard $creditCard): Response
    // {
    //     $form = $this->createForm(CreditCardType::class, $creditCard);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->creditCardRepository->save($creditCard, true);

    //         return $this->redirectToRoute('credit_card_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('credit_card/edit.html.twig', [
    //         'creditCard' => $creditCard,
    //         'creditCardForm' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'credit_card_delete', methods: ['POST'])]
    public function delete(Request $request, CreditCard $creditCard, DataCryptRepository $dataCryptRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$creditCard->getId(), $request->request->get('_token'))) {
            $dataCrypt = $dataCryptRepository->findOneBy(['creditCard' => $creditCard]);
            $dataCryptRepository->remove($dataCrypt, true);
            $this->creditCardRepository->remove($creditCard, true);
            $this->addFlash('success', "La carte a été supprimée !");
        }

        return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/selected', name: 'credit_card_selected', methods: ['POST'])]
    public function selected(CreditCard $creditCard): Response
    {
        if ($creditCard->isSelected()) {
            $creditCard->setSelected(false);
        } else {
            $userCreditCards = $this->creditCardRepository->findBy(['author' => $this->getUser(), 'selected' => true]);
            foreach ($userCreditCards as $userAddress) {
                $userAddress->setSelected(false);
                $this->creditCardRepository->save($userAddress, true);
            }
            $creditCard->setSelected(true);
        }
        $this->creditCardRepository->save($creditCard, true);
        return new JsonResponse([
            'status' => true,
            'isSelected' => $creditCard->isSelected(),
        ]);
    }
}
