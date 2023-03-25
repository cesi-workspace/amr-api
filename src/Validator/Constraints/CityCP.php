<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CityCP extends Constraint {
    public string $message = 'La ville {{ ville }} associé au code postal {{ codepostal }} n\'est pas répertoriée';
    
    public function __construct(
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