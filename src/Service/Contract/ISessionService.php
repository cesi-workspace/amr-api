<?php

namespace App\Service\Contract;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

interface ISessionService
{

    public function login(Request $request): JsonResponse;
    public function logout(User $user): JsonResponse;

}