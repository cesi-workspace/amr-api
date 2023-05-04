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


    public function getInfos(array $helpmessages): array
    {
        $arraymessages = [];
        foreach($helpmessages as $key => $value){
            $arraymessages[$key] = $this->getInfo($value);
        }
        return $arraymessages;
    }
    function getInfo(Message $message) : array
    {
        return [
            'id' => $message->getId(),
            'sender_name' => $message->getFromUser()->getFirstName().' '.$message->getFromUser()->getSurname(),
            'receiver_name' => $message->getToUser()->getFirstName().' '.$message->getToUser()->getSurname(),
            'content' => $message->getContent(),
            'date' => $message->getDate()->format('Y-m-d H:i:s')
        ];
    }

}