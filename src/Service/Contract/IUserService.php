<?php

namespace App\Service\Contract;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface IUserService
{

    function isUserExists(array $findQuery): bool;
    function findUser(array $findQuery): User | null;
    function createUser(Request $request) : JsonResponse;
    function getUser(Request $request, User $user) : JsonResponse;
    function editUser(Request $request, User $user) : JsonResponse;
    function removeUser(Request $request, User $user) : JsonResponse;

}