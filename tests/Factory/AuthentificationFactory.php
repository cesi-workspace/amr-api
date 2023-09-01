<?php
namespace App\Tests\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Role {
    const SUPERADMIN = 0;
    const ADMIN = 1;
    const MODERATOR = 2;
    const OWNER = 3;
    const HELPER = 4;
    const PARTNER = 5;
    const GOV = 6;
}

class AuthentificationFactory
{
    public function getToken(KernelBrowser $client, int $role) : string
    {
        $email = "";
        $auth = "";
        switch ($role){
            case Role::SUPERADMIN:
                $email = "superadmin@amr.org";
                $auth = "Ioj7d*8-{m}";
                break;
            case Role::ADMIN:
                $email = "admin@amr.org";
                $auth = "-4d°kfz%md";
                break;
            case Role::MODERATOR:
                $email = "moderator@amr.org";
                $auth = "çù(p7Z*0Z";
                break;
            case Role::OWNER:
                $email = "owner@amr.org";
                $auth = "4j7#!:Ge_";
                break;
            case Role::HELPER:
                $email = "helper@amr.org";
                $auth = "iI00|è+/SQ";
                break;
            case Role::PARTNER:
                $email = "partner@amr.org";
                $auth = 'mM7x~f&$k';
                break;
            case Role::GOV:
                $email = "gov@amr.org";
                $auth = "HgçsàÖm*}";
                break;
        }
        
        $client->request(
            'POST',
            '/session',
            [],
            [],
            [],
            json_encode(['email' => $email, 'password' => $auth])
        );
        
        $response = $client->getResponse();

        $data = json_decode($response->getContent(), true);

        return $data["data"]["token"];
    }
}
