<?php
namespace App\Tests\UnitTest\HelpRequestsCategoriesTest;
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

# POST /helprequests/categories : Créer une nouvelle catégorie de demande
final class PostHelpRequestsCategoriesTest extends TestCase
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

    public function testAddHelpRequestsCategoriesAdmin(): void
    {
        $token = $this->authentificationFactory->getToken(Role::ADMIN);
        $random_string = $this->randomStringFactory->generatePassword(20);

        $body = [
            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
        ];

        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests/categories',
            [
                'verify_peer' => false,
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Catégorie de demandes d\'aide ajoutée'], $data);
    }
    public function testAddHelpRequestsCategoriesHelper(): void
    {
        $token = $this->authentificationFactory->getToken(Role::HELPER);
        $random_string = $this->randomStringFactory->generatePassword(20);

        $body = [
            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
        ];

        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests/categories',
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
    public function testAddHelpRequestsCategoriesOwner(): void
    {
        $token = $this->authentificationFactory->getToken(Role::OWNER);
        $random_string = $this->randomStringFactory->generatePassword(20);

        $body = [
            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
        ];

        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests/categories',
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
    public function testAddHelpRequestsCategoriesModerator(): void
    {
        $token = $this->authentificationFactory->getToken(Role::MODERATOR);
        $random_string = $this->randomStringFactory->generatePassword(20);

        $body = [
            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
        ];

        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests/categories',
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
    public function testAddHelpRequestsCategoriesNoAuth(): void
    {
        $random_string = $this->randomStringFactory->generatePassword(20);

        $body = [
            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
        ];

        $response = $this->client->request(
            'POST',
            $this->api_url.'/helprequests/categories',
            [
                'verify_peer' => false,
                'json' => $body
            ]
        );

        $data = json_decode($response->getContent(false), true);
        
        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Accès interdit, il faut être connecté pour accéder à cette route ou à cette ressource'], $data);
    }
}
