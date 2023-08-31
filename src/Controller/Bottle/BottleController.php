<?php

namespace App\Controller\Bottle;

use App\Entity\Bottle;
use App\Service\Search\Search;
use App\Form\Bottle\BottleType;
use App\Service\Premium\Premium;
use App\Repository\BottleRepository;
use App\Form\Search\FilterBottleType;
use App\Service\Bottle\BottleToCategory;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bottle')]
class BottleController extends AbstractController
{
    private BottleRepository $bottleRepository;
    private BottleToCategory $bottleToCategory;
    private Search           $search;
    private PaginatorInterface $paginator;
    private Premium          $premium;

    public function __construct(
        BottleRepository $bottleRepository,
        BottleToCategory $bottleToCategory,
        Search           $search,
        PaginatorInterface $paginator, 
        Premium          $premium
    ){
        $this->bottleRepository = $bottleRepository;
        $this->bottleToCategory = $bottleToCategory;
        $this->search           = $search;
        $this->paginator        = $paginator;
        $this->premium          = $premium;
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
            'bottles' => $this->paginator->paginate($this->premium->restriction($this->getUser()), $request->query->getInt('page', 1), 6),
        ];

        // Filter search
        if ($request->query->has('filter_bottle')) {
            $filter = $request->query->all()['filter_bottle'];
            $variables['bottles'] = $this->paginator->paginate(
                $this->premium->restriction($this->getUser(), true, $this->search->filter($this->getUser(), $filter)),
                $request->query->getInt('page', 1), 6
            );
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('bottle/index.html.twig', $variables);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'bottle_index_admin', methods: ['GET', 'POST'])]
    public function indexAdmin(Request $request): Response
    {
        // Filter search form
        $filterForm = $this->createForm(FilterBottleType::class, [], [
            'user'  => $this->getUser(),
            'admin' => true,
        ]);
        $filterForm->handleRequest($request);

        $variables = [
            'filterForm' => $filterForm->createView(),
            'bottles'    => $this->paginator->paginate($this->bottleRepository->findAll(), $request->query->getInt('page', 1), 6),
            'admin'      => true,
        ];

        // Filter search
        if ($request->query->has('filter_bottle')) {
            $filter = $request->query->all()['filter_bottle'];
            $variables['bottles'] = $this->paginator->paginate(
                $this->premium->restriction($this->getUser(), true, $this->search->filter($this->getUser(), $filter, null, true)),
                $request->query->getInt('page', 1), 6
            );
            $this->addFlash('success', "Les bouteilles ont été filtrées !");
        }

        return $this->render('bottle/index.html.twig', $variables);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'bottle_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if (!$this->premium->is_premium($this->getUser(), 'bottle')) {
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
        $admin = $request->get('admin');
        $form = $this->createForm(BottleType::class, $bottle, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bottleToCategory->bottleToCategory($bottle, $form->get('categories')->getData());

            $this->addFlash('success', "La bouteille a été modifiée !");
            return $this->redirectToRoute($admin ? 'bottle_index_admin' : 'bottle_index', [], Response::HTTP_SEE_OTHER);
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
        $admin = $request->get('admin');
        if ($this->isCsrfTokenValid('delete'.$bottle->getId(), $request->request->get('_token'))) {
            $this->bottleRepository->remove($bottle, true);

            $this->addFlash('success', "La bouteille a été supprimée !");
        }

        return $this->redirectToRoute($admin ? 'bottle_index_admin' : 'bottle_index', [], Response::HTTP_SEE_OTHER);
    }

}
