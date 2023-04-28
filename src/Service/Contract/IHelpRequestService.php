<?php

namespace App\Service\Contract;

use App\Entity\HelpRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface IHelpRequestService
{
    function createHelprequest(Request $request): JsonResponse;
}