<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class TestController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    #[Route('/test', name: 'app_test')]
    public function test()
    {
        return 'Test CI/CD';
    }

}