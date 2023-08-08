<?php

namespace App\Service\Payment;

use App\Entity\User;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Stripe {

    private ParameterBagInterface $params;
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository        $userRepository;
    private ProductRepository     $productRepository;
    private TransactionRepository $transactionRepository;

    public function __construct(
        ParameterBagInterface $params,
        UrlGeneratorInterface $urlGenerator,
        UserRepository        $userRepository,
        ProductRepository     $productRepository,
        TransactionRepository $transactionRepository
    )
    {
        $this->params         = $params;
        $this->urlGenerator   = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->transactionRepository = $transactionRepository;
        \Stripe\Stripe::setApiKey($this->params->get('app.stripe_private_key'));
    }

    public function payment(Product $product, User $user): RedirectResponse
    {
        $productStripe = [
            'price_data' => [
                'currency' => "EUR",
                'unit_amount' => $product->getPrice(),
                'product_data' => [
                    'name' => $product->getName(),
                ],
            ],
            'quantity' => 1,
        ];

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $productStripe
            ],
            'mode' => 'payment',
            'success_url' => $this->params->get('app.base_url') . $this->urlGenerator->generate('payment_success', referenceType: UrlGeneratorInterface::ABSOLUTE_PATH),
            'cancel_url' => $this->params->get('app.base_url') . $this->urlGenerator->generate(name: 'payment_cancel', referenceType: UrlGeneratorInterface::ABSOLUTE_PATH),
            'payment_intent_data' => [
                'metadata' => [
                    'product_id' => $product->getId(),
                    'quantity' => 1,
                    'user_id' => $user->getId()
                ]
            ]
        ]);

        return new RedirectResponse($checkout_session->url);
    }

    public function handle(Request $request): Response
    {
        $endpoint_secret = $this->params->get('app.stripe_private_webhook');
        $payload = $request->getContent();
        $signature = $request->headers->get('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $signature, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $user = $this->userRepository->findOneBy(['id' => $paymentIntent->metadata->user_id]);
                $product = $this->productRepository->findOneBy(['id' => $paymentIntent->metadata->product_id]);
                $this->transactionRepository->create(
                    $paymentIntent,
                    $user,
                    $product
                );
                $this->userRepository->upgradePremium($user, $product->getDuration());
                break;
            default:
                break;
        }

        return new Response('Webhook Handled', 200);
    }

}