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

# GET /users
final class GetUsersTest extends WebTestCase
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
    
    public function testGetUsersTestAdmin(): void
    {
        $token = $this->authentificationFactory->getToken($this->client, Role::ADMIN);
        $this->client->request(
            'GET',
            '/users',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ]
        );
        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);
        $constraints = new Assert\Collection([
            'id' => [new Assert\Type('int'), new Assert\NotBlank],
            'email' => [new Assert\Type('string'), new Assert\NotBlank],
            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
            'city' => [new Assert\Type('string')],
            'postal_code' => [new Assert\Type('string')],
            'type' => [new Assert\Type('string'), new Assert\NotBlank],
            'status' => [new Assert\Type('string'), new Assert\NotBlank]
        ]);
        $checkStructure = $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints);
        
        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
    }
    
#    public function testGetUsersTestAdminByStatus(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::ADMIN);
#        $this->client->request(
#            'GET',
#            '/users',
#            [
#                'status' => 'Activé'
#            ],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'email' => [new Assert\Type('string'), new Assert\NotBlank],
#            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#            'city' => [new Assert\Type('string')],
#            'postal_code' => [new Assert\Type('string')],
#            'type' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank]
#        ]);
#        $checkStructure = $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#        $this->assertTrue((count(array_unique(array_column($data['data'], 'status'))) == 1 && array_unique(array_column($data['data'], 'status'))[0] == "Activé"), "Le résultat doit être filtrer le statut par 'Actif' ;;;");
#    }
#
#
#    public function testGetUsersTestAdminByType(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::ADMIN);
#        $this->client->request(
#            'GET',
#            '/users',
#            [
#                'type' => 'Modérateur'
#            ],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'email' => [new Assert\Type('string'), new Assert\NotBlank],
#            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#            'city' => [new Assert\Type('string')],
#            'postal_code' => [new Assert\Type('string')],
#            'type' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank]
#        ]);
#        $checkStructure = $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#        $this->assertTrue((count(array_unique(array_column($data['data'], 'type'))) == 1 && array_unique(array_column($data['data'], 'type'))[0] == "Modérateur"), "Le résultat doit être filtrer le type par 'Modérateur' ;;;");
#    }
#    public function testGetUsersTestOwnerNotAllowed(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::OWNER);
#        $this->client->request(
#            'GET',
#            '/users',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Accès interdit, votre habilitation ne vous permet d\'accéder à cette route ou à cette ressource'], $data);
#    }
#    public function testGetUsersTestHelperNotAllowed(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::HELPER);
#        $this->client->request(
#            'GET',
#            '/users',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Accès interdit, votre habilitation ne vous permet d\'accéder à cette route ou à cette ressource'], $data);
#    }
#
#
#    public function testGetUsersTestModerator(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::MODERATOR);
#        $this->client->request(
#            'GET',
#            '/users',
#            [
#                'type' => 'MembreVolontaire'
#            ],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ]
#        );
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#        $constraints = new Assert\Collection([
#            'id' => [new Assert\Type('int'), new Assert\NotBlank],
#            'email' => [new Assert\Type('string'), new Assert\NotBlank],
#            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
#            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
#            'city' => [new Assert\Type('string')],
#            'postal_code' => [new Assert\Type('string')],
#            'type' => [new Assert\Type('string'), new Assert\NotBlank],
#            'status' => [new Assert\Type('string'), new Assert\NotBlank]
#        ]);
#        $checkStructure = $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints);
#
#        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
#        $this->assertTrue((count(array_unique(array_column($data['data'], 'type'))) == 1 && array_unique(array_column($data['data'], 'type'))[0] == "MembreVolontaire"), "Le résultat doit être filtrer le type par 'MembreVolontaire' ;;;");
#        $this->assertTrue((count(array_unique(array_column($data['data'], 'status'))) == 1 && array_unique(array_column($data['data'], 'status'))[0] == "Activé"), "Le résultat doit être filtrer le statut par 'Activé' ;;;");
#    }
}
