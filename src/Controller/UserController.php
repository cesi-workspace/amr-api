<?php

namespace App\Controller;

use App\Entity\UserStatus;
use App\Entity\User;
use App\Service\Contract\IUserService;
use App\Service\CryptService;
use App\Service\EmailService;
use App\Service\ResponseValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class UserController extends AbstractController
{

    public function __construct(
        private readonly IUserService $userService
    ){}
    
    //Ajouter un nouvel utilisateur
    #[Route('/users', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        return $this->userService->createUser($request);
    }

    // Récupérer les données de l'utilisateur connecté seulement
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_MODERATOR")'))]
    #[Route('/users', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->userService->getUsers($request);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/status', name: 'app_user_status_index', methods: ['GET'])]
    public function indexStatus(): Response
    {
        return $this->userService->getUserStatus();
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/types', name: 'app_user_type_index', methods: ['GET'])]
    public function indexTypes(): Response
    {
        return $this->userService->getUserTypes();
    }

    // Récupérer les données d'un utilisateur
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(Request $request, User $user): Response
    {
        return $this->userService->getUser($request, $user);
    }
    
    // Modifier les données standard d'un utilisateur
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request, User $user): Response
    {
        return $this->userService->editUser($request, $user);
    }
    
    // Supprimer un utilisateur
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user): Response
    {
        return $this->userService->removeUser($request, $user);
    }

    //Modifier le statut d'un utilisateur
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_GOV")'))]
    #[Route('/users/{id}/status', name: 'user_status_edit', methods: ['PUT'])]
    public function editStatus(Request $request, User $user): Response
    {
        return $this->userService->editStatusUser($request, $user);
    }

    /*
    #[Route('/users/{id}/proofs', name: 'user_send_proofs', methods: ['PUT'])]
    public function sendProofs(Request $request, User $user): Response
    {
        return $this->userService->sendProofsUser($request, $user);
    }*/

    //Ajouter un membrevolontaire en favori pour un membremr
    #[IsGranted('ROLE_OWNER')]
    #[Route('/users/{id}/favorites', name: 'app_user_new_favorite', methods: ['POST'])]
    public function newFavorite(Request $request, User $user): Response
    {
        return $this->userService->addFavoriteUser($request, $user);
    }

    //Supprimer un membrevolontaire des favoris pour un membremr
    #[IsGranted('ROLE_OWNER')]
    #[Route('/users/{id1}/favorites/{id2}', name: 'app_user_delete_favorite', methods: ['DELETE'])]
    #[Entity('owner', expr: 'repository.find(id1)')]
    #[Entity('helper', expr: 'repository.find(id2)')]
    public function deleteFavorite(User $owner, User $helper): Response
    {
        return $this->userService->removeFavoriteUser($owner, $helper);
    }

    #[IsGranted('ROLE_OWNER')]
    #[Route('/users/{id}/favorites', name: 'app_user_index_favorite', methods: ['GET'])]
    public function indexFavorites(User $user): Response
    {
        return $this->userService->getFavoriteUser($user);
    }
}
