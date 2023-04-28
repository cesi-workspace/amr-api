<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CoordinatesFr extends Constraint {
    public string $message = 'Les coordonnées {{ latitude }}, {{ longitude }} ne font pas référence à une ville française';
    
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