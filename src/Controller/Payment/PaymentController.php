<?php

namespace App\Controller\Payment;

use App\Entity\Product;
use App\Service\Payment\Stripe;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private Stripe $stripe;

    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }

    #[Route('/payment/payment/{id}', name: 'payment_payment')]
    public function payment(Product $product)
    {
        return $this->stripe->payment($product, $this->getUser());
    }

    #[Route('/payment/payment_success', name: 'payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/payment_cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/payment/payment_error', name: 'payment_error')]
    public function paymentError(): Response
    {
        return $this->render('payment/error.html.twig');
    }

    #[Route('/webhook', name: 'payment_webhook', methods: ['GET', 'POST'])]
    public function handleWebhook(Request $request)
    {
        return $this->stripe->handle($request);
    }

}
