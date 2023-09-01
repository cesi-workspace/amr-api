<?php

namespace App\Service\Contract;

interface IResponseValidatorService
{
    public function getErrorMessagesValidation($parameters, $constraints): array;
}