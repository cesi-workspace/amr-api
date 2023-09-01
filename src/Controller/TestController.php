<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return new Response('Test CI/CD');
    }

}