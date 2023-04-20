<?php

namespace App\Service;
use App\Service\Contract\IResponseValidatorService;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResponseValidatorService implements IResponseValidatorService
{

    public function __construct(public ValidatorInterface $validator){
        
    }

    public function getErrorMessagesValidation($parameters, $constraints): array
    {
        $violations = $this->validator->validate($parameters, $constraints);
        
        $errorMessages = [];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($violations as $violation) {

            $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
        }

        return $errorMessages;
    }

}