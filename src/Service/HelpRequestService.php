<?php

namespace App\Service;

use App\Entity\User;
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
    case ACCEPTED = 'Acceptée';
    case FINISHED = 'Terminée';
}

enum HelpRequestCategoryTitle: string
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
        private readonly IUserService $userService,
        private readonly EmailService $emailService
    ) {}

    function findOneBy(array $query, array $orderBy = []): HelpRequest |null
    {
        return $this->entityManager->getRepository(HelpRequest::class)->findOneBy($query, $orderBy);
    }
    
    function findHelpRequestTreatment(array $query, bool $single = true): HelpRequestTreatment|null|array
    {
        if($single){
            return $this->entityManager->getRepository(HelpRequestTreatment::class)->findOneBy($query);
        }else{
            return $this->entityManager->getRepository(HelpRequestTreatment::class)->findBy($query);
        }
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

    function findHelpRequestCategoryByTitle(HelpRequestCategoryTitle|string $helpRequestCategory): HelpRequestCategory|null
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
            'category' => $helpRequest->getCategory()->getTitle(),
            'status' => $helpRequest->getStatus()->getLabel(),
            'owner' => $this->userService->getInfo($helpRequest->getOwner()),
            'helper' => $helpRequest->getHelper() == null ? null : $this->userService->getInfo($helpRequest->getHelper()),
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
            'category' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)],
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
        $helpRequest->setCategory($this->findHelpRequestCategoryByTitle($parameters['category']));
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
        
        if($helpRequest->getStatus()->getLabel() != HelpRequestStatusLabel::CREATED->value)
        {
            return new JsonResponse("La demande d'aide est déjà acceptée ou terminée", Response::HTTP_BAD_REQUEST);
        }

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'type' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestTreatmentType::class, 'label', true)],
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
            $this->findHelpRequestTreatmentTypeByLabel($parameters['type'])
        );

        $this->entityManager->persist($helprequesttreatment);
        $this->entityManager->flush();
        
        return new JsonResponse(["message" => "Traitement de la demande d'aide bien enregistrée : ".$parameters['type']], Response::HTTP_OK);
    }

    function acceptHelpRequestTreatment(Request $request, HelpRequest $helpRequest, User $user) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getOwner()->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Traitement d'une demande d'aide non associé à l'utilisateur connecté interdite");
        }
        $parameters = json_decode($request->getContent(), true);

        // On récupère le traitement Acceptée de la demande d'aide associé au membre volontaire, ce traitement doit exister sinon erreur 403
        $helprequesttreatment = $this->findHelpRequestTreatment([
            'helper' => $user,
            'helpRequest' => $helpRequest,
            'type' => $this->findHelpRequestTreatmentTypeByLabel(HelpRequestTreatmentTypeLabel::ACCEPTED)
        ]);
        
        if($helpRequest->getStatus()->getLabel() != HelpRequestStatusLabel::CREATED->value)
        {
            return new JsonResponse(["message" => "La demande d'aide est déjà accepté ou terminé"], Response::HTTP_BAD_REQUEST);
        }
        if($helprequesttreatment == null)
        {
            return new JsonResponse(["message" => "Le traitement accepté sur la demande d'aide décrite n'existe pas"], Response::HTTP_BAD_REQUEST);
        }

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'accepted' => [new Assert\Type('int'), new Assert\NotBlank, new Assert\Choice([0, 1])],
            ])
        );


        if($parameters['accepted'] == "0"){
            $textEmail = "Le traitement de la demande d'aide que vous aviez acceptée a été refusé par l'utilisateur. Il s'agissait de la demande d'aide suivante : \n- Titre : ".$helpRequest->getTitle()."\n- Description : ".$helpRequest->getDescription()."\n- Catégorie : ".$helpRequest->getCategory()->getTitle()."\n- Auteur demande d'aide : ".$userconnect->getFirstname().' '.$userconnect->getSurname();
            $this->emailService->sendText(to:$user->getEmail(), subject:"Refus du traitement d'une demande d'aide", text:$textEmail);

            $this->entityManager->remove($helprequesttreatment);
            $this->entityManager->flush();

            return new JsonResponse(["message" => "Traitement de la demande d'aide bien refusé"], Response::HTTP_OK);
        }else{

            $helpRequestTreatments = $this->findHelpRequestTreatment([
                'helpRequest' => $helpRequest
            ], false);

            foreach($helpRequestTreatments as $value){
                if($value != $helprequesttreatment && $value->getType()->getLabel() != HelpRequestTreatmentTypeLabel::ACCEPTED){
                    $textEmail = "Le traitement de la demande d'aide que vous aviez acceptée a été refusé par l'utilisateur. Il s'agissait de la demande d'aide suivante : \n- Titre : ".$helpRequest->getTitle()."\n- Description : ".$helpRequest->getDescription()."\n- Catégorie : ".$helpRequest->getCategory()->getTitle()."\n- Auteur demande d'aide : ".$helpRequest->getOwner()->getFirstname().' '.$helpRequest->getOwner()->getSurname();
                    $this->emailService->sendText(to:$value->getHelper()->getEmail(), subject:"Refus du traitement d'une demande d'aide", text:$textEmail);
                }
                $this->entityManager->remove($value);
            }

            $helpRequest->setHelper($user);
            $helpRequest->setStatus($this->findHelpRequestStatusByLabel(HelpRequestStatusLabel::ACCEPTED));

            $textEmail = "Le traitement de la demande d'aide que vous aviez acceptée a été accepté par l'utilisateur. Il s'agit de la demande d'aide suivante : \n- Titre : ".$helpRequest->getTitle()."\n- Description : ".$helpRequest->getDescription()."\n- Catégorie : ".$helpRequest->getCategory()->getTitle()."\n- Auteur demande d'aide : ".$helpRequest->getOwner()->getFirstname().' '.$helpRequest->getOwner()->getSurname();
            $this->emailService->sendText(to:$user->getEmail(), subject:"Acceptation du traitement d'une demande d'aide", text:$textEmail);

            $this->entityManager->persist($helpRequest);
            $this->entityManager->flush();

            return new JsonResponse(["message" => "Traitement de la demande d'aide bien accepté"], Response::HTTP_OK);
        }

    }

    function finishHelpRequest(Request $request, HelpRequest $helpRequest) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getOwner()->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Traitement d'une demande d'aide non associé à l'utilisateur connecté interdite");
        }
        $parameters = json_decode($request->getContent(), true);

        if($helpRequest->getStatus()->getLabel() != HelpRequestStatusLabel::ACCEPTED->value)
        {
            return new JsonResponse(["message" => "La demande d'aide n'est pas acceptée ou est déjà terminé"], Response::HTTP_BAD_REQUEST);
        }
        
        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
                'real_delay' => [new Assert\Time, new Assert\NotBlank],
            ])
        );

        $helpRequest->setRealDelay(new DateTime($parameters['real_delay']));
        $helpRequest->setStatus($this->findHelpRequestStatusByLabel(HelpRequestStatusLabel::FINISHED));
        $this->entityManager->persist($helpRequest);
        $this->entityManager->flush();

        $helper = $helpRequest->getHelper();
        $helper->setPoint($helper->getPoint() + 10);
        $this->entityManager->persist($helper);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Demande d\'aide enregistrée comme terminée'], Response::HTTP_OK);

    }

    function deleteHelpRequest(HelpRequest $helpRequest) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getOwner()->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Suppression d'une demande d'aide non associé à l'utilisateur connecté interdite");
        }
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getStatus()->getLabel() != HelpRequestStatusLabel::CREATED->value)
        {
            throw new AccessDeniedException("Suppression d'une demande d'aide dont le statut n'est pas Créée est interdit");
        }

        $this->entityManager->remove($helpRequest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => "Demande d'aide supprimée avec succès"], Response::HTTP_OK);

    }

    function getHelpRequestCategories() : JsonResponse
    {
        $helpRequestCategories = $this->entityManager->getRepository(HelpRequestCategory::class)->findAll();

        $arrayHelpRequestCategories = [];
        foreach($helpRequestCategories as $key => $value){
            $arrayHelpRequestCategories[$key] = $value->getTitle();
        }

        return new JsonResponse(["message" => "Catégories des demandes d'aides récupérées", "data" => $arrayHelpRequestCategories], Response::HTTP_OK);
    }

}