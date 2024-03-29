<?php
namespace App\Tests\ApiTest\HelpRequestsTest;
use App\Entity\HelpRequest;
use App\Tests\Factory\RandomStringFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Tests\Factory\AuthentificationFactory as AuthentificationFactory;
use App\Tests\Factory\Role;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

# POST /helprequests
final class PostHelpRequestsTest extends WebTestCase
{
    private KernelBrowser $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private EntityManagerInterface $entityManager;
    private EntityRepository $helprequestRepository;

    protected function setUp(): void {        
        $this->client = static::createClient([
            'CONTENT_TYPE' => 'application/json'
        ]);
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->helprequestRepository = $this->entityManager->getRepository(HelpRequest::class);
    }
    
    public function testAddHelpRequestsOwner(): void
    {
        $token = $this->authentificationFactory->getToken($this->client, Role::OWNER);
        $titlehelprequest = 'HelpRequest n°'.$this->randomStringFactory->generatePassword(7);
        $body = [
            'title' => $titlehelprequest,
            'estimated_delay' => '02:00:00',
            'latitude' => 49.0,
            'longitude' => 1.0,
            'description' => 'Description de la helprequest',
            'category' => 'Courses'
        ];
        
        $this->client->request(
            'POST',
            '/helprequests',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            json_encode($body, JSON_PRESERVE_ZERO_FRACTION)
        );
        
        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);
        
        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Demande créée'], $data);
        $this->assertEquals(1, $this->helprequestRepository->count(['title' => $titlehelprequest]), "La demande d'aide n'a visiblement pas été ajoutée en base");

    }
#    public function testAddHelpRequestsNoAuth(): void
#    {
#        $titlehelprequest = 'HelpRequest n°'.$this->randomStringFactory->generatePassword(7);
#        $body = [
#            'title' => $titlehelprequest,
#            'estimated_delay' => '02:00:00',
#            'latitude' => 49.0,
#            'longitude' => 1.0,
#            'description' => 'Description de la helprequest',
#            'category' => 'Courses'
#        ];
#
#        $this->client->request(
#            'POST',
#            '/helprequests',
#            [],
#            [],
#            [],
#            json_encode($body)
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Accès interdit, il faut être connecté pour accéder à cette route ou à cette ressource'], $data);
#        $this->assertEquals(0, $this->helprequestRepository->count(['title' => $titlehelprequest]), "La demande d'aide a visiblement été ajoutée en base");
#
#    }
#    public function testAddHelpRequestsHelper(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::HELPER);
#        $titlehelprequest = 'HelpRequest n°'.$this->randomStringFactory->generatePassword(7);
#        $body = [
#            'title' => $titlehelprequest,
#            'estimated_delay' => '02:00:00',
#            'latitude' => 49.0,
#            'longitude' => 1.0,
#            'description' => 'Description de la helprequest',
#            'category' => 'Courses'
#        ];
#
#        $this->client->request(
#            'POST',
#            '/helprequests',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode($body, JSON_PRESERVE_ZERO_FRACTION)
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);
#        $this->assertEquals(0, $this->helprequestRepository->count(['title' => $titlehelprequest]), "La demande d'aide a visiblement été ajoutée en base");
#    }
#    public function testAddHelpRequestsModerator(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::MODERATOR);
#        $titlehelprequest = 'HelpRequest n°'.$this->randomStringFactory->generatePassword(7);
#        $body = [
#            'title' => $titlehelprequest,
#            'estimated_delay' => '02:00:00',
#            'latitude' => 49.0,
#            'longitude' => 1.0,
#            'description' => 'Description de la helprequest',
#            'category' => 'Courses'
#        ];
#
#        $this->client->request(
#            'POST',
#            '/helprequests',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode($body, JSON_PRESERVE_ZERO_FRACTION)
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);
#        $this->assertEquals(0, $this->helprequestRepository->count(['title' => $titlehelprequest]), "La demande d'aide a visiblement été ajoutée en base");
#
#    }
#    public function testAddHelpRequestsAdmin(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::ADMIN);
#        $titlehelprequest = 'HelpRequest n°'.$this->randomStringFactory->generatePassword(7);
#        $body = [
#            'title' => $titlehelprequest,
#            'estimated_delay' => '02:00:00',
#            'latitude' => 49.0,
#            'longitude' => 1.0,
#            'description' => 'Description de la helprequest',
#            'category' => 'Courses'
#        ];
#
#        $this->client->request(
#            'POST',
#            '/helprequests',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode($body, JSON_PRESERVE_ZERO_FRACTION)
#        );
#
#        $response = $this->client->getResponse();
#
#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Demande créée'], $data);
#        $this->assertEquals(1, $this->helprequestRepository->count(['title' => $titlehelprequest]), "La demande d'aide n'a visiblement pas été ajoutée en base");
#    }
#
}
