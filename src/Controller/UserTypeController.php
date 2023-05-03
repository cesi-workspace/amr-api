<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\HelpRequest;
use App\Form\HelpRequestType;
use App\Repository\HelpRequestRepository;
use App\Service\Contract\IHelpRequestService;
use App\Service\Contract\IUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class UserTypeController extends AbstractController
{

    public function __construct(
        private readonly IUserService $userService
    ){}

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/usertypes', name: 'app_user_type_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->userService->getUserTypes();
    }
}
