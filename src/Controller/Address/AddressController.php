<?php

namespace App\Controller\Address;

use App\Entity\Address;
use App\Form\Address\AddressType;
use App\Repository\AddressRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/address')]
class AddressController extends AbstractController
{
    private AddressRepository $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    // #[Route('/', name: 'address_index', methods: ['GET'])]
    // public function index(): Response
    // {
    //     return $this->render('address/index.html.twig', [
    //         'addresses' => $this->addressRepository->findAll(),
    //     ]);
    // }

    #[IsGranted('ADDRESS_SELECTED', 'address')]
    #[Route('/{id}/selected', name: 'address_selected', methods: ['POST'])]
    public function selected(Address $address): Response
    {
        if ($address->isSelected()) {
            $address->setSelected(false);
        } else {
            $userAddresses = $this->addressRepository->findBy(['author' => $this->getUser(), 'selected' => true]);
            foreach ($userAddresses as $userAddress) {
                $userAddress->setSelected(false);
                $this->addressRepository->save($userAddress, true);
            }
            $address->setSelected(true);
        }
        $this->addressRepository->save($address, true);
        return new JsonResponse([
            'status' => true,
            'isSelected' => $address->isSelected(),
        ]);
    }

    #[IsGranted('ADDRESS_EDIT', 'address')]
    #[Route('/{id}/edit', name: 'address_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Address $address): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        
        try {
            $form->handleRequest($request);
        } catch (\Exception $e) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
            $this->addFlash('danger', "Veuillez remplir tous les champs obligatoires");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressRepository->save($address, true);

            $this->addFlash('success', "L'adresse a été modifiée !");
            return $this->redirectToRoute('user_show', ['id' => $address->getauthor()->getId()], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted()) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ], $response ?? null);
    }

    #[IsGranted('ADDRESS_DELETE', 'address')]
    #[Route('/{id}', name: 'address_delete', methods: ['POST'])]
    public function delete(Request $request, Address $address): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $this->addressRepository->remove($address, true);
        }

        return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
