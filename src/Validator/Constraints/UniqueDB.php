<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueDB extends Constraint {
    public string $message = 'Cette valeur pour le champ \'{{ field }}\' existe déjà en base de données, et elle doit être unique';
    
    public function __construct(
        public string $entityName,
        public string $fieldName,
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