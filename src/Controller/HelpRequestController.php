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

/*
    #[Route('/helprequests/{id}/edit', name: 'app_help_request_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HelpRequest $helpRequest, HelpRequestRepository $helpRequestRepository): Response
    {
        $form = $this->createForm(HelpRequestType::class, $helpRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $helpRequestRepository->save($helpRequest, true);

            return $this->redirectToRoute('app_help_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('help_request/edit.html.twig', [
            'help_request' => $helpRequest,
            'form' => $form,
        ]);
    }

    #[Route('/helprequests/{id}', name: 'app_help_request_delete', methods: ['POST'])]
    public function delete(Request $request, HelpRequest $helpRequest, HelpRequestRepository $helpRequestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$helpRequest->getId(), $request->request->get('_token'))) {
            $helpRequestRepository->remove($helpRequest, true);
        }

        return $this->redirectToRoute('app_help_request_index', [], Response::HTTP_SEE_OTHER);
    }
    */
}
