<?php

namespace App\Service\Contract;

use App\Entity\Comment;
use App\Entity\Answer;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface ICommentService
{
    public function createComment(Request $request): JsonResponse;
    public function getComments(Request $request) : JsonResponse;
    public function postReportOnComment(Comment $comment) : JsonResponse;
    public function deleteComment(Comment $comment) : JsonResponse;
    public function getComment(Comment $comment) : JsonResponse;
    public function postAnswerToComment(Request $request, Comment $comment) : JsonResponse;
    public function deleteAnswerToComment(Comment $comment, Answer $answer) : JsonResponse;
}