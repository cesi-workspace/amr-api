<?php

namespace App\Service;

use App\Entity\Connection;
use App\Service\Contract\IConnectionService;
use App\Service\Contract\IDateService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ConnectionService implements IConnectionService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CryptService $cryptservice
    ) {}

    public function initConnection(Request $request): Connection
    {
        $connection = new Connection();
        $connection->setLoginDate(new DateTime());
        $connection->setIpAddress($request->getClientIp());

        return $connection;
    }

    public function save(Connection $connection): void
    {
        $this->entityManager->persist($connection);
        $this->entityManager->flush();
    }

    public function findOneBy(array $query, array $orderBy = []): Connection
    {
        return $this->entityManager->getRepository(Connection::class)->findOneBy($query, $orderBy);
    }

    public function isTooMany(Request $request) : bool
    {
        $connections = $this->entityManager->getRepository(Connection::class)
        ->findConnectionsInLastDay($this->cryptservice->encrypt($request->getClientIp()));

        $connectionRefused = array_filter($connections, function($connection) {
            return $connection->getSuccess() == 0;
        });
        
        return (count($connectionRefused) >= 5);

    }
}