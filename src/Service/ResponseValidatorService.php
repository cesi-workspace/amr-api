<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ResponseValidatorService
{

    public function __construct(public ValidatorInterface $validator){
        
    }

    public function getErrorMessagesValidation($parameters, $constraints)
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