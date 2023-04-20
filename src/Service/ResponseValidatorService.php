<?php

namespace App\Service;
use App\Exception\ValidationContraintsException;
use App\Service\Contract\IResponseValidatorService;
use Exception;
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

    public function checkContraintsValidation($parameters, $constraints){
        $errorMessages = $this->getErrorMessagesValidation($parameters, $constraints);

        if (count($errorMessages) != 0){
            throw new ValidationContraintsException($errorMessages);
        }
    }

}