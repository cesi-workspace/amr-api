<?php

namespace App\Service\Contract;

use App\Entity\Connection;
use Symfony\Component\HttpFoundation\Request;

interface IConnectionService
{

    function initConnection(Request $request): Connection;
    function findOneBy(array $query, array $orderBy = []): Connection;
    function save(Connection $connection): void;
}