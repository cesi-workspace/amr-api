<?php

namespace App\Validator\Constraints;
use App\Service\APIGeo;
use App\Service\CryptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CoordinatesFrValidator extends ConstraintValidator {

    public function __construct(public APIGeo $apigeoService){

    }

    public function validate($value, Constraint $contraint){
        if (!$contraint instanceof CoordinatesFr) {
            throw new UnexpectedTypeException($contraint, CoordinatesFr::class);
        }

        if(count($this->apigeoService->searchCityByCoordinates($value[0], $value[1]))==0){
            $this->context->buildViolation($contraint->message)
            ->setParameter('{{ latitude }}', $value[0])
            ->setParameter('{{ longitude }}', $value[1])
            ->addViolation();
        }
    }
}