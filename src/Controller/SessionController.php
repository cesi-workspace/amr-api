<?php

namespace App\Controller;

use App\Service\Contract\ISessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SessionController extends AbstractController
{

    public function __construct(
        private readonly ISessionService $sessionService
    ){}

    #[Route('/session', name: 'session_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        return $this->sessionService->login($request);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/session', name: 'session_logout', methods: ['DELETE'])]
    public function logout(): Response
    {
        return $this->sessionService->logout($this->getUser());
    }
}
