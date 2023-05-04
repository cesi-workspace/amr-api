<?php

namespace App\Service;
use App\Exception\ValidationContraintsException;
use App\Service\Contract\IResponseValidatorService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestAmrService
{
    private Request $request;
    public function __construct(private RequestStack $requestStack){
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function getParamURL(): array
    {
        return $this->request->query->all();
    }

    public function getParamJSON(): mixed
    {
        return json_decode($this->request->getContent(), true);
    }

    public function getJsonResponseOk(string $message, $data = null, string $messageEmpty = "") : JsonResponse
    {
        if($data !== null){
            if(count($data) == 0){
                return new JsonResponse(["message" => $messageEmpty], Response::HTTP_NO_CONTENT);
            }
            return new JsonResponse(["message" => $message, "data" => $data], Response::HTTP_OK);
        }

        if($this->request->getMethod() == 'POST'){
            return new JsonResponse(["message" => $message], Response::HTTP_CREATED);
        }

        return new JsonResponse(["message" => $message], Response::HTTP_OK);
        
    }

}