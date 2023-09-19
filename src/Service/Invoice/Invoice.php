<?php

namespace App\Service\Invoice;

use App\Entity\Address;
use App\Entity\Subscription;
use \Knp\Snappy\Pdf;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Transaction;
use App\Service\Abstract\AbstractService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Invoice {

    private $knpSnappyPdf;
    private Environment $twig;
    private ParameterBagInterface $params;

    public function __construct(
        \Knp\Snappy\Pdf $knpSnappyPdf,
        Environment $twig,
        ParameterBagInterface $params
    )
    {
        $this->knpSnappyPdf = $knpSnappyPdf;
        $this->twig = $twig;
        $this->params = $params;
    }

    public function generate(User $user, Address $userAddress, Transaction $transaction, Subscription $subscription): string
    {
        $imageData = file_get_contents('./images/logo_ws.png');
        $logo = base64_encode($imageData);

        $invoiceName = 'facture_' . $transaction->getPaymentId() . '.pdf';
        $this->knpSnappyPdf->generateFromHtml(
            $this->twig->render(
                'invoice/invoice.html.twig',
                [
                    'logo' => $logo,
                    'invoice' => $transaction,
                    'user' => $user,
                    'userAddress' => $userAddress,
                    'subscription' => $subscription
                ]
            ),
            $this->params->get('app.invoice_route') . $invoiceName
        );

        return $invoiceName;
    }

}