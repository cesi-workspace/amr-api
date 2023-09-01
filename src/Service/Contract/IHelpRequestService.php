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
    public function findOneBy(array $query, array $orderBy = []): HelpRequest|null;
    public function findHelpRequestTreatment(array $query, bool $single = true): HelpRequestTreatment|null|array;
    public function findHelpRequestCategory(array $findQuery): HelpRequestCategory|null;
    public function findHelpRequestStatus(array $findQuery): HelpRequestStatus|null;
    public function findHelpRequestTreatmentType(array $findQuery): HelpRequestTreatmentType|null;
    public function findHelpRequestCategoryByTitle(HelpRequestCategoryTitle|string $helpRequestCategory): HelpRequestCategory|null;
    public function findHelpRequestStatusByLabel(HelpRequestStatusLabel|string $helpRequestStatusLabel): HelpRequestStatus|null;
    public function findHelpRequestTreatmentTypeByLabel(HelpRequestTreatmentTypeLabel|string $helpRequestTreatmentTypeLabel): HelpRequestTreatmentType|null;
    public function getInfo(HelpRequest $helpRequest, bool $details): array;
    public function createHelprequest(Request $request): JsonResponse;
    public function getHelprequest(HelpRequest $helpRequest): JsonResponse;
    public function postHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse;
    public function deleteHelpRequestTreatment(HelpRequest $request) : JsonResponse;
    public function acceptHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse;
    public function finishHelpRequest(Request $request, HelpRequest $helpRequest) : JsonResponse;
    public function deleteHelpRequest(HelpRequest $helpRequest) : JsonResponse;
    public function getHelpRequestCategories() : JsonResponse;
    public function getHelpRequests(Request $request) : JsonResponse;
    public function getHelpRequestsHistory(Request $request) : JsonResponse;
    public function getOwnHelpRequests(User $user, Request $request) : JsonResponse;
    public function addHelpRequestCategory(Request $request) : JsonResponse;
    public function removeHelpRequestCategory(HelpRequestCategory $helpRequestCategory) : JsonResponse;
    public function editHelpRequestCategory(Request $request, HelpRequestCategory $helpRequestCategory) : JsonResponse;
    public function getHelpRequestStats(Request $request) : JsonResponse;
}