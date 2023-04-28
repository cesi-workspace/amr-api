<?php

namespace App\Controller;

use App\Entity\HelpRequest;
use App\Form\HelpRequestType;
use App\Repository\HelpRequestRepository;
use App\Service\Contract\IHelpRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
/*
    #[Route('/helprequests/{id}', name: 'app_help_request_show', methods: ['GET'])]
    public function show(HelpRequest $helpRequest): Response
    {
        return $this->render('help_request/show.html.twig', [
            'help_request' => $helpRequest,
        ]);
    }

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
