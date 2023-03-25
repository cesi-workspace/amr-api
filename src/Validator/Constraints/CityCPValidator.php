<?php

namespace App\Validator\Constraints;
use App\Service\APIGeo;
use App\Service\CryptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CityCPValidator extends ConstraintValidator {

    public function __construct(public APIGeo $apigeoService){

    }

    public function validate($value, Constraint $contraint){
        if (!$contraint instanceof CityCP) {
            throw new UnexpectedTypeException($contraint, CityCP::class);
        }

        if(count($this->apigeoService->searchCity($value[0], $value[1]))==0){
            $this->context->buildViolation($contraint->message)
            ->setParameter('{{ ville }}', $value[0])
            ->setParameter('{{ codepostal }}', $value[1])
            ->addViolation();
        }
    }
}