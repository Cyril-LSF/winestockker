<?php

namespace App\Controller\Bottle;

use App\Entity\Bottle;
use App\Service\Search\Search;
use App\Form\Bottle\BottleType;
use App\Repository\BottleRepository;
use App\Form\Search\FilterBottleType;
use App\Service\Bottle\BottleToCategory;
use App\Service\Premium\Premium;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bottle')]
class BottleController extends AbstractController
{
    private BottleRepository $bottleRepository;
    private BottleToCategory $bottleToCategory;
    private Search           $search;

    public function __construct(
        BottleRepository $bottleRepository,
        BottleToCategory $bottleToCategory,
        Search           $search
    ){
        $this->bottleRepository = $bottleRepository;
        $this->bottleToCategory = $bottleToCategory;
        $this->search           = $search;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'bottle_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        // Filter search form
        $filterForm = $this->createForm(FilterBottleType::class, [], [
            'user' => $this->getUser(),
        ]);
        $filterForm->handleRequest($request);

        $variables = [
            'filterForm' => $filterForm->createView(),
            'bottles' => $this->bottleRepository->findBy(['author' => $this->getUser()]),
        ];

        // Filter search
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $variables['bottles'] = $this->search->filter($this->getUser(), $filterForm);
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('bottle/index.html.twig', $variables);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'bottle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Premium $premium): Response
    {
        if (!$premium->is_premium($this->getUser(), 'bottle')) {
            $this->addFlash('warning', "Vous devez être membre premium pour réaliser cette action !");
            return $this->redirectToRoute('subscription_index', [], Response::HTTP_SEE_OTHER);
        }

        $bottle = new Bottle();
        $form = $this->createForm(BottleType::class, $bottle, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bottle->setAuthor($this->getUser());
            $this->bottleToCategory->bottleToCategory($bottle, $form->get('categories')->getData());

            $this->addFlash('success', "La bouteille a été créée !");
            return $this->redirectToRoute('bottle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bottle/new.html.twig', [
            'bottle' => $bottle,
            'form' => $form,
        ]);
    }

    #[IsGranted('BOTTLE_EDIT', 'bottle')]
    #[Route('/{id}/edit', name: 'bottle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bottle $bottle): Response
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

    #[IsGranted('BOTTLE_DELETE', 'bottle')]
    #[Route('/{id}', name: 'bottle_delete', methods: ['POST'])]
    public function delete(Request $request, Bottle $bottle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bottle->getId(), $request->request->get('_token'))) {
            $this->bottleRepository->remove($bottle, true);

            $this->addFlash('success', "La bouteille a été supprimée !");
        }

        return $this->redirectToRoute('bottle_index', [], Response::HTTP_SEE_OTHER);
    }

}
