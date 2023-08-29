<?php
namespace App\Tests\UnitTest\HelpRequestsTest;
use App\Entity\HelpRequest;
use App\Tests\Factory\RandomStringFactory;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Tests\Factory\AuthentificationFactory as AuthentificationFactory;
use App\Tests\Factory\Role;

# POST /helprequests
final class PostHelpRequestsTest extends TestCase
{
    private ?string $api_url = null;
    private HttpClientInterface $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;

    protected function setUp(): void {
        $this->api_url = $_ENV["API_URL"];
        $this->client = HttpClient::create();
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
    }
    
    public function testAddHelpRequestsOwner(): void
    {
        $token = $this->authentificationFactory->getToken(Role::OWNER);

        $body = [
            'title' => 'HelpRequest n°1',
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Courses'
        ];
        
        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Demande créée'], $data);

    }
    public function testAddHelpRequestsNoAuth(): void
    {
        $body = [
            'title' => 'HelpRequest n°1',
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Courses'
        ];
        
        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Accès interdit, il faut être connecté pour accéder à cette route ou à cette ressource'], $data);

    }
    public function testAddHelpRequestsHelper(): void
    {
        $token = $this->authentificationFactory->getToken(Role::HELPER);
        $body = [
            'title' => 'HelpRequest n°1',
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Courses'
        ];
        
        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);

    }
    public function testAddHelpRequestsModerator(): void
    {
        $token = $this->authentificationFactory->getToken(Role::MODERATOR);
        $body = [
            'title' => 'HelpRequest n°1',
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Courses'
        ];
        
        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);

    }
    public function testAddHelpRequestsAdmin(): void
    {
        $token = $this->authentificationFactory->getToken(Role::ADMIN);
        $body = [
            'title' => 'HelpRequest n°1',
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Courses'
        ];
        
        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Demande créée'], $data);

    }
    
}
