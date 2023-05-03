<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\HelpRequest;
use App\Form\HelpRequestType;
use App\Repository\HelpRequestRepository;
use App\Service\Contract\IHelpRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class HelpRequestCategoryController extends AbstractController
{

    public function __construct(
        private readonly IHelpRequestService $helpRequestService
    ){}

    
    #[Route('/helprequestcategories', name: 'app_help_request_category_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->helpRequestService->getHelpRequestCategories();
    }
}
