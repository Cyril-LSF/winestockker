<?php

namespace App\Controller\Contact;

use App\Form\Contact\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContactController extends AbstractController
{
    private ParameterBagInterface $params;
    private MailerInterface       $mailer;

    public function __construct(ParameterBagInterface $params, MailerInterface $mailer)
    {
        $this->params = $params;
        $this->mailer = $mailer;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->from(new Address($this->getUser()->getEmail(), $this->getUser()->getScreenname()))
                ->to($this->params->get('app.wine_stoccker_email'))
                ->subject($form->get('subject')->getData())
                ->htmlTemplate('contact/email.html.twig')
                ->context([
                    'content' => $form->get('content')->getData(),
                ]);
            $this->mailer->send($email);
            $this->addFlash('success', "Votre message a bien été envoyé !");

            return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted()) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ], $response ?? null);
    }
}