<?php

namespace App\Controller\Search;

use App\Service\Search\Search;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private Search $search;

    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    #[Route('/search', name: 'search_search', methods: ['POST'])]
    public function search(Request $request): Response
    {
        $data = addslashes(trim(strtolower($request->get('query'))));
        $results = $this->search->search($this->getUser(), $data);
        
        return $this->render('search/index.html.twig', [
            'search' => $data,
            'results' => $results,
        ]);
    }
}
