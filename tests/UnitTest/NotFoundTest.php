<?php
namespace App\Tests\UnitTest;
use App\Entity\HelpRequest;
use App\Service\ResponseValidatorService;
use App\Tests\Factory\RandomStringFactory;
use DateTime;
use PHPUnit\Framework\TestCase;
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

final class NotFoundTest extends TestCase
{
    private ?string $api_url = null;
    private HttpClientInterface $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private ResponseValidatorService $responseValidatorService;

    protected function setUp(): void {
        $this->api_url = $_ENV["API_URL"];
        $this->client = HttpClient::create();
    }
    
    public function testGetHelpRequestsCategories(): void
    {
        $response = $this->client->request(
            'GET',
            $this->api_url.'/nothing',
            [
                'verify_peer' => false
            ]
        );

        $data = json_decode($response->getContent(false), true);

        $this->assertEquals(404, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Ressource ou route non trouvée'], $data);
    }
}
