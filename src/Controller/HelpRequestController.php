<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\HelpRequest;
use App\Entity\HelpRequestCategory;
use App\Repository\HelpRequestRepository;
use App\Service\Contract\IHelpRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class HelpRequestController extends AbstractController
{

    public function __construct(
        private readonly IHelpRequestService $helpRequestService
    ){}

    
    #[Route('/helprequests', name: 'app_help_request_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->helpRequestService->getHelpRequests($request);
    }
    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests', name: 'app_help_request_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        return $this->helpRequestService->createHelprequest($request);
    }

    #[Route('/helprequests/categories', name: 'app_help_request_category_index', methods: ['GET'])]
    public function indexCategories(): Response
    {
        return $this->helpRequestService->getHelpRequestCategories();
    }
    
    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests/{id}', name: 'app_help_request_show', methods: ['GET'])]
    public function show(HelpRequest $helpRequest): Response
    {
        return $this->helpRequestService->getHelprequest($helpRequest);
    }

    #[IsGranted('ROLE_HELPER')]
    #[Route('/helprequests/{id}/treatment', name: 'app_help_request_edit_treatment', methods: ['PUT'])]
    public function editTreatment(Request $request, HelpRequest $helpRequest): Response
    {
        return $this->helpRequestService->postHelpRequestTreatment($request, $helpRequest);
    }

    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests/{helprequest_id}/accept', name: 'app_help_request_edit_accept', methods: ['PUT'])]
    #[Entity('helpRequest', expr: 'repository.find(helprequest_id)')]
    public function editAcceptTreatment(Request $request, HelpRequest $helpRequest) : Response
    {
        return $this->helpRequestService->acceptHelpRequestTreatment($request, $helpRequest);
    }

    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests/{id}/finish', name: 'app_help_request_edit_finish', methods: ['PUT'])]
    public function editFinish(Request $request, HelpRequest $helpRequest)
    {
        return $this->helpRequestService->finishHelpRequest($request, $helpRequest);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_OWNER")'))]
    #[Route('/helprequests/{id}', name: 'app_help_request_delete', methods: ['DELETE'])]
    public function delete(HelpRequest $helpRequest): Response
    {
        return $this->helpRequestService->deleteHelpRequest($helpRequest);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/helprequests/categories', name: 'app_help_request_category_new', methods: ['POST'])]
    public function newCategory(Request $request) : Response
    {
        return $this->helpRequestService->addHelpRequestCategory($request);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/helprequests/categories/{id}', name: 'app_help_request_category_delete', methods: ['DELETE'])]
    public function removeCategory(HelpRequestCategory $helpRequestCategory) : Response
    {
        return $this->helpRequestService->removeHelpRequestCategory($helpRequestCategory);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/helprequests/categories/{id}', name: 'app_help_request_category_edit', methods: ['PUT'])]
    public function editCategory(Request $request, HelpRequestCategory $helpRequestCategory) : Response
    {
        return $this->helpRequestService->editHelpRequestCategory($request, $helpRequestCategory);
    }

}
