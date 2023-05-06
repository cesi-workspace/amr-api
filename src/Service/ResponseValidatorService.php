<?php

namespace App\Service;
use App\Exception\ValidationConstraintsException;
use App\Service\Contract\IResponseValidatorService;
use Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
        
        if($parameters == null){
            $parameters = [];
        }
        $errorMessages = $this->getErrorMessagesValidation($parameters, $constraints);

        if (count($errorMessages) != 0){
            throw new ValidationConstraintsException($errorMessages);
        }
    }

}