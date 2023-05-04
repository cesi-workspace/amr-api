<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Service\Contract\IConversationService;
use App\Service\Contract\IMessageService;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class ConversationController extends AbstractController
{
    public function __construct(
        private readonly IConversationService $conversationService
    ){}
    /*
    #[Route('/messages', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }
*/
    #[IsGranted(new Expression('is_granted("ROLE_OWNER") or is_granted("ROLE_HELPER")'))]
    #[Route('/conversations/{id}/messages', name: 'app_conversation_message_new', methods: ['POST'])]
    public function newMessage(Request $request, User $user): Response
    {
        return $this->conversationService->createMessage($request, $user);
    }
    #[IsGranted(new Expression('is_granted("ROLE_OWNER") or is_granted("ROLE_HELPER")'))]
    #[Route('/conversations/{id}/messages', name: 'app_conversation_message_index', methods: ['GET'])]
    public function indexMessage(Request $request, User $user): Response
    {
        return $this->conversationService->getConversationMessages($user);
    }
    
/*
    #[Route('/messages/{date}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{date}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageRepository->save($message, true);

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{date}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getDate(), $request->request->get('_token'))) {
            $messageRepository->remove($message, true);
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }*/
}
