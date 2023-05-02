<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Comment;
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

    function findOneBy(array $query, array $orderBy = []): Comment
    {
        return $this->entityManager->getRepository(Comment::class)->findOneBy($query, $orderBy);
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

}