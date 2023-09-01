<?php
namespace App\Tests\ApiTest\HelpRequestsCategoriesTest;
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

# GET /helprequests/categories
final class GetHelpRequestsCategoriesTest extends WebTestCase
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
    
    public function testGetHelpRequestsCategories(): void
    {
        $this->client->request(
            'GET',
            '/helprequests/categories'
        );
        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);
        $constraints = new Assert\Collection([
            'id' => [new Assert\Type('int'), new Assert\NotBlank],
            'title' => [new Assert\Type('string'), new Assert\NotBlank]
        ]);
        $checkStructure = $this->responseValidatorService->getErrorMessagesValidation($data["data"][0], $constraints);
        
        $this->assertEquals(200, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(0, count($checkStructure), "Le format de données retourné n'est valide : ".json_encode($checkStructure). ";;;".json_encode($data));
    }
}
