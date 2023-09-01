<?php

namespace App\Service\Contract;

use App\Entity\Connection;
use Symfony\Component\HttpFoundation\Request;

interface IConnectionService
{

    public function initConnection(Request $request): Connection;
    public function findOneBy(array $query, array $orderBy = []): Connection;
    public function save(Connection $connection): void;
    public function isTooMany(Request $request) : bool;
}