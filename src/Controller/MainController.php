<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

    #[Route('/', name: 'index')]
    public function main(): RedirectResponse
    {
        return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
    }

}