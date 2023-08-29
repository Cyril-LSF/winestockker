<?php

namespace App\Controller\Category;

use DateTime;
use App\Entity\Category;
use App\Service\Search\Search;
use App\Service\Premium\Premium;
use App\Form\Category\CategoryType;
use App\Form\Category\AddBottleType;
use App\Form\Search\FilterBottleType;
use App\Repository\CategoryRepository;
use App\Service\Bottle\BottleToCategory;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category')]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private BottleToCategory   $bottleToCategory;
    private Search             $search;
    private PaginatorInterface $paginator;

    public function __construct(
        CategoryRepository $categoryRepository,
        BottleToCategory   $bottleToCategory,
        Search             $search,
        PaginatorInterface $paginator
    ){
        $this->categoryRepository = $categoryRepository;
        $this->bottleToCategory   = $bottleToCategory;
        $this->search             = $search;
        $this->paginator          = $paginator;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'category_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $this->paginator->paginate($this->categoryRepository->findBy(['author' => $this->getUser()]), $request->query->getInt('page', 1), 6),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'category_index_admin', methods: ['GET'])]
    public function indexAdmin(Request $request): Response
    {
        return $this->render('category/index.html.twig', [
            // 'categories' => $this->categoryRepository->findAll(),
            'categories' => $this->paginator->paginate($this->categoryRepository->findAll(), $request->query->getInt('page', 1), 6),
            'admin'      => true,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Premium $premium): Response
    {
        if (!$premium->is_premium($this->getUser(), 'category')) {
            $this->addFlash('warning', "Vous devez être membre premium pour réaliser cette action !");
            return $this->redirectToRoute('subscription_index', [], Response::HTTP_SEE_OTHER);
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setAuthor($this->getUser())->setCreatedAt(new DateTime());
            $this->categoryRepository->save($category, true);

            $this->addFlash('success', "La catégorie a été créée !");
            return $this->redirectToRoute('category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[IsGranted('CATEGORY_VIEW', 'category')]
    #[Route('/{id}', name: 'category_show', methods: ['GET', 'POST'])]
    public function show(Category $category, Request $request): Response
    {
        // Add or remove bottles form
        $form = $this->createForm(AddBottleType::class, $category, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        // Filter search form
        $filterForm = $this->createForm(FilterBottleType::class, [], [
            'user' => $this->getUser(),
        ]);
        $filterForm->handleRequest($request);

        $variables = [
            'category' => $category,
            'bottles' => $this->paginator->paginate($category->getBottles(), $request->query->getInt('page', 1), 6),
            'form' => $form->createView(),
            'filterForm' => $filterForm->createView(),
        ];

        // Add or remove bottles on category
        if ($form->isSubmitted() && $form->isValid()) {
            $this->bottleToCategory->categoryToBottle($category, $form->get('bottles')->getData());
            
            $this->addFlash('success', "Les bouteilles de la catégorie " . $category->getName() . " ont été modifiées !");
            return $this->redirectToRoute('category_show', ['id' => $category->getId(), RESPONSE::HTTP_SEE_OTHER]);
        }

        // Filter search
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $variables['bottles'] = $this->paginator->paginate($this->search->filter($this->getUser(), $filterForm, $category), $request->query->getInt('page', 1), 6);
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('category/show.html.twig', $variables);
    }

    #[IsGranted('CATEGORY_EDIT', 'category')]
    #[Route('/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $admin = $request->get('admin');
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($category, true);

            $this->addFlash('success', "La catégorie a été modifiée !");
            return $this->redirectToRoute($admin ? 'category_index_admin' : 'category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'update' => true,
        ]);
    }

    #[IsGranted('CATEGORY_DELETE', 'category')]
    #[Route('/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        $admin = $request->get('admin');
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->categoryRepository->remove($category, true);
            $this->addFlash('success', "La catégorie a été supprimée !");
        }

        return $this->redirectToRoute($admin ? 'category_index_admin' : 'category_index', [], Response::HTTP_SEE_OTHER);
    }
}
