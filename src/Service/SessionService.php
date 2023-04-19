<?php

namespace App\Service;

use App\Service\Contract\IConnectionService;
use App\Service\Contract\IResponseValidatorService;
use App\Service\Contract\ISessionService;
use App\Service\Contract\IUserService;
use DateTime;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SessionService implements ISessionService
{

    public function __construct(
        private readonly IConnectionService       $connectionService,
        private readonly IResponseValidatorService $responseValidatorService,
        private readonly IUserService             $userService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly JWTTokenManagerInterface $JWTTokenManager
    ) {}

    function login(Request $request): JsonResponse
    {
        $connection = $this->connectionService->initConnection($request);

        $requestBody = json_decode($request->getContent(), true);
        $constraints = new Assert\Collection([
            'email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank],
            'password' => [new Assert\Type('string'), new Assert\NotBlank]
        ]);

        $errorMessages = $this->responseValidatorService->getErrorMessagesValidation($requestBody, $constraints);

        if (count($errorMessages) != 0) {
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        if($this->userService->isUserExists(['email' => $requestBody->email])){
            $connection->setSuccess(false);
            $this->connectionService->save($connection);
            return new JsonResponse(['message' => 'Authentification échouée, vérifiez le login et le mot de passe'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userService->findUser(['email' => $requestBody->email]);

        if (!$this->userPasswordHasher->isPasswordValid($user, $requestBody->password)) {
            $connection->setSuccess(false);
            $this->connectionService->save($connection);
            return new JsonResponse(['message' => 'Authentification échouée, vérifiez le login et le mot de passe'], Response::HTTP_UNAUTHORIZED);
        }

        $connection->setUser($user);
        $connection->setSuccess(true);
        $this->connectionService->save($connection);

        $authToken = $this->JWTTokenManager->create($user);
        return new JsonResponse(['message' => 'Authentification réussie', 'data' => ['token' => $authToken]], Response::HTTP_OK);
    }

    function logout(): JsonResponse
    {
        $connectedUser = $this->getUser();
        $connection = $this->connectionService->findOneBy(
            [
                'user' => $connectedUser,
                'success' => true,
                'logoutDate' => null
            ],
            ['loginDate' => 'DESC']
        );

        $connection->setLogoutDate(new DateTime());
        $this->connectionService->save($connection);

        return new JsonResponse(['message' => 'Déconnexion réussie'], Response::HTTP_OK);
    }
}