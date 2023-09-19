<?php

namespace App\Controller\Search;

use App\Entity\Bottle;
use App\Service\Search\Search;
use App\Service\Search\WikiApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private Search $search;
    private WikiApi $wikiApi;

    public function __construct(Search $search, WikiApi $wikiApi)
    {
        $this->search  = $search;
        $this->wikiApi = $wikiApi;
    }

    #[IsGranted('ROLE_USER')]
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

    #[IsGranted('ROLE_USER')]
    #[Route('/search/{id}/vine', name: 'search_vine', methods: ['GET'])]
    public function searchVine(Bottle $bottle): Response
    {
        $result = $this->wikiApi->searchVine($bottle->getVine());

        return $this->render('search/wiki.html.twig', [
            'data' => $result,
        ]);
    }
}
