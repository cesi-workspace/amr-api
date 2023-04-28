<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class APIGeo
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client){
        $this->client = $client;
    }

    public function searchCityByCityAndCP(string $city, string $codepostal)
    {
        $response = $this->client->request(
            'GET',
            'https://geo.api.gouv.fr/communes?nom='.$city.'&codePostal='.$codepostal,
            [
                'verify_peer' => false,
            ]
        );
        $content = $response->toArray();
        
        return $content;
    }

    public function searchCityByCoordinates(float $lat, float $lon)
    {
        $response = $this->client->request(
            'GET',
            'https://geo.api.gouv.fr/communes?lat='.$lat.'&lon='.$lon,
            [
                'verify_peer' => false,
            ]
        );
        $content = $response->toArray();
        
        return $content;
    }

}