<?php

namespace App\Service\Contract;

use App\Entity\User;
use App\Entity\UserStatus;
use App\Entity\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserTypeLabel;
use App\Service\UserStatusLabel;

interface IUserService
{

    function isUserExists(array $findQuery): bool;
    function findUser(array $findQuery): User | null;
    function findUsers(array $findQuery): array | null;
    function findUserType(array $findQuery): UserType|null;
    function findUserStatus(array $findQuery): UserStatus|null;
    function findUserStatusByLabel(UserStatusLabel|string $userStatusLabel): UserStatus|null;
    function findUserTypeByLabel(UserTypeLabel|string $userTypeLabel): UserType|null;
    function getInfo(User $user) : array;
    function createUser(Request $request) : JsonResponse;
    function getUser(Request $request, User $user) : JsonResponse;
    function getUsers(Request $request) : JsonResponse;
    function editUser(Request $request, User $user) : JsonResponse;
    function removeUser(Request $request, User $user) : JsonResponse;
    function editStatusUser(Request $request, User $user) : JsonResponse;
    function sendProofsUser(Request $request, User $user) : JsonResponse;
    function addFavoriteUser(Request $request, User $user) : JsonResponse;
    function removeFavoriteUser(User $owner, User $helper) : JsonResponse;

}