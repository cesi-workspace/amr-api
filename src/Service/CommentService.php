<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Report;
use App\Service\Contract\ICommentService;
use App\Service\Contract\IHelpRequestService;
use App\Service\Contract\IDateService;
use App\Service\Contract\IResponseValidatorService;
use App\Service\Contract\IUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use App\Service\UserTypeLabel as UserTypeLabel;

class CommentService implements ICommentService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IResponseValidatorService $responseValidatorService,
        private readonly IUserService $userService,
        private readonly IHelpRequestService $helpRequestService,
        private readonly EmailService $emailService
    ) {}

    function findComment(array $query, array $orderBy = []): Comment|null
    {
        return $this->entityManager->getRepository(Comment::class)->findOneBy($query, $orderBy);
    }
    function findComments(array $query, array $orderBy = []): array|null
    {
        return $this->entityManager->getRepository(Comment::class)->findBy($query, $orderBy);
    }
    
    public function getInfos(array $comments): array
    {
        $arraycomments = [];
        foreach($comments as $key => $value){
            $arraycomments[$key] = $this->getInfo($value);
        }
        return $arraycomments;
    }
    public function getInfo(Comment $comment): array
    {   
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'mark' => $comment->getMark(),
            'date' => $comment->getDate()->format('Y-m-d H:i:s'),
            'owner' => $this->userService->getInfo($comment->getOwner()),
            'helper' => $this->userService->getInfo($comment->getHelper()),
            'number_report' => $this->entityManager->getRepository(Report::class)->countReportByComment($comment)
        ];
    }

    function createComment(Request $request) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = json_decode($request->getContent(), true);

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'helper_id' => [new Assert\Type('int'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)],
                'content' => [new Assert\Type('string'), new Assert\NotBlank],
                'mark' => [new Assert\Type('int'), new Assert\NotBlank, new Assert\Range(min: 0, max: 10)],
            ])
        );

        $helper = $this->userService->findUser([
            'id' => $parameters['helper_id'],
            'type' => $this->userService->findUserTypeByLabel(UserTypeLabel::HELPER)
        ]);

        if($helper == null){
            return new JsonResponse(['message' => '', 'data' => ['helper_id' => 'Cet utilisateur n\'existe pas ou n\'est pas membre volontaire']], Response::HTTP_BAD_REQUEST);
        }

        $helpRequest = $this->helpRequestService->findOneBy([
            'status' => $this->helpRequestService->findHelpRequestStatusByLabel(HelpRequestStatusLabel::FINISHED),
            'helper' => $this->userService->findUser(['id' => $parameters['helper_id']]),
            'owner' => $userconnect
        ]);

        if($helpRequest == null){
            return new JsonResponse(['message' =>  "Il n'existe pas de demandes d'aides terminées associées à ce membre volontaire que vous aviez créée"], Response::HTTP_BAD_REQUEST);
        }
        
        $comment = new Comment();
        $comment->setHelper($helper);
        $comment->setContent($parameters['content']);
        $comment->setMark($parameters['mark']);
        $comment->setDate(new DateTime());
        $comment->setOwner($userconnect);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Commentaire ajouté'], Response::HTTP_OK);
    }

    function getComments(Request $request) : JsonResponse
    {
        $parameters = $request->query->all();

        if(!$this->security->isGranted('ROLE_ADMIN') && ($this->security->isGranted('ROLE_OWNER') || $this->security->isGranted('ROLE_HELPER'))){
            $this->responseValidatorService->checkContraintsValidation($parameters,
                new Assert\Collection([
                    'helper_id' => [new Assert\Type('int'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)]
                ])
            );

            $comments = $this->findComments([
                'helper' => $this->userService->findUser(['id' => $parameters['helper_id']])
            ], ['date' => 'DESC']);

            return new JsonResponse(['message' => "Demandes d'aide récupérées", 'data' => $this->getInfos($comments)]);

        }else{
            new Assert\Collection(
                fields: [
                    'start_date' => [new Assert\DateTime, new Assert\NotBlank],
                    'end_date' => [new Assert\DateTime, new Assert\NotBlank],
                    'helper_id' => [new Assert\Type('int'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)],
                    'owner_id' => [new Assert\Type('int'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)],
                    'min_number_report' => [new Assert\Type('int'), new Assert\NotBlank],
            ],
            allowMissingFields: true);

            $comments = $this->entityManager->getRepository(Comment::class)->findCommentsByCriteria(
                $parameters
            );

            $result = $this->getInfos($comments);

            if(array_key_exists('min_number_report', $parameters)){
                $minNumberReport = (int)$parameters['min_number_report'];
                $result = array_filter($result, function($v, $k) use($minNumberReport) {
                    return $v['number_report'] >= $minNumberReport;
                }, ARRAY_FILTER_USE_BOTH);
            }

            return new JsonResponse(['message' => "Demandes d'aide récupérées", 'data' => $result]);
        }
    }

    function postReportOnComment(Comment $comment) : JsonResponse
    {
        $userconnect = $this->security->getUser();

        if($this->entityManager->getRepository(Report::class)->findBy([
            'user' => $userconnect,
            'comment' => $comment
        ]) != null){
            return new JsonResponse(["message" => "Commentaire déjà signalée"], Response::HTTP_BAD_REQUEST);
        }

        $report = new Report();
        $report->setComment($comment);
        $report->setUser($this->security->getUser());
        $report->setDate(new DateTime());
        $this->entityManager->persist($report);
        $this->entityManager->flush();

        return new JsonResponse(["message" => "Commentaire signalée"], Response::HTTP_OK);
    }

    function deleteComment(Comment $comment) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_MODERATOR') && $this->security->isGranted('ROLE_OWNER') && $userconnect->getId() != $comment->getOwner()->getId()){
            throw new AccessDeniedException("La suppression de commentaires que vous n'avez pas écris est interdite");
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return new JsonResponse(["message" => "Commentaire supprimé"], Response::HTTP_OK);

    }

}