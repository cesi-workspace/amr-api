<?php

namespace App\Validator\Constraints;

use App\Service\CryptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExistDBValidator extends ConstraintValidator {

    public function __construct(public CryptService $cryptService, public EntityManagerInterface $em){

    }

    public function validate($value, Constraint $contraint){
        if (!$contraint instanceof ExistDB) {
            throw new UnexpectedTypeException($contraint, ExistDB::class);
        }

        $message = $contraint->isexist ? $contraint->message2 : $contraint->message1;

        if((!$contraint->isexist && $contraint->isciffer && $this->em->getRepository($contraint->entityName)->findBy([
            $contraint->fieldName => $this->cryptService->encrypt($value)
        ]) != null) || ($contraint->isexist && $contraint->isciffer && $this->em->getRepository($contraint->entityName)->findBy([
            $contraint->fieldName => $this->cryptService->encrypt($value)
        ]) == null)){
            $this->context->buildViolation($message)
            ->setParameter('{{ field }}', $contraint->fieldName)
            ->addViolation();
        }
        if((!$contraint->isexist && !$contraint->isciffer && $this->em->getRepository($contraint->entityName)->findBy([
            $contraint->fieldName => $value
        ]) != null) ||($contraint->isexist && !$contraint->isciffer && $this->em->getRepository($contraint->entityName)->findBy([
            $contraint->fieldName => $value
        ]) == null)){
            $this->context->buildViolation($message)
            ->setParameter('{{ field }}', $contraint->fieldName)
            ->addViolation();
        }
    }
}