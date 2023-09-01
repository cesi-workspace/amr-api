<?php
namespace App\Tests\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HelpRequestFactory
{
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;

    function __construct(){
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
    }
    function getExistHelpRequest(KernelBrowser $client, int $role)
    {
        $tokenAdmin = $this->authentificationFactory->getToken($client, Role::ADMIN);
        $tokenOwner = $this->authentificationFactory->getToken($client, Role::OWNER);

        $body = [
            'title' => 'Nouvelle HelpRequest '.$this->randomStringFactory->generatePassword(20),
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Espaces verts'
        ];
        
        $client->request(
            'POST',
            '/helprequests',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.($role == Role::ADMIN ? $tokenAdmin : $tokenOwner)
            ],
            json_encode($body, JSON_PRESERVE_ZERO_FRACTION)
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);


        $client->request(
            'GET',
            '/helprequests'
            ,[
                'owner_id' => ($role == Role::ADMIN ? 2 : 4)
            ]
            ,[]
            ,[
                'HTTP_AUTHORIZATION' => 'Bearer '.$tokenAdmin
            ]
        );

        $response2 = $client->getResponse();
        $data2 = json_decode($response2->getContent(), true);

        $body['id'] = $data2['data'][0]['id'];

        return $body;

    }
}
