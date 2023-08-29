<?php
namespace App\Tests\UnitTest\HelpRequestsTest;
use App\Entity\HelpRequest;
use App\Service\ResponseValidatorService;
use App\Tests\Factory\HelpRequestFactory;
use App\Tests\Factory\RandomStringFactory;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Tests\Factory\AuthentificationFactory as AuthentificationFactory;
use App\Tests\Factory\Role;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

# GET /helprequests
final class GetHelpRequestsTest extends TestCase 
{
    private ?string $api_url = null;
    private HttpClientInterface $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private ResponseValidatorService $responseValidatorService;

    protected function setUp(): void {
        $this->api_url = $_ENV["API_URL"];
        $this->client = HttpClient::create();
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
        $validator = Validation::createValidator();
        $this->responseValidatorService = new ResponseValidatorService($validator);
    }
    
    public function testGetHelpRequest(): void
    {

        $response = $this->client->request(
            'GET',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false
            ]
        );

        $data = json_decode($response->getContent(false), true);
        $constraints = new Assert\Collection([
            'id' => [new Assert\Type('int'), new Assert\NotBlank],
            'title' => [new Assert\Type('string'), new Assert\NotBlank],
            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
            'finished_date' => [new Assert\Type('string')],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
            'description' => [new Assert\Type('string'), new Assert\NotBlank],
            'category' => [new Assert\Type('string'), new Assert\NotBlank],
            'status' => [new Assert\Type('string'), new Assert\NotBlank],
            'do' => [new Assert\Type('boolean')],
            'owner' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ]), new Assert\NotBlank],
            'helper' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ])],
            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
            'nb_helpers_accept' => [new Assert\Type('int'), new Assert\NotBlank]
        ]);

        $checkStructure =  $data != null && array_key_exists('data', $data) ? $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints) : [];
        
        $this->assertContains($response->getStatusCode(), [200, 204], json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
        
    }
    
    public function testGetHelpRequestByCategory(): void
    {

        $response = $this->client->request(
            'GET',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'query' => [
                    'category' => 'Courses'
                ]
            ]
        );

        $data = json_decode($response->getContent(false), true);
        $constraints = new Assert\Collection([
            'id' => [new Assert\Type('int'), new Assert\NotBlank],
            'title' => [new Assert\Type('string'), new Assert\NotBlank],
            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
            'finished_date' => [new Assert\Type('string')],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
            'description' => [new Assert\Type('string'), new Assert\NotBlank],
            'category' => [new Assert\Type('string'), new Assert\NotBlank],
            'status' => [new Assert\Type('string'), new Assert\NotBlank],
            'do' => [new Assert\Type('boolean')],
            'owner' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ]), new Assert\NotBlank],
            'helper' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ])],
            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
            'nb_helpers_accept' => [new Assert\Type('int'), new Assert\NotBlank]
        ]);

        $checkStructure =  $data != null && array_key_exists('data', $data) ? $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints) : [];
        
        $this->assertContains($response->getStatusCode(), [200, 204], json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
        $this->assertTrue($response->getStatusCode() == 204 || (count(array_unique(array_column($data['data'], 'category'))) == 1 && array_unique(array_column($data['data'], 'category'))[0] == "Courses"), "Le résultat doit être filtrer la catégorie par 'Courses' ;;;");
    }
    
    public function testGetHelpRequestByGeoAndCategory(): void
    {

        $response = $this->client->request(
            'GET',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'query' => [
                    'latitude' => 48.0,
                    'longitude' => 1.0,
                    'range' => 100,
                    'category' => 'Courses'
                ]
            ]
        );

        $data = json_decode($response->getContent(false), true);
        $constraints = new Assert\Collection([
            'id' => [new Assert\Type('int'), new Assert\NotBlank],
            'title' => [new Assert\Type('string'), new Assert\NotBlank],
            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
            'finished_date' => [new Assert\Type('string')],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
            'description' => [new Assert\Type('string'), new Assert\NotBlank],
            'category' => [new Assert\Type('string'), new Assert\NotBlank],
            'status' => [new Assert\Type('string'), new Assert\NotBlank],
            'do' => [new Assert\Type('boolean')],
            'owner' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ]), new Assert\NotBlank],
            'helper' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ])],
            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
            'nb_helpers_accept' => [new Assert\Type('int'), new Assert\NotBlank]
        ]);

        $checkStructure =  $data != null && array_key_exists('data', $data) ? $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints) : [];
        
        $this->assertContains($response->getStatusCode(), [200, 204], json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
        $this->assertTrue($response->getStatusCode() == 204 || (count(array_unique(array_column($data['data'], 'category'))) == 1 && array_unique(array_column($data['data'], 'category'))[0] == "Courses"), "Le résultat doit être filtrer la catégorie par 'Courses'");
    }
    
    public function testGetHelpRequestTooFar(): void
    {

        $response = $this->client->request(
            'GET',
            $this->api_url.'/helprequests',
            [
                'verify_peer' => false,
                'query' => [
                    'latitude' => 48.0,
                    'longitude' => 48.0,
                    'range' => 100
                ]
            ]
        );

        $data = json_decode($response->getContent(false), true);

        $this->assertEquals(204, $response->getStatusCode(), json_encode($data));
        
    }
    
    public function testGetHelpRequestAdmin(): void
    {
        $token = $this->authentificationFactory->getToken(Role::ADMIN);

        $response = $this->client->request(
            'GET',
            $this->api_url.'/helprequests',
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'verify_peer' => false,
                'query' => [
                    'latitude' => 48.0,
                    'longitude' => 48.0,
                    'range' => 100,
                    'owner_id' => 4,
                    'status' => 'Créée'
                ]
            ]
        );

        $data = json_decode($response->getContent(false), true);
        $constraints = new Assert\Collection([
            'id' => [new Assert\Type('int'), new Assert\NotBlank],
            'title' => [new Assert\Type('string'), new Assert\NotBlank],
            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
            'finished_date' => [new Assert\Type('string')],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
            'description' => [new Assert\Type('string'), new Assert\NotBlank],
            'category' => [new Assert\Type('string'), new Assert\NotBlank],
            'status' => [new Assert\Type('string'), new Assert\NotBlank],
            'do' => [new Assert\Type('boolean')],
            'owner' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ]), new Assert\NotBlank],
            'helper' => [new Assert\Collection([
                'id' => [new Assert\Type('int'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string')],
                'postal_code' => [new Assert\Type('string')],
                'status' => [new Assert\Type('string'), new Assert\NotBlank],
                'type' => [new Assert\Type('string'), new Assert\NotBlank]
            ])],
            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
            'nb_helpers_accept' => [new Assert\Type('int'), new Assert\NotBlank]
        ]);

        $checkStructure =  $data != null && array_key_exists('data', $data) ? $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints) : [];
        
        $this->assertContains($response->getStatusCode(), [200, 204], json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
        
    }
}
