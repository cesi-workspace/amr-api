<?php

namespace App\Service\Contract;

use App\Entity\Comment;
use App\Entity\Answer;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ICommentService
{
    function createComment(Request $request): JsonResponse;
    function getComments(Request $request) : JsonResponse;
    function postReportOnComment(Comment $comment) : JsonResponse;
    function deleteComment(Comment $comment) : JsonResponse;
    function getComment(Comment $comment) : JsonResponse;
    function postAnswerToComment(Request $request, Comment $comment) : JsonResponse;
    function deleteAnswerToComment(Comment $comment, Answer $answer) : JsonResponse;
}