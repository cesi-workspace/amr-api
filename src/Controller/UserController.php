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
    #[Route('/users', name: 'users_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        return $this->userService->createUser($request);
    }

    // Récupérer les données de l'utilisateur connecté seulement
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users', name: 'users_get', methods: ['GET'])]
    public function getAll(Request $request): Response
    {
        return $this->userService->getUsers($request);
    }

    // Récupérer les données d'un utilisateur
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'user_get', methods: ['GET'])]
    public function get(Request $request, User $user): Response
    {
        return $this->userService->getUser($request, $user);
    }
    
    // Modifier les données standard d'un utilisateur
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'user_edit', methods: ['PUT'])]
    public function edit(Request $request, User $user): Response
    {
        return $this->userService->editUser($request, $user);
    }
    
    // Supprimer un utilisateur
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function remove(Request $request, User $user): Response
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
    #[Route('/users/{id}/favorites', name: 'user_add_favorite', methods: ['POST'])]
    public function addFavorite(Request $request, User $user): Response
    {
        return $this->userService->addFavoriteUser($request, $user);
    }

    //Supprimer un membrevolontaire des favoris pour un membremr
    #[IsGranted('ROLE_OWNER')]
    #[Route('/users/{id1}/favorites/{id2}', name: 'user_del_favorite', methods: ['DELETE'])]
    #[Entity('owner', expr: 'repository.find(id1)')]
    #[Entity('helper', expr: 'repository.find(id2)')]
    public function removeFavorite(User $owner, User $helper): Response
    {
        return $this->userService->removeFavoriteUser($owner, $helper);
    }
}
