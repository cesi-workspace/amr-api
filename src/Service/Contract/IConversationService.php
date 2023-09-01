<?php

namespace App\Service\Contract;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface IConversationService
{
    public function createMessage(Request $request, User $userto) : JsonResponse;
    public function getConversationMessages(User $user) : JsonResponse;
    public function getConversations() : JsonResponse;
}