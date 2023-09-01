<?php
namespace App\Tests\ApiTest\UsersTest;
use App\Entity\HelpRequest;
use App\Service\ResponseValidatorService;
use App\Tests\Factory\RandomStringFactory;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Tests\Factory\AuthentificationFactory as AuthentificationFactory;
use App\Tests\Factory\Role;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

# POST /users
final class PostUsersTest extends WebTestCase
{
    private ?string $api_url = null;
    private KernelBrowser $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private ResponseValidatorService $responseValidatorService;

    protected function setUp(): void {
        $this->client = static::createClient();
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
        $validator = Validation::createValidator();
        $this->responseValidatorService = new ResponseValidatorService($validator);
    }
    
    public function testPostUsersNoAuth(): void
    {
        $this->client->request(
            'POST',
            '/users',
            [],
            [],
            [],
            json_encode([
                    'email' => 'whois'.rand(1, 5000).'@amrapicesi.org',
                    'password' => '1#Aa'.$this->randomStringFactory->generatePassword(10),
                    'firstname' => 'Whois',
                    'surname' => 'Test',
                    'postal_code' => '76000',
                    'city' => 'Rouen',
                    'type' => 'MembreVolontaire'
                    ])
        );
        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Utilisateur crée avec succès'], $data);
    }
#    public function testPostUsersNoAuthNotAllowed(): void
#    {
#        $this->client->request(
#            'POST',
#            '/users',
#            [],
#            [],
#            [],
#            json_encode([
#                    'email' => 'whois'.rand(1, 5000).'@amrapicesi.org',
#                    'password' => '1#Aa'.$this->randomStringFactory->generatePassword(10),
#                    'firstname' => 'Whois',
#                    'surname' => 'Test',
#                    'postal_code' => '76000',
#                    'city' => 'Rouen',
#                    'type' => 'Administrateur'
#                    ])
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $this->assertEquals(401, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Création d\'utilisateur non MembreMr et non MembreVolontaire interdite'], $data);
#    }
#
#    public function testPostUsersOwnerNotAllowed(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::OWNER);
#        $this->client->request(
#            'POST',
#            '/users',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode([
#                    'email' => 'whois'.rand(1, 5000).'@amrapicesi.org',
#                    'password' => '1#Aa'.$this->randomStringFactory->generatePassword(10),
#                    'firstname' => 'Whois',
#                    'surname' => 'Test',
#                    'postal_code' => '76000',
#                    'city' => 'Rouen',
#                    'type' => 'Administrateur'
#                    ])
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Création d\'utilisateur non MembreMr et non MembreVolontaire interdite'], $data);
#    }
#    public function testPostUsersSuperAdmin(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::SUPERADMIN);
#        $this->client->request(
#            'POST',
#            '/users',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode([
#                    'email' => 'whois'.rand(1, 5000).'@amrapicesi.org',
#                    'password' => '1#Aa'.$this->randomStringFactory->generatePassword(10),
#                    'firstname' => 'Whois',
#                    'surname' => 'Test',
#                    'postal_code' => '76000',
#                    'city' => 'Rouen',
#                    'type' => 'Administrateur'
#                    ])
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Utilisateur crée avec succès'], $data);
#    }
}
