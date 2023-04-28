<?php

namespace App\Service;

use App\Entity\HelpRequest;
use App\Entity\HelpRequestCategory;
use App\Entity\HelpRequestStatus;
use App\Service\Contract\IHelpRequestService;
use App\Service\Contract\IDateService;
use App\Service\Contract\IResponseValidatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
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
class HelpRequestService implements IHelpRequestService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IResponseValidatorService $responseValidatorService
    ) {}

    function findOneBy(array $query, array $orderBy = []): HelpRequest
    {
        return $this->entityManager->getRepository(HelpRequest::class)->findOneBy($query, $orderBy);
    }
    

    function findHelpRequestCategory(array $findQuery): HelpRequestCategory|null
    {
        return $this->entityManager->getRepository(HelpRequestCategory::class)->findOneBy($findQuery);
    }

    function findHelpRequestStatus(array $findQuery): HelpRequestStatus|null
    {
        return $this->entityManager->getRepository(HelpRequestStatus::class)->findOneBy($findQuery);
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
            'helprequestcategory' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)],
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

}