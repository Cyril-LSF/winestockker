<?php

namespace App\Controller\Subscription;

use App\Entity\Subscription;
use App\Form\Subscription\SubscriptionType;
use App\Repository\SubscriptionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'subscription_index', methods: ['GET'])]
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptionRepository->findBy([], ['duration' => 'ASC']),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'subscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SubscriptionRepository $subscriptionRepository): Response
    {
        $subscription = new Subscription();
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subscriptionRepository->save($subscription, true);
            $this->addFlash('success', "L'abonnement à été créé !");

            return $this->redirectToRoute('subscription_index', [], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted()) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->renderForm('subscription/new.html.twig', [
            'subscription' => $subscription,
            'form' => $form,
        ], $response ?? null);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'subscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subscription $subscription, SubscriptionRepository $subscriptionRepository): Response
    {
        $form = $this->createForm(SubscriptionType::class, $subscription);
        
        try {
            $form->handleRequest($request);
        } catch (\Exception $e) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
            $this->addFlash('danger', "Veuillez remplir tous les champs obligatoires");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $subscriptionRepository->save($subscription, true);
            $this->addFlash('success', "L'abonnement a été modifié !");

            return $this->redirectToRoute('subscription_index', [], Response::HTTP_SEE_OTHER);
        }else if ($form->isSubmitted()) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->renderForm('subscription/edit.html.twig', [
            'subscription' => $subscription,
            'form' => $form,
            'update' => true,
        ], $response ?? null);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'subscription_delete', methods: ['POST'])]
    public function delete(Request $request, Subscription $subscription, SubscriptionRepository $subscriptionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subscription->getId(), $request->request->get('_token'))) {
            $subscriptionRepository->remove($subscription, true);
            $this->addFlash('success', "L'abonnement a été supprimé !");
        }

        return $this->redirectToRoute('subscription_index', [], Response::HTTP_SEE_OTHER);
    }
}
