<?php

namespace App\Controller\Bottle;

use App\Entity\Bottle;
use App\Entity\Cellar;
use App\Service\Search\Search;
use App\Form\Bottle\BottleType;
use App\Repository\BottleRepository;
use App\Form\Search\FilterBottleType;
use App\Service\Bottle\BottleToCategory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bottle')]
class BottleController extends AbstractController
{
    private BottleToCategory $bottleToCategory;
    private Search $search;

    public function __construct(BottleToCategory $bottleToCategory, Search $search)
    {
        $this->bottleToCategory = $bottleToCategory;
        $this->search = $search;
    }

    #[Route('/', name: 'bottle_index', methods: ['GET', 'POST'])]
    public function index(BottleRepository $bottleRepository, Request $request): Response
    {
        // Filter search form
        $filterForm = $this->createForm(FilterBottleType::class, [], [
            'user' => $this->getUser(),
        ]);
        $filterForm->handleRequest($request);

        $variables = [
            'filterForm' => $filterForm->createView(),
            'bottles' => $bottleRepository->findBy(['author' => $this->getUser()]),
        ];

        // Filter search
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $variables['bottles'] = $this->search->filter($this->getUser(), $filterForm);
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('bottle/index.html.twig', $variables);
    }

    #[Route('/new', name: 'bottle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BottleRepository $bottleRepository): Response
    {
        $bottle = new Bottle();
        $form = $this->createForm(BottleType::class, $bottle, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bottle->setAuthor($this->getUser());
            $this->bottleToCategory->bottleToCategory($bottle, $form->get('categories')->getData());
            //$bottleRepository->save($bottle, true);

            $this->addFlash('success', "La bouteille a été créée !");
            return $this->redirectToRoute('bottle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bottle/new.html.twig', [
            'bottle' => $bottle,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'bottle_show', methods: ['GET'])]
    // public function show(Bottle $bottle): Response
    // {
    //     return $this->render('bottle/show.html.twig', [
    //         'bottle' => $bottle,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'bottle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bottle $bottle, BottleRepository $bottleRepository): Response
    {
        $form = $this->createForm(BottleType::class, $bottle, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bottleToCategory->bottleToCategory($bottle, $form->get('categories')->getData());

            $this->addFlash('success', "La bouteille a été modifiée !");
            return $this->redirectToRoute('bottle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bottle/edit.html.twig', [
            'bottle' => $bottle,
            'form' => $form,
            'update' => true,
        ]);
    }

    #[Route('/{id}', name: 'bottle_delete', methods: ['POST'])]
    public function delete(Request $request, Bottle $bottle, BottleRepository $bottleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bottle->getId(), $request->request->get('_token'))) {
            $bottleRepository->remove($bottle, true);

            $this->addFlash('success', "La bouteille a été supprimée !");
        }

        return $this->redirectToRoute('bottle_index', [], Response::HTTP_SEE_OTHER);
    }

}
