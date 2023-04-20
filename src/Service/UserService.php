<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserStatus;
use App\Entity\UserType;
use App\Service\Contract\IUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserService implements IUserService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    function isUserExists(array $findQuery): bool
    {
        return $this->entityManager->getRepository(User::class)->count($findQuery) > 0;
    }

    function findUser(array $findQuery): User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy($findQuery);
    }

    function findUserType(array $findQuery): UserType
    {
        return $this->entityManager->getRepository(UserType::class)->findOneBy($findQuery);
    }

    function findUserStatus(array $findQuery): UserStatus
    {
        return $this->entityManager->getRepository(UserStatus::class)->findOneBy($findQuery);
    }
}