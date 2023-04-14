<?php

namespace App\Service\Contract;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ISessionService
{

    function login(Request $request): JsonResponse;
    function logout(): JsonResponse;

}