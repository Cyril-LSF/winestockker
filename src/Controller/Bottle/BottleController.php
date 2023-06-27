<?php

namespace App\Controller\Bottle;

use App\Entity\Bottle;
use App\Form\Bottle\BottleType;
use App\Repository\BottleRepository;
use App\Service\Bottle\BottleToCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bottle')]
class BottleController extends AbstractController
{
    private BottleToCategory $bottleToCategory;

    public function __construct(BottleToCategory $bottleToCategory)
    {
        $this->bottleToCategory = $bottleToCategory;
    }

    #[Route('/', name: 'bottle_index', methods: ['GET'])]
    public function index(BottleRepository $bottleRepository): Response
    {
        return $this->render('bottle/index.html.twig', [
            'bottles' => $bottleRepository->findBy(['author' => $this->getUser()]),
        ]);
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

            return $this->redirectToRoute('bottle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bottle/new.html.twig', [
            'bottle' => $bottle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'bottle_show', methods: ['GET'])]
    public function show(Bottle $bottle): Response
    {
        return $this->render('bottle/show.html.twig', [
            'bottle' => $bottle,
        ]);
    }

    #[Route('/{id}/edit', name: 'bottle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bottle $bottle, BottleRepository $bottleRepository): Response
    {
        $form = $this->createForm(BottleType::class, $bottle, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$bottleRepository->save($bottle, true);
            $this->bottleToCategory->bottleToCategory($bottle, $form->get('categories')->getData());

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
