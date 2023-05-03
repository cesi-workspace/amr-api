<?php

namespace App\Service\Contract;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ICommentService
{
    function createComment(Request $request): JsonResponse;
    function getComments(Request $request) : JsonResponse;
}