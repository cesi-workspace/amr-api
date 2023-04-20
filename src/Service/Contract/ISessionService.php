<?php

namespace App\Service\Contract;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

interface ISessionService
{

    function login(Request $request): JsonResponse;
    function logout(User $user): JsonResponse;

}