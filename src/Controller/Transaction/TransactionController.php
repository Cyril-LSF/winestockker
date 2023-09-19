<?php

namespace App\Controller\Transaction;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/transactions')]
class TransactionController extends AbstractController
{
    private TransactionRepository $transactionRepository;
    private ParameterBagInterface $params;
    private PaginatorInterface $paginator;

    public function __construct(
        TransactionRepository $transactionRepository,
        ParameterBagInterface $params,
        PaginatorInterface $paginator
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->params                = $params;
        $this->paginator             = $paginator;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'transaction_index')]
    public function index(Request $request): Response
    {
        return $this->render('transaction/index.html.twig', [
            'transactions' => $this->paginator->paginate(
                $this->transactionRepository->findBy(['user' => $this->getUser()], ['id' => 'DESC']),
                $request->query->getInt('page', 1), 6
            ),
        ]);
    }

    #[IsGranted('TRANSACTION_DOWNLOAD', 'transaction')]
    #[Route('/download/{id}', name: 'transaction_download')]
    public function download(Transaction $transaction): BinaryFileResponse
    {
        $filePath = $this->params->get('app.invoice_route') . $transaction->getInvoice();
        return $this->file( $filePath);
    }
}
