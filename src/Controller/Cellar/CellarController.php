<?php

namespace App\Controller\Cellar;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Form\Cellar\AddBottleType;
use App\Form\Cellar\CellarType;
use App\Form\Quantity\QuantityType;
use App\Form\Search\FilterBottleType;
use App\Repository\BottleRepository;
use App\Repository\CellarRepository;
use App\Repository\QuantityRepository;
use App\Service\Bottle\BottleToCellar;
use App\Service\Search\Search;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cellar')]
class CellarController extends AbstractController
{
    private BottleToCellar $bottleToCellar;
    private Search $search;

    public function __construct(BottleToCellar $bottleToCellar, Search $search)
    {
        $this->bottleToCellar = $bottleToCellar;
        $this->search = $search;
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

            $this->addFlash('success', "La cave a été créée !");
            return $this->redirectToRoute('cellar_show', ['id' => $cellar->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cellar/new.html.twig', [
            'cellar' => $cellar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cellar_show', methods: ['GET', 'POST'])]
    public function show(Cellar $cellar, Request $request, QuantityRepository $quantityRepository): Response
    {
        // Add or remove bottles form
        $form = $this->createForm(AddBottleType::class, $cellar, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);
        
        // Filter search form
        $filterForm = $this->createForm(FilterBottleType::class, [], [
            'user' => $this->getUser(),
            'category' => true,
        ]);
        $filterForm->handleRequest($request);

        $variables = [
            'cellar' => $cellar,
            'bottles' => $cellar->getBottles(),
            'form' => $form->createView(),
            'quantities' => $quantityRepository->findBy(['cellar' => $cellar]),
            'filterForm' => $filterForm->createView(),
        ];
        
        // Add or remove bottles on cellar
        if ($form->isSubmitted() && $form->isValid()) {
            $this->bottleToCellar->bottleToCellar($cellar, $form->get('bottles')->getData());

            $this->addFlash('success', "Les bouteilles de la cave " . $cellar->getName() . " ont été modifiées !");
            $this->redirectToRoute('cellar_show', ['id' => $cellar->getId(), RESPONSE::HTTP_SEE_OTHER]);
        }

        // Filter search
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $variables['bottles'] = $this->search->filter($this->getUser(), $filterForm, $cellar);
            
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('cellar/show.html.twig', $variables);
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

    #[Route('/{id}/delete', name: 'cellar_delete', methods: ['POST'])]
    public function delete(Request $request, Cellar $cellar, CellarRepository $cellarRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cellar->getId(), $request->request->get('_token'))) {
            $cellarRepository->remove($cellar, true);
            $this->addFlash('success', "La cave a été supprimée !");
        }

        return $this->redirectToRoute('cellar_index', [], Response::HTTP_SEE_OTHER);
    }
}
