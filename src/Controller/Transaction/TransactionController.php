<?php

namespace App\Controller\Transaction;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/transactions')]
class TransactionController extends AbstractController
{
    private TransactionRepository $transactionRepository;
    private ParameterBagInterface $params;

    public function __construct(TransactionRepository $transactionRepository, ParameterBagInterface $params)
    {
        $this->transactionRepository = $transactionRepository;
        $this->params                = $params;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'transaction_index')]
    public function index(): Response
    {
        return $this->render('transaction/index.html.twig', [
            'transactions' => $this->transactionRepository->findBy(['user' => $this->getUser()], ['id' => 'DESC']),
        ]);
    }

    #[IsGranted('TRANSACTION_DOWNLOAD', 'transaction')]
    #[Route('/download/{id}', name: 'transaction_download')]
    public function download(Transaction $transaction): BinaryFileResponse
    {
        $filePath = $this->params->get('app.invoice_root') . $transaction->getInvoice();
        return $this->file( $filePath);
    }
}
