<?php

namespace App\Controller\Cellar;

use App\Entity\Cellar;
use App\Form\Cellar\AddBottleType;
use App\Form\Cellar\CellarType;
use App\Form\Search\FilterBottleType;
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
    private CellarRepository   $cellarRepository;
    private BottleToCellar     $bottleToCellar;
    private Search             $search;
    private QuantityRepository $quantityRepository;

    public function __construct(
        CellarRepository $cellarRepository,
        BottleToCellar $bottleToCellar,
        Search $search,
        QuantityRepository $quantityRepository
    ){
        $this->cellarRepository   = $cellarRepository;
        $this->bottleToCellar     = $bottleToCellar;
        $this->search             = $search;
        $this->quantityRepository = $quantityRepository;
    }

    #[Route('/', name: 'cellar_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('cellar/index.html.twig', [
            'cellars' => $this->cellarRepository->findBy(['author' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'cellar_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $cellar = new Cellar();
        $form = $this->createForm(CellarType::class, $cellar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cellar->setCreatedAt(new DateTime());
            $cellar->setAuthor($this->getUser());
            $this->cellarRepository->save($cellar, true);

            $this->addFlash('success', "La cave a été créée !");
            return $this->redirectToRoute('cellar_show', ['id' => $cellar->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cellar/new.html.twig', [
            'cellar' => $cellar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cellar_show', methods: ['GET', 'POST'])]
    public function show(Cellar $cellar, Request $request): Response
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
            'quantities' => $this->quantityRepository->findBy(['cellar' => $cellar]),
            'filterForm' => $filterForm->createView(),
        ];
        
        // Add or remove bottles on cellar
        if ($form->isSubmitted() && $form->isValid()) {
            $this->bottleToCellar->bottleToCellar($cellar, $form->get('bottles')->getData());

            $this->addFlash('success', "Les bouteilles de la cave " . $cellar->getName() . " ont été modifiées !");
            $variables['bottles'] = $cellar->getBottles();
            $variables['quantities'] = $this->quantityRepository->findBy(['cellar' => $cellar]);
        }

        // Filter search
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $variables['bottles'] = $this->search->filter($this->getUser(), $filterForm, $cellar);
            
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('cellar/show.html.twig', $variables);
    }

    #[Route('/{id}/edit', name: 'cellar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cellar $cellar): Response
    {
        $form = $this->createForm(CellarType::class, $cellar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cellarRepository->save($cellar, true);

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
    public function delete(Request $request, Cellar $cellar): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cellar->getId(), $request->request->get('_token'))) {
            $this->cellarRepository->remove($cellar, true);
            $this->addFlash('success', "La cave a été supprimée !");
        }

        return $this->redirectToRoute('cellar_index', [], Response::HTTP_SEE_OTHER);
    }
}
