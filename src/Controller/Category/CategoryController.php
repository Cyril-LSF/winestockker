<?php

namespace App\Controller\Category;

use DateTime;
use App\Entity\Category;
use App\Form\Category\AddBottleType;
use App\Form\Category\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\Bottle\BottleToCategory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category')]
class CategoryController extends AbstractController
{
    private BottleToCategory $bottleToCategory;

    public function __construct(BottleToCategory $bottleToCategory)
    {
        $this->bottleToCategory = $bottleToCategory;
    }
    #[Route('/', name: 'category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findBy(['author' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setAuthor($this->getUser())->setCreatedAt(new DateTime());
            $categoryRepository->save($category, true);

            $this->addFlash('success', "La catégorie a été créée !");
            return $this->redirectToRoute('category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'category_show', methods: ['GET', 'POST'])]
    public function show(Category $category, Request $request, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(AddBottleType::class, $category, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bottleToCategory->categoryToBottle($category, $form->get('bottles')->getData());
            
            $this->addFlash('success', "Les boutteilles de la catégorie " . $category->getName() . " ont été modifiées !");
            $this->redirectToRoute('category_show', ['id' => $category->getId(), RESPONSE::HTTP_SEE_OTHER]);
        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            $this->addFlash('success', "La catégorie a été modifiée !");
            return $this->redirectToRoute('category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'update' => true,
        ]);
    }

    #[Route('/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
            $this->addFlash('success', "La catégorie a été supprimée !");
        }

        return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
    }
}
