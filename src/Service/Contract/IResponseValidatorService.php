<?php

namespace App\Service\Contract;

interface IResponseValidatorService
{
    function getErrorMessagesValidation($parameters, $constraints): array;
}