<?php

namespace App\Controller\Cellar;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Form\Cellar\AddBottleType;
use App\Form\Cellar\CellarType;
use App\Form\Quantity\QuantityType;
use App\Repository\BottleRepository;
use App\Repository\CellarRepository;
use App\Repository\QuantityRepository;
use App\Service\Bottle\BottleToCellar;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cellar')]
class CellarController extends AbstractController
{
    private BottleToCellar $bottleToCellar;

    public function __construct(BottleToCellar $bottleToCellar)
    {
        $this->bottleToCellar = $bottleToCellar;
    }

    #[Route('/', name: 'cellar_index', methods: ['GET'])]
    public function index(CellarRepository $cellarRepository): Response
    {
        return $this->render('cellar/index.html.twig', [
            'cellars' => $cellarRepository->findBy(['author' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'cellar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CellarRepository $cellarRepository): Response
    {
        $cellar = new Cellar();
        $form = $this->createForm(CellarType::class, $cellar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cellar->setCreatedAt(new DateTime());
            $cellar->setAuthor($this->getUser());
            $cellarRepository->save($cellar, true);

            return $this->redirectToRoute('cellar_show', ['id' => $cellar->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cellar/new.html.twig', [
            'cellar' => $cellar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cellar_show', methods: ['GET', 'POST'])]
    public function show(Cellar $cellar, Request $request, CellarRepository $cellarRepository, BottleRepository $bottleRepository, QuantityRepository $quantityRepository): Response
    {

        $form = $this->createForm(AddBottleType::class, $cellar, [
            'user' => $this->getUser(),
            'cellar' => $cellar,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $add = $this->bottleToCellar->bottleToCellar($cellar, $form->get('bottles')->getData());

            $message = "La/les bouteilles ont été ajoutées à la cave";
            if ($add == false) {
                $message = "La/les bouteilles ont été retirées la cave";
            }
            $this->addFlash('success', $message);
            $this->redirectToRoute('cellar_show', ['id' => $cellar->getId(), RESPONSE::HTTP_SEE_OTHER]);
        }
        
        return $this->render('cellar/show.html.twig', [
            'cellar' => $cellar,
            'form' => $form->createView(),
            'quantities' => $quantityRepository->findBy(['cellar' => $cellar]),
        ]);
    }

    #[Route('/{id}/edit', name: 'cellar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cellar $cellar, CellarRepository $cellarRepository): Response
    {
        $form = $this->createForm(CellarType::class, $cellar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cellarRepository->save($cellar, true);

            $this->addFlash('success', "La cave a été modifiée !");
            return $this->redirectToRoute('cellar_show', ['id' => $cellar->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cellar/edit.html.twig', [
            'cellar' => $cellar,
            'form' => $form,
            'update' => true,
        ]);
    }

    #[Route('/{id}', name: 'cellar_delete', methods: ['POST'])]
    public function delete(Request $request, Cellar $cellar, CellarRepository $cellarRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cellar->getId(), $request->request->get('_token'))) {
            $cellarRepository->remove($cellar, true);
            $this->addFlash('success', "La cave a été supprimée !");
        }

        return $this->redirectToRoute('cellar_index', [], Response::HTTP_SEE_OTHER);
    }
}
