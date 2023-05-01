<?php

namespace App\Service;

use App\Entity\HelpRequest;
use App\Entity\HelpRequestCategory;
use App\Entity\HelpRequestStatus;
use App\Entity\HelpRequestTreatment;
use App\Entity\HelpRequestTreatmentType;
use App\Service\Contract\IHelpRequestService;
use App\Service\Contract\IDateService;
use App\Service\Contract\IResponseValidatorService;
use App\Service\Contract\IUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;


enum HelpRequestStatusLabel: string
{
    case CREATED = 'Créée';
    case ACCEPTED = 'Accepté';
    case FINISHED = 'Terminé';
}

enum HelpRequestCategoryLabel: string
{
    case HOUSEWORKS = 'Tâches ménagères';
    case GREENSPACES = 'Espaces verts';
    case SHOPPING = 'Courses';
    case ITSUPPORT = 'Soutien informatique';
    case TRANSPORTS = 'Transports';
    case DIY = 'Bricolage';
    case FOOD = 'Alimentation';
}
enum HelpRequestTreatmentTypeLabel: string
{
    case FAVORITED = 'Favorisée';
    case ACCEPTED = 'Acceptée';
    case REFUSED = 'Refusée';
}
class HelpRequestService implements IHelpRequestService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IResponseValidatorService $responseValidatorService,
        private readonly APIGeo $apiGeo,
        private readonly IUserService $userService
    ) {}

    function findOneBy(array $query, array $orderBy = []): HelpRequest
    {
        return $this->entityManager->getRepository(HelpRequest::class)->findOneBy($query, $orderBy);
    }
    
    function findHelpRequestTreatment(array $query): HelpRequestTreatment|null
    {
        return $this->entityManager->getRepository(HelpRequestTreatment::class)->findOneBy($query);
    }

    function findHelpRequestCategory(array $findQuery): HelpRequestCategory|null
    {
        return $this->entityManager->getRepository(HelpRequestCategory::class)->findOneBy($findQuery);
    }

    function findHelpRequestStatus(array $findQuery): HelpRequestStatus|null
    {
        return $this->entityManager->getRepository(HelpRequestStatus::class)->findOneBy($findQuery);
    }

    function findHelpRequestTreatmentType(array $findQuery): HelpRequestTreatmentType|null
    {
        return $this->entityManager->getRepository(HelpRequestTreatmentType::class)->findOneBy($findQuery);
    }

    function findHelpRequestCategoryByTitle(HelpRequestCategory|string $helpRequestCategory): HelpRequestCategory|null
    {
        return $this->findHelpRequestCategory([
            'title' => $helpRequestCategory
        ]);
    }

    function findHelpRequestStatusByLabel(HelpRequestStatusLabel|string $helpRequestStatusLabel): HelpRequestStatus|null
    {
        return $this->findHelpRequestStatus([
            'label' => $helpRequestStatusLabel
        ]);
    }

    function findHelpRequestTreatmentTypeByLabel(HelpRequestTreatmentTypeLabel|string $helpRequestTreatmentTypeLabel): HelpRequestTreatmentType|null
    {
        return $this->findHelpRequestTreatmentType([
            'label' => $helpRequestTreatmentTypeLabel
        ]);
    }

    public function getInfo(HelpRequest $helpRequest): array
    {
        $content = $this->apiGeo->searchCityByCoordinates($helpRequest->getLatitude(), $helpRequest->getLongitude());

        return [
            'id' => $helpRequest->getId(),
            'title' => $helpRequest->getTitle(),
            'estimated_delay' => $helpRequest->getEstimatedDelay()->format('H:i:s'),
            'date' => $helpRequest->getDate()->format('Y-m-d H:i:s'),
            'city' => $content[0]["nom"],
            'postal_code' => $content[0]["codesPostaux"][0],
            'description' => $helpRequest->getDescription(),
            'help_request_category' => $helpRequest->getCategory()->getTitle(),
            'help_request_status' => $helpRequest->getStatus()->getLabel(),
            'help_request_owner' => $this->userService->getInfo($helpRequest->getOwner()),
            'help_request_helper' => $helpRequest->getHelper() == null ? null : $this->userService->getInfo($helpRequest->getHelper()),
        ];
    }

    function createHelprequest(Request $request) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        
        if(array_key_exists('latitude', $parameters) && array_key_exists('longitude', $parameters)){
            $parameters['latitude,longitude'] = [$parameters['latitude'], $parameters['longitude']];
        }

        $constraints = new Assert\Collection([
            'title' => [new Assert\Type('string'), new Assert\NotBlank],
            'estimated_delay' => [new Assert\Time, new Assert\NotBlank],
            'date' => [new Assert\Date, new Assert\NotBlank],
            'latitude' => [new Assert\Type('float'), new Assert\NotBlank],
            'longitude' => [new Assert\Type('float'), new Assert\NotBlank],
            'description' => [new Assert\Type('string'), new Assert\NotBlank],
            'help_request_category' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)],
            'latitude,longitude' => [new CustomAssert\CoordinatesFr]
        ]);

        $this->responseValidatorService->checkContraintsValidation($parameters, $constraints);
        
        $helpRequest=new HelpRequest();
        $helpRequest->setTitle($parameters['title']);
        $helpRequest->setDate(new DateTime($parameters['date']));
        $helpRequest->setEstimatedDelay(new DateTime($parameters['estimated_delay']));
        $helpRequest->setLatitude($parameters['latitude']);
        $helpRequest->setLongitude($parameters['longitude']);
        $helpRequest->setDescription($parameters['description']);
        $helpRequest->setCategory($this->findHelpRequestCategoryByTitle($parameters['helprequestcategory']));
        $helpRequest->setOwner($this->security->getUser());
        $helpRequest->setStatus($this->findHelpRequestStatusByLabel(HelpRequestStatusLabel::CREATED));
        $this->entityManager->persist($helpRequest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Demande créée'], Response::HTTP_OK);
    }

    function getHelpRequest(HelpRequest $helpRequest) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER') && $helpRequest->getStatus()->getLabel() != HelpRequestStatusLabel::CREATED->value && $helpRequest->getHelper()?->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Récupération de demande d'aide non créée et non associé à l'utilisateur interdite");
        }
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getOwner()->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Récupération de demande d'aide non associé à l'utilisateur interdite");
        }

        $data = $this->getInfo($helpRequest);
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    function postHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = json_decode($request->getContent(), true);
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER') && $helpRequest->getStatus()->getLabel() != HelpRequestStatusLabel::CREATED->value)
        {
            throw new AccessDeniedException("Traitement sur une demande d'aide non créée interdite");
        }

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'help_request_treatment_type' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestTreatmentType::class, 'label', true)],
            ])
        );

        $helprequesttreatment = $this->findHelpRequestTreatment([
            'helper' => $userconnect,
            'helpRequest' => $helpRequest
        ]);

        if($helprequesttreatment == null){
            $helprequesttreatment = new HelpRequestTreatment();
            $helprequesttreatment->setHelper($userconnect);
            $helprequesttreatment->setHelpRequest($helpRequest);
        }

        $helprequesttreatment->setType(
            $this->findHelpRequestTreatmentTypeByLabel($parameters['help_request_treatment_type'])
        );

        $this->entityManager->persist($helprequesttreatment);
        $this->entityManager->flush();
        
        return new JsonResponse(["message" => "Traitement de la demande d'aide bien enregistrée : ".$parameters['help_request_treatment_type']], Response::HTTP_OK);
    }

}