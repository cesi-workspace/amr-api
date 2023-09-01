<?php
namespace App\Tests\ApiTest\HelpRequestsCategoriesTest;
use App\Entity\HelpRequestCategory;
use App\Repository\HelpRequestCategoryRepository;
use App\Tests\Factory\RandomStringFactory;
use DateTime;
use Doctrine\ORM\EntityManager;
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
use App\Repository\UserRepository;

# POST /helprequests/categories : Créer une nouvelle catégorie de demande
final class PostHelpRequestsCategoriesTest extends WebTestCase
{
    private ?string $api_url = null;
    private KernelBrowser $client;
    private AuthentificationFactory $authentificationFactory;
    private RandomStringFactory $randomStringFactory;
    private EntityManagerInterface $entityManager;
    private EntityRepository $helprequestcategoryRepository;

    protected function setUp(): void {
        $this->client = static::createClient([
            'CONTENT_TYPE' => 'application/json'
        ]);
        $this->authentificationFactory = new AuthentificationFactory();
        $this->randomStringFactory = new RandomStringFactory();
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->helprequestcategoryRepository = $this->entityManager->getRepository(HelpRequestCategory::class);
    }

    public function testAddHelpRequestsCategoriesAdmin(): void
    {

        $token = $this->authentificationFactory->getToken($this->client, Role::ADMIN);
        $random_string = $this->randomStringFactory->generatePassword(20);

        $body = [
            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
        ];

        $this->client->request(
            'POST',
            '/helprequests/categories',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            json_encode($body)
        );

        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true);
        
        $this->assertEquals(201, $response->getStatusCode(), json_encode($data));
        $this->assertEquals(['message' => 'Catégorie de demandes d\'aide ajoutée'], $data);
        $this->assertEquals(1, $this->helprequestcategoryRepository->count(['title' => 'Nouvelle catégorie de demande d\'aide '.$random_string]), "La catégorie n'a visiblement pas été ajoutée en base");

        
    }
#    public function testAddHelpRequestsCategoriesHelper(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::HELPER);
#        $random_string = $this->randomStringFactory->generatePassword(20);

#        $body = [
#            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
#        ];

#        $this->client->request(
#            'POST',
#            '/helprequests/categories',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode($body)
#        );

#        $response = $this->client->getResponse();

#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);
#        $this->assertEquals(0, $this->helprequestcategoryRepository->count(['title' => 'Nouvelle catégorie de demande d\'aide '.$random_string]), "La catégorie a visiblement été ajoutée en base");
#    }
#    public function testAddHelpRequestsCategoriesOwner(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::OWNER);
#        $random_string = $this->randomStringFactory->generatePassword(20);

#        $body = [
#            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
#        ];

#        $this->client->request(
#            'POST',
#            '/helprequests/categories',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode($body)
#        );

#        $response = $this->client->getResponse();

#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);
#        $this->assertEquals(0, $this->helprequestcategoryRepository->count(['title' => 'Nouvelle catégorie de demande d\'aide '.$random_string]), "La catégorie a visiblement été ajoutée en base");
#    }
#    public function testAddHelpRequestsCategoriesModerator(): void
#    {
#        $token = $this->authentificationFactory->getToken($this->client, Role::MODERATOR);
#        $random_string = $this->randomStringFactory->generatePassword(20);

#        $body = [
#            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
#        ];

#        $this->client->request(
#            'POST',
#            '/helprequests/categories',
#            [],
#            [],
#            [
#                'HTTP_AUTHORIZATION' => 'Bearer '.$token
#            ],
#            json_encode($body)
#        );

#        $response = $this->client->getResponse();

#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => "Accès interdit, votre habilitation ne vous permet d'accéder à cette route ou à cette ressource"], $data);
#        $this->assertEquals(0, $this->helprequestcategoryRepository->count(['title' => 'Nouvelle catégorie de demande d\'aide '.$random_string]), "La catégorie a visiblement été ajoutée en base");
#    }
#    public function testAddHelpRequestsCategoriesNoAuth(): void
#    {
#        $random_string = $this->randomStringFactory->generatePassword(20);

#        $body = [
#            'title' => 'Nouvelle catégorie de demande d\'aide '.$random_string,
#        ];
#        $this->client->request(
#            'POST',
#            '/helprequests/categories',
#            [],
#            [],
#            [],
#            json_encode($body)
#        );
#        $response = $this->client->getResponse();

#        $data = json_decode($response->getContent(), true);
#
#        $this->assertEquals(403, $response->getStatusCode(), json_encode($data));
#        $this->assertEquals(['message' => 'Accès interdit, il faut être connecté pour accéder à cette route ou à cette ressource'], $data);
#        $this->assertEquals(0, $this->helprequestcategoryRepository->count(['title' => 'Nouvelle catégorie de demande d\'aide '.$random_string]), "La catégorie a visiblement été ajoutée en base");
#    }
}
