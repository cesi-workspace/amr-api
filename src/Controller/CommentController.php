<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\Contract\ICommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

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

    /*
    #[Route('/comments/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/comments/{id}', name: 'app_comment_edit', methods: ['PUT'])]
    public function edit(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comments/{id}', name: 'app_comment_delete', methods: ['DELETE'])]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
    */
}
