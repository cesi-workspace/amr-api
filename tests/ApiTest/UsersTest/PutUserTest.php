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

# PUT /users/{user_id}
final class PutUserTest extends WebTestCase
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
    
    public function testPutUserNoAuth(): void
    {
        $this->client->request(
            'PUT',
            '/users/1',
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
        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Accès interdit, il faut être connecté pour accéder à cette route ou à cette ressource'], $data);
    }
    
#    public function testPutUserOk(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::HELPER);
#        $this->client->request(
#            'PUT',
#            '/users/5',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode([
#                    'firstname' => 'Whois',
#                    'surname' => 'Test',
#                    'postal_code' => '76000',
#                    'city' => 'Rouen'
#                    ])
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Données de l\'utilisateur modifiée avec succès'], $data);
#    }
}
