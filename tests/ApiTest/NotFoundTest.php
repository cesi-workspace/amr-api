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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class NotFoundTest extends WebTestCase
{
    private ?string $api_url = null;
    private KernelBrowser $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private ResponseValidatorService $responseValidatorService;

    protected function setUp(): void {
        $this->client = static::createClient();
    }
    
    public function testGetHelpRequestsCategories(): void
    {
        $this->client->request(
            'GET',
            '/nothing'
        );

        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Ressource ou route non trouvée'], $data);
    }
}
