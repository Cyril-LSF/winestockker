<?php

namespace App\Service\Invoice;

use App\Entity\Address;
use App\Entity\Subscription;
use \Knp\Snappy\Pdf;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Transaction;
use App\Service\Abstract\AbstractService;

class Invoice extends AbstractService {

    private $knpSnappyPdf;
    private Environment $twig;

    public function __construct(\Knp\Snappy\Pdf $knpSnappyPdf, Environment $twig)
    {
        $this->knpSnappyPdf = $knpSnappyPdf;
        $this->twig = $twig;
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
            "./invoices/$invoiceName"
        );

        return $invoiceName;
    }

}