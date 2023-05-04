<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Report;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use App\Service\UserTypeLabel as UserTypeLabel;

class MessageService implements IMessageService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IResponseValidatorService $responseValidatorService,
        private readonly IUserService $userService,
    ) {}

    function createMessage(Request $request) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = json_decode($request->getContent(), true);

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'user_id' => [new Assert\Type('int'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)],
                'content' => [new Assert\Type('string'), new Assert\NotBlank],
            ])
        );

        $userto = new User();

        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER')){
            $userto=$this->userService->findUser([
                'id' => $parameters['user_id'],
                'type' => $this->userService->findUserTypeByLabel(userTypeLabel::HELPER->value)
            ]);
            if($userto == null){
                return new JsonResponse(['message' => 'Les données ne sont pas valides', 'data' => ['user_id' => 'Il s\'agit pas d\'un utilisateur membrevolontaire']], Response::HTTP_BAD_REQUEST);
            }
        }
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER')){
            $userto=$this->userService->findUser([
                'id' => $parameters['user_id'],
                'type' => $this->userService->findUserTypeByLabel(userTypeLabel::OWNER->value)
            ]);
            if($userto == null){
                return new JsonResponse(['message' => 'Les données ne sont pas valides', 'data' => ['user_id' => 'Il s\'agit pas d\'un utilisateur membre mr']], Response::HTTP_BAD_REQUEST);
            }
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

}