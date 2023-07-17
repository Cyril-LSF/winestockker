<?php

namespace App\Service\Search;

use App\Service\Abstract\AbstractService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WikiApi extends AbstractService {

    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function searchVine(string $vine)
    {
        $data = str_replace([' ', 'À', 'Á', 'Â', 'à', 'Ä', 'Å', 'à', 'á', 'â', 'à', 'ä', 'å', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'È', 'É', 'Ê', 'Ë', 'è', 'é', 'ê', 'ë', 'Ç', 'ç', 'Ì', 'Í', 'Î', 'Ï'],
        ['%20', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a' ,'a', 'a' ,'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o' ,'o', 'o', 'o', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'c', 'c', 'i' ,'i', 'i', 'i', 'i', 'i', 'i', 'i'],
        $vine);

        $base_api_url = $this->params->get('app.api_wiki');

        $params = [
            '?action=query',
            'format=json',
            'prop=extracts',
            'exintro=true',
            'explaintext=true',
            "titles=$data",
        ];

        $request = $base_api_url . join('&', $params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL , $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result, true);
        $pageId = array_keys($result['query']['pages'])[0];

        if ($pageId != -1) {
            $result = [
                'title' => $vine,
                'content' => $result['query']['pages'][$pageId]['extract'],
                'url' => 'https://fr.wikipedia.org/wiki/' . $data,
            ];
            return $result;
        } else {
            return false;
        }
        
    }

}