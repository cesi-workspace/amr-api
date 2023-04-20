<?php

namespace App\Exception;

class ValidationContraintsException extends \Exception
{

    private $data;

    public function getData() {
        return $this->data;
    }

    public function __construct ($data){
        parent::__construct("Les données ne sont pas valides");
        $this->data = $data;
    }
}