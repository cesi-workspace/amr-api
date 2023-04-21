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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class UserController extends AbstractController
{

    public function __construct(
        private readonly IUserService $userService
    ){}
    
    //Route pour ajouter un utilisateur
    #[Route('/users', name: 'users_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        return $this->userService->createUser($request);
    }

    // Récupérer les données de l'utilisateur connecté seulement
    /*
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/users', name: 'users_get', methods: ['GET'])]
    public function getallusers(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager): Response
    {
        $users=$em->getRepository(User::class)->findAll();
        
        $parametersURL = $request->query->all();

        $constraints = new Assert\Collection([
            'fields' => [
                'usertype' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Usertype::class, 'label', true, false)],
                'userstatus' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Userstatus::class, 'label', true, false)],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank]
            ],
            'allowMissingFields' => true,
        ]);

        return new JsonResponse(['message' => 'Utilisateurs récupérés', 'data' => $users], Response::HTTP_OK);
    }*/

    // Récupérer les données de l'utilisateur connecté seulement
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'users_get', methods: ['GET'])]
    public function get(Request $request, User $user): Response
    {
        return $this->userService->getUser($request, $user);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'user_edit', methods: ['PUT'])]
    public function edit(Request $request, User $user): Response
    {
        return $this->userService->editUser($request, $user);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function remove(Request $request, User $user): Response
    {
        return $this->userService->removeUser($request, $user);
    }
}
