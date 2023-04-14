<?php

namespace App\Service\Contract;

use App\Entity\User;

interface IUserService
{

    function isUserExists(array $findQuery): bool;
    function findUser(array $findQuery): User;

}