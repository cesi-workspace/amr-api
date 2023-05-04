<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Report;
use App\Service\Contract\IConversationService;
use App\Service\Contract\IMessageService;
use App\Service\Contract\IResponseValidatorService;
use App\Service\Contract\IUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use App\Service\UserTypeLabel as UserTypeLabel;

class ConversationService implements IConversationService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IResponseValidatorService $responseValidatorService,
        private readonly IUserService $userService,
        private readonly IMessageService $messageService,
        private readonly RequestAmrService $requestAmrService
    ) {}

    function createMessage(Request $request, User $userto) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = json_decode($request->getContent(), true);

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'content' => [new Assert\Type('string'), new Assert\NotBlank],
            ])
        );


        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $userto->getType()->getLabel() != userTypeLabel::HELPER->value){
            return new JsonResponse(['message' => 'Les données ne sont pas valides : Il s\'agit pas d\'un utilisateur membrevolontaire'], Response::HTTP_BAD_REQUEST);
        }
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER') && $userto->getType()->getLabel() != userTypeLabel::OWNER->value){
            return new JsonResponse(['message' => 'Les données ne sont pas valides : Il s\'agit pas d\'un utilisateur membre mr'], Response::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($parameters['content']);
        $message->setFromUser($userconnect);
        $message->setToUser($userto);
        $message->setDate(new DateTime());
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Message envoyé'], Response::HTTP_OK);

    }

    function getConversationMessages(User $user) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $user->getType()->getLabel() != userTypeLabel::HELPER->value){
            return new JsonResponse(['message' => 'Erreur de validation de données : La conversation est possible seulement avec un membre volontaire'], Response::HTTP_BAD_REQUEST);
        }
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER') && $user->getType()->getLabel() != userTypeLabel::OWNER->value){
            return new JsonResponse(['message' => 'Erreur de validation de données : La conversation est possible seulement avec un membre mr'], Response::HTTP_BAD_REQUEST);
        }
        $messages = $this->entityManager->getRepository(Message::class)->getConversationsMessage($userconnect, $user);
        return new JsonResponse(["message" => "Message récupérés", "data" => $this->messageService->getInfos($messages)], $messages ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }
    public function getInfos(array $conversations): array
    {
        $arrayconversations = [];
        foreach($conversations as $key => $value){
            $arrayconversations[$key] = $this->getInfo($value);
        }
        return $arrayconversations;
    }
    function getInfo(array $conversation) : array
    {
        $lastmessage = $this->entityManager->getRepository(Message::class)->find((int)$conversation['last_message_id']);
        $user = $this->userService->findUser([
            'id' => (int)$conversation['user_id']
        ]);

        return [
            'user' => $this->userService->getInfo($user),
            'last_message' => $this->messageService->getInfo($lastmessage)
        ];
    }

    function getConversations() : JsonResponse
    {
        $userconnect=$this->security->getUser();
        $conversations = $this->entityManager->getRepository(User::class)->getLastMessageByUsers($userconnect);
        return new JsonResponse(["message" => "Conversations récupérées", "data" => $this->getInfos($conversations)], $conversations ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

}