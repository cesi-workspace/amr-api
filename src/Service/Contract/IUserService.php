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

    public function isUserExists(array $findQuery): bool;
    public function findUser(array $findQuery): User | null;
    public function findUsers(array $findQuery): array | null;
    public function findUserType(array $findQuery): UserType|null;
    public function findUserStatus(array $findQuery): UserStatus|null;
    public function findUserStatusByLabel(UserStatusLabel|string $userStatusLabel): UserStatus|null;
    public function findUserTypeByLabel(UserTypeLabel|string $userTypeLabel): UserType|null;
    public function getInfos(array $users): array;
    public function getInfo(User $user) : array;
    public function createUser(Request $request) : JsonResponse;
    public function getUser(Request $request, User $user) : JsonResponse;
    public function getUsers(Request $request) : JsonResponse;
    public function editUser(Request $request, User $user) : JsonResponse;
    public function removeUser(Request $request, User $user) : JsonResponse;
    public function editStatusUser(Request $request, User $user) : JsonResponse;
    public function sendProofsUser(Request $request, User $user) : JsonResponse;
    public function addFavoriteUser(Request $request, User $user) : JsonResponse;
    public function removeFavoriteUser(User $owner, User $helper) : JsonResponse;
    public function getUserTypes() : JsonResponse;
    public function getUserStatus() : JsonResponse;
    public function getFavoriteUser(User $owner) : JsonResponse;
    public function sendProofIdentity(Request $request, User $user) : JsonResponse;
}