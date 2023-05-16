<?php

namespace App\Service\Contract;

use App\Entity\HelpRequest;
use App\Entity\User;
use App\Entity\HelpRequestCategory;
use App\Entity\HelpRequestTreatment;
use App\Entity\HelpRequestStatus;
use App\Entity\HelpRequestTreatmentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\HelpRequestCategoryTitle;
use App\Service\HelpRequestStatusLabel;
use App\Service\HelpRequestTreatmentTypeLabel;

interface IHelpRequestService
{
    function findOneBy(array $query, array $orderBy = []): HelpRequest|null;
    function findHelpRequestTreatment(array $query, bool $single = true): HelpRequestTreatment|null|array;
    function findHelpRequestCategory(array $findQuery): HelpRequestCategory|null;
    function findHelpRequestStatus(array $findQuery): HelpRequestStatus|null;
    function findHelpRequestTreatmentType(array $findQuery): HelpRequestTreatmentType|null;
    function findHelpRequestCategoryByTitle(HelpRequestCategoryTitle|string $helpRequestCategory): HelpRequestCategory|null;
    function findHelpRequestStatusByLabel(HelpRequestStatusLabel|string $helpRequestStatusLabel): HelpRequestStatus|null;
    function findHelpRequestTreatmentTypeByLabel(HelpRequestTreatmentTypeLabel|string $helpRequestTreatmentTypeLabel): HelpRequestTreatmentType|null;
    public function getInfo(HelpRequest $helpRequest, bool $details): array;
    function createHelprequest(Request $request): JsonResponse;
    function getHelprequest(HelpRequest $helpRequest): JsonResponse;
    function postHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse;
    function deleteHelpRequestTreatment(HelpRequest $request) : JsonResponse;
    function acceptHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse;
    function finishHelpRequest(Request $request, HelpRequest $helpRequest) : JsonResponse;
    function deleteHelpRequest(HelpRequest $helpRequest) : JsonResponse;
    function getHelpRequestCategories() : JsonResponse;
    function getHelpRequests(Request $request) : JsonResponse;
    function getHelpRequestsHistory(Request $request) : JsonResponse;
    function getOwnHelpRequests(User $user, Request $request) : JsonResponse;
    function addHelpRequestCategory(Request $request) : JsonResponse;
    function removeHelpRequestCategory(HelpRequestCategory $helpRequestCategory) : JsonResponse;
    function editHelpRequestCategory(Request $request, HelpRequestCategory $helpRequestCategory) : JsonResponse;
    function getHelpRequestStats(Request $request) : JsonResponse;
}