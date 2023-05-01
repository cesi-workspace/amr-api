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

class HelpRequestController extends AbstractController
{

    public function __construct(
        private readonly IHelpRequestService $helpRequestService
    ){}

    /*
    #[Route('/helprequests', name: 'app_help_request_index', methods: ['GET'])]
    public function index(HelpRequestRepository $helpRequestRepository): Response
    {
        return $this->render('help_request/index.html.twig', [
            'help_requests' => $helpRequestRepository->findAll(),
        ]);
    }*/
    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests', name: 'app_help_request_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        return $this->helpRequestService->createHelprequest($request);
    }
    
    #[IsGranted(new Expression('is_granted("ROLE_HELPER") or is_granted("ROLE_OWNER")'))]
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
    #[Route('/helprequests/{helprequest_id}/accept/{owner_id}', name: 'app_help_request_edit_accept', methods: ['PUT'])]
    #[Entity('helpRequest', expr: 'repository.find(helprequest_id)')]
    #[Entity('owner', expr: 'repository.find(owner_id)')]
    public function editAcceptTreatment(Request $request, HelpRequest $helpRequest, User $owner) : Response
    {
        return $this->helpRequestService->acceptHelpRequestTreatment($request, $helpRequest, $owner);
    }

    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests/{id}/finish', name: 'app_help_request_edit_finish', methods: ['PUT'])]
    public function editFinish(Request $request, HelpRequest $helpRequest)
    {
        return $this->helpRequestService->finishHelpRequest($request, $helpRequest);
    }

    #[IsGranted('ROLE_OWNER')]
    #[Route('/helprequests/{id}', name: 'app_help_request_delete', methods: ['DELETE'])]
    public function delete(HelpRequest $helpRequest): Response
    {
        return $this->helpRequestService->deleteHelpRequest($helpRequest);
    }
}
