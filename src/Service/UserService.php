<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Contract\IUserService;
use Doctrine\ORM\EntityManagerInterface;

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
}