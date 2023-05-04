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
        private readonly IMessageService $messageService
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
            return new JsonResponse(['message' => 'Les données ne sont pas valides : Il s\'agit pas d\'un utilisateur membrevolontaire'], Response::HTTP_BAD_REQUEST);
        }
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER') && $user->getType()->getLabel() != userTypeLabel::OWNER->value){
            return new JsonResponse(['message' => 'Les données ne sont pas valides : Il s\'agit pas d\'un utilisateur membre mr'], Response::HTTP_BAD_REQUEST);
        }
        $messages = $this->entityManager->getRepository(Message::class)->getConversationsMessage($userconnect, $user);
        if(count($messages) == 0){
            throw new NotFoundHttpException();
        }
        return new JsonResponse(["message" => "Message récupérés", "data" => $this->messageService->getInfos($messages)], Response::HTTP_OK);
    }

}