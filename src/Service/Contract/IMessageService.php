<?php

namespace App\Service\Contract;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface IMessageService
{
    function getInfo(Message $message) : array;
    public function getInfos(array $helpmessages): array;
}