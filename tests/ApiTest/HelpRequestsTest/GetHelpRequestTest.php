<?php
namespace App\Tests\ApiTest\HelpRequestsTest;
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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

# GET /helprequests/{helprequest_id}
final class GetHelpRequestTest extends WebTestCase 
{
    private KernelBrowser $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private ResponseValidatorService $responseValidatorService;
    private HelpRequestFactory $helpRequestFactory;

    protected function setUp(): void {
        $this->client = static::createClient([
            'CONTENT_TYPE' => 'application/json'
        ]);
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
        $validator = Validation::createValidator();
        $this->responseValidatorService = new ResponseValidatorService($validator);
        $this->helpRequestFactory = new HelpRequestFactory();
    }
    
    public function testGetHelpRequestAdmin(): void
    {
        $token = $this->authentificationFactory->getToken($this->client, Role::ADMIN);
        $helprequest = $this->helpRequestFactory->getExistHelpRequest($this->client, Role::ADMIN);

        
        $this->client->request(
            'GET',
            '/helprequests/'.$helprequest['id']
            ,[]
            ,[]
            ,[
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ]
        );

        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);
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
            'helpers_accept' => []
        ]);

        $checkStructure =  $this->responseValidatorService->getErrorMessagesValidation($data["data"], $constraints);
        
        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
    }
    
#    public function testGetHelpRequestOwnerOk(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::OWNER);
#        $helprequest = $this->helpRequestFactory->getExistHelpRequest($this->client, Role::OWNER);
#
#
#        $this->client->request(
#            'GET',
#            '/helprequests/'.$helprequest['id']
#            ,[]
#            ,[]
#            ,[
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'title' => [new Assert\Type('string'), new Assert\NotBlank],
#            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
#            'finished_date' => [new Assert\Type('string')],
#            'city' => [new Assert\Type('string'), new Assert\NotBlank],
#            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
#            'description' => [new Assert\Type('string'), new Assert\NotBlank],
#            'category' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank],
#            'do' => [new Assert\Type('boolean')],
#            'owner' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ]), new Assert\NotBlank],
#            'helper' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ])],
#            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
#            'helpers_accept' => []
#        ]);
#
#        $checkStructure =  $this->responseValidatorService->getErrorMessagesValidation($data["data"], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#    }
#
#    public function testGetHelpRequestOwnerUnAuthorized(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::OWNER);
#        $helprequest = $this->helpRequestFactory->getExistHelpRequest($this->client, Role::ADMIN);
#
#
#        $this->client->request(
#            'GET',
#            '/helprequests/'.$helprequest['id']
#            ,[]
#            ,[]
#            ,[
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => "Récupération de demande d'aide non associé à l'utilisateur interdite"], $data);
#    }
#
#    public function testGetHelpRequestModerator(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::MODERATOR);
#        $helprequest = $this->helpRequestFactory->getExistHelpRequest($this->client, Role::OWNER);
#
#
#        $this->client->request(
#            'GET',
#            '/helprequests/'.$helprequest['id']
#            ,[]
#            ,[]
#            ,[
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'title' => [new Assert\Type('string'), new Assert\NotBlank],
#            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
#            'finished_date' => [new Assert\Type('string')],
#            'city' => [new Assert\Type('string'), new Assert\NotBlank],
#            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
#            'description' => [new Assert\Type('string'), new Assert\NotBlank],
#            'category' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank],
#            'do' => [new Assert\Type('boolean')],
#            'owner' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ]), new Assert\NotBlank],
#            'helper' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ])],
#            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
#            'helpers_accept' => []
#        ]);
#
#        $checkStructure =  $this->responseValidatorService->getErrorMessagesValidation($data["data"], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#    }
#
#    public function testGetHelpRequestHelper(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::HELPER);
#        $helprequest = $this->helpRequestFactory->getExistHelpRequest($this->client, Role::OWNER);
#
#        $this->client->request(
#            'GET',
#            '/helprequests/'.$helprequest['id']
#            ,[]
#            ,[]
#            ,[
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'title' => [new Assert\Type('string'), new Assert\NotBlank],
#            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
#            'finished_date' => [new Assert\Type('string')],
#            'city' => [new Assert\Type('string'), new Assert\NotBlank],
#            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
#            'description' => [new Assert\Type('string'), new Assert\NotBlank],
#            'category' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank],
#            'do' => [new Assert\Type('boolean')],
#            'owner' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ]), new Assert\NotBlank],
#            'helper' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ])],
#            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
#            'helpers_accept' => []
#        ]);
#
#        $checkStructure =  $this->responseValidatorService->getErrorMessagesValidation($data["data"], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#    }
#
#    public function testGetHelpRequestNoAuth(): void
#    {
#
#        $helprequest = $this->helpRequestFactory->getExistHelpRequest($this->client, Role::OWNER);
#
#
#        $this->client->request(
#            'GET',
#            '/helprequests/'.$helprequest['id']
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'title' => [new Assert\Type('string'), new Assert\NotBlank],
#            'estimated_delay' => [new Assert\Type('string'), new Assert\NotBlank],
#            'finished_date' => [new Assert\Type('string')],
#            'city' => [new Assert\Type('string'), new Assert\NotBlank],
#            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
#            'description' => [new Assert\Type('string'), new Assert\NotBlank],
#            'category' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank],
#            'do' => [new Assert\Type('boolean')],
#            'owner' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ]), new Assert\NotBlank],
#            'helper' => [new Assert\Collection([
#                'id' => [new Assert\Type('int'), new Assert\NotBlank],
#                'email' => [new Assert\Type('string'), new Assert\NotBlank],
#                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#                'city' => [new Assert\Type('string')],
#                'postal_code' => [new Assert\Type('string')],
#                'status' => [new Assert\Type('string'), new Assert\NotBlank],
#                'type' => [new Assert\Type('string'), new Assert\NotBlank]
#            ])],
#            'created_at' => [new Assert\Type('string'), new Assert\NotBlank],
#            'helpers_accept' => []
#        ]);
#
#        $checkStructure =  $this->responseValidatorService->getErrorMessagesValidation($data["data"], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#    }
#    public function testGetHelpRequestsNoExist(): void
#    {
#        $this->client->request(
#            'GET',
#            '/helprequests/0'
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $this->assertEquals(404, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Ressource ou route non trouvée'], $data);
#    }
}
