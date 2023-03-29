<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ExistDB extends Constraint {
    public string $message1 = 'Cette valeur pour le champ \'{{ field }}\' existe déjà en base de données, elle ne doit pas exister';
    public string $message2 = 'Cette valeur pour le champ \'{{ field }}\' n\'existe pas en base de données, et elle doit exister';
    
    
    public function __construct(
        public string $entityName,
        public string $fieldName,
        public bool $isexist, // True, s'il doit exister en base, False s'il ne doit pas exister en base
        public bool $isciffer = false,
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
    /**
     * @return string
     */
    public function validatedBy(){

        return static::class.'Validator';
    }
}