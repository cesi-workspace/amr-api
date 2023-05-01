<?php

namespace App\Service\Contract;

use App\Entity\HelpRequest;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface IHelpRequestService
{
    function createHelprequest(Request $request): JsonResponse;
    function getHelprequest(HelpRequest $helpRequest): JsonResponse;
    function postHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse;
    function acceptHelpRequestTreatment(Request $request, HelpRequest $helpRequest, User $user) : JsonResponse;
}