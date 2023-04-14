<?php

namespace App\Service;

use App\Entity\Connection;
use App\Service\Contract\IConnectionService;
use App\Service\Contract\IDateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class ConnectionService implements IConnectionService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    function initConnection(Request $request): Connection
    {
        $connection = new Connection();
        $connection->setLoginDate(new DateTime());
        $connection->setIpAddress($request->getClientIp());

        return $connection;
    }

    function save(Connection $connection): void
    {
        $this->entityManager->persist($connection);
        $this->entityManager->flush();
    }

    function findOneBy(array $query, array $orderBy = []): Connection
    {
        return $this->entityManager->getRepository(Connection::class)->findOneBy($query, $orderBy);
    }
}