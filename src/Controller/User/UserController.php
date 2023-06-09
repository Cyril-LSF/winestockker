<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Entity\User;
use App\Form\Address\AddressType;
use App\Form\Security\EditPasswordType;
use App\Form\User\RegistrationFormType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use App\Service\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    private ParameterBagInterface $params;
    private UserRepository        $userRepository;
    private AddressRepository     $addressRepository;

    public function __construct(
        ParameterBagInterface $params,
        UserRepository        $userRepository,
        AddressRepository     $addressRepository,
    ){
        $this->params            = $params;
        $this->userRepository    = $userRepository;
        $this->addressRepository = $addressRepository;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'user_show', methods: ['GET', 'POST'])]
    public function show(User $user, Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setauthor($this->getUser());
            $this->addressRepository->save($address, true);

            $this->addFlash('success', "L'adresse a été créée !");
        } else if ($form->isSubmitted()) {
            $this->addFlash('danger', "Erreur de saisie");
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'addresses' => $this->addressRepository->findBy(['author' => $this->getUser()]),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UploadedFile $uploadedFile): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setScreenname();
            if ($form->get('picture')->getData()) {
                $filename = $uploadedFile->upload($form->get('picture')->getData());
                $user->setPicture($filename);
            }
            $this->userRepository->save($user, true);

            return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'registrationForm' => $form->createView(),
            'update' => true,
            'fileRoot' => $this->params->get('app.uploaded_root'),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}/edit_password', name: 'user_edit_password', methods: ['GET', 'POST'])]
    public function edit_password(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $form = $this->createForm(EditPasswordType::class, null, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $form->get('password')->getData()));
            $this->userRepository->save($user, true);
            $this->addFlash('success', "Votre mot de passe a été mis à jour");
            return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId(), Response::HTTP_SEE_OTHER]);
        }
        return $this->render('security/edit_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user, true);
            $this->container->get('security.token_storage')->setToken(null);
        }

        return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
    }
}
