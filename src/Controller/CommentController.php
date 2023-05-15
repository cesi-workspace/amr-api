<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Answer;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\Contract\ICommentService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class CommentController extends AbstractController
{
    
    public function __construct(
        private readonly ICommentService $commentService
    ){}


    #[IsGranted('ROLE_OWNER')]
    #[Route('/comments', name: 'app_comment_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        return $this->commentService->createComment($request);
    }
    
    #[IsGranted(new Expression('is_granted("ROLE_MODERATOR") or is_granted("ROLE_OWNER") or is_granted("ROLE_HELPER")'))]
    #[Route('/comments', name: 'app_comment_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->commentService->getComments($request);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/comments/{id}/report', name: 'app_comment_report_new', methods: ['POST'])]
    public function newReport(Comment $comment): Response
    {
        return $this->commentService->postReportOnComment($comment);
    }

    #[IsGranted('ROLE_MODERATOR')]
    #[Route('/comments/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->commentService->getComment($comment);
    }

    #[IsGranted(new Expression('is_granted("ROLE_MODERATOR") or is_granted("ROLE_OWNER")'))]
    #[Route('/comments/{id}', name: 'app_comment_delete', methods: ['DELETE'])]
    public function delete(Comment $comment): Response
    {
        return $this->commentService->deleteComment($comment);
    }
    #[IsGranted(new Expression('is_granted("ROLE_MODERATOR") or is_granted("ROLE_HELPER")'))]
    #[Route('/comments/{id}/answers', name: 'app_comment_answer_new', methods: ['POST'])]
    public function newAnswer(Request $request, Comment $comment) : Response
    {
        return $this->commentService->postAnswerToComment($request, $comment);
    }
    
    
}
