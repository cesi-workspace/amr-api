<?php
namespace App\Tests\Factory;
use PHPUnit\Framework\TestCase;
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
    function getExistHelpRequest(int $role)
    {
        $tokenAdmin = $this->authentificationFactory->getToken(Role::ADMIN);
        $tokenOwner = $this->authentificationFactory->getToken(Role::OWNER);

        $client = HttpClient::create();

        $body = [
            'title' => 'Nouvelle HelpRequest '.$this->randomStringFactory->generatePassword(20),
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Espaces verts'
        ];
        
        $response = $client->request(
            'POST',
            $_ENV['API_URL'].'/helprequests',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.($role == Role::ADMIN ? $tokenAdmin : $tokenOwner)],
                'json' => $body
            ]
        );

        $client=HttpClient::create();

        $response2 = $client->request(
            'GET',
            $_ENV['API_URL'].'/helprequests',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.$tokenAdmin],
                'query' => [
                    'owner_id' => ($role == Role::ADMIN ? 2 : 4)
                ]
            ]
        );


        $data = json_decode($response2->getContent(false), true);

        $body['id'] = $data['data'][0]['id'];

        return $body;

    }
}
