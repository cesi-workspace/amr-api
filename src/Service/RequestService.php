<?php

namespace App\Service;
use App\Exception\ValidationContraintsException;
use App\Service\Contract\IResponseValidatorService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestService
{
    private Request $request;
    public function __construct(private RequestStack $requestStack){
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function getParamJSON(): array
    {
        return $this->request->query->all();
    }

    public function getParamURL(): mixed
    {
        return json_decode($this->request->getContent(), true);
    }

}