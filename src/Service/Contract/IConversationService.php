<?php

namespace App\Service\Contract;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface IConversationService
{
    function createMessage(Request $request, User $userto) : JsonResponse;
    function getConversationMessages(User $user) : JsonResponse;
    function getConversations() : JsonResponse;
}