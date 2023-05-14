<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\HelpRequest;
use App\Entity\HelpRequestCategory;
use App\Entity\HelpRequestStatus;
use App\Entity\HelpRequestTreatment;
use App\Entity\HelpRequestTreatmentType;
use App\Service\Contract\IConversationService;
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
    function findHelpRequest(array $query, bool $single = true, array $orderBy = []): HelpRequest |null|array
    {
        if($single){
            return $this->entityManager->getRepository(HelpRequest::class)->findOneBy($query, $orderBy);
        }else{
            return $this->entityManager->getRepository(HelpRequest::class)->findBy($query, $orderBy);
        }
    }
    
    function findHelpRequestTreatment(array $query, bool $single = true, array $orderBy = []): HelpRequestTreatment|null|array
    {
        if($single){
            return $this->entityManager->getRepository(HelpRequestTreatment::class)->findOneBy($query, $orderBy);
        }else{
            return $this->entityManager->getRepository(HelpRequestTreatment::class)->findBy($query, $orderBy);
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

    public function getInfo(HelpRequest $helpRequest, bool $details): array
    {
        $content = $this->apiGeo->searchCityByCoordinates($helpRequest->getLatitude(), $helpRequest->getLongitude());

        $data = [
            'id' => $helpRequest->getId(),
            'title' => $helpRequest->getTitle(),
            'estimated_delay' => $helpRequest->getEstimatedDelay()->format('H:i:s'),
            'finished_date' => $helpRequest->getFinishedDate()?->format('Y-m-d H:i:s'),
            'city' => $content[0]["nom"],
            'postal_code' => $content[0]["codesPostaux"][0],
            'description' => $helpRequest->getDescription(),
            'category' => $helpRequest->getCategory()->getTitle(),
            'status' => $helpRequest->getStatus()->getLabel(),
            'owner' => $this->userService->getInfo($helpRequest->getOwner()),
            'helper' => $helpRequest->getHelper() == null ? null : $this->userService->getInfo($helpRequest->getHelper()),
            'created_at' => $helpRequest->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        if($details){
            $helperAccepted = $this->entityManager->getRepository(User::class)->findHelperAcceptHelpRequest($helpRequest);
            $data['helpers_accept'] = $this->userService->getInfos($helperAccepted);
        }else{
            $data['nb_helpers_accept'] = $this->entityManager->getRepository(User::class)->findNbHelperAcceptHelpRequest($helpRequest);
        }

        return $data;
    }
    
    public function getInfos(array $helprequests): array
    {
        $arrayhelprequests = [];
        foreach($helprequests as $key => $value){
            $arrayhelprequests[$key] = $this->getInfo($value, false);
        }
        return $arrayhelprequests;
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
            'latitude' => [new Assert\Type('float'), new Assert\NotBlank],
            'longitude' => [new Assert\Type('float'), new Assert\NotBlank],
            'description' => [new Assert\Type('string'), new Assert\NotBlank],
            'category' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)],
            'latitude,longitude' => [new CustomAssert\CoordinatesFr]
        ]);

        $this->responseValidatorService->checkContraintsValidation($parameters, $constraints);
        
        $helpRequest=new HelpRequest();
        $helpRequest->setTitle($parameters['title']);
        $helpRequest->setEstimatedDelay(new DateTime($parameters['estimated_delay']));
        $helpRequest->setLatitude($parameters['latitude']);
        $helpRequest->setLongitude($parameters['longitude']);
        $helpRequest->setDescription($parameters['description']);
        $helpRequest->setCategory($this->findHelpRequestCategoryByTitle($parameters['category']));
        $helpRequest->setOwner($this->security->getUser());
        $helpRequest->setStatus($this->findHelpRequestStatusByLabel(HelpRequestStatusLabel::CREATED));
        $this->entityManager->persist($helpRequest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Demande créée'], Response::HTTP_CREATED);
    }

    function getHelpRequest(HelpRequest $helpRequest) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getOwner()->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Récupération de demande d'aide non associé à l'utilisateur interdite");
        }

        $data = $this->getInfo($helpRequest, true);
        
        return new JsonResponse(['message' => '', 'data' => $data], Response::HTTP_OK);
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

    function acceptHelpRequestTreatment(Request $request, HelpRequest $helpRequest) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER') && $helpRequest->getOwner()->getId()!=$userconnect->getId())
        {
            throw new AccessDeniedException("Traitement d'une demande d'aide non associé à l'utilisateur connecté interdite");
        }
        $parameters = json_decode($request->getContent(), true);
        $user = array_key_exists('helper_id', $parameters) ? $this->userService->findUser(['id' => $parameters["helper_id"]]) : null;
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
                'helper_id' => [new Assert\Type('int'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)],
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
        $helpRequest->setFinishedDate(new DateTime());
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

    function getHelpRequests(Request $request) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = $request->query->all();
        
        if(!$this->security->isGranted('ROLE_ADMIN')){

            $constraints = new Assert\Collection([
                'latitude' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\NotBlank]
                )],
                'longitude' =>  [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\NotBlank]
                )],
                'range' => [new Assert\Optional(
                    [new Assert\NotBlank, new Assert\Type('numeric'), new Assert\GreaterThanOrEqual(0)]
                )],
                'category' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)]
                )],
                'max_nb_results' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\GreaterThanOrEqual(0), new Assert\DivisibleBy(1, message: "Cette valeur doit être entière")]
                )],
            ]);
    
            $this->responseValidatorService->checkContraintsValidation($parameters, $constraints);
    
            if(array_key_exists('latitude', $parameters) && array_key_exists('longitude', $parameters) && !array_key_exists('range', $parameters)){
                $parameters['range'] = 100;
            }
            if(!array_key_exists('max_nb_results', $parameters)){
                $parameters['max_nb_results'] = 25;
            }
    
            if (!isset($parameters['status'])) $parameters['status'] = HelpRequestStatusLabel::CREATED->value;
        }else{
            
            $constraints = new Assert\Collection([
                'latitude' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\NotBlank]
                )],
                'longitude' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\NotBlank]
                )],
                'range' => [new Assert\Optional(
                    [new Assert\NotBlank, new Assert\Type('numeric'), new Assert\GreaterThanOrEqual(0)]
                )],
                'category' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)]
                )],
                'status' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestStatus::class, 'label', true)]
                )],
                'owner_id' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)]
                )],
                'helper_id' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true)]
                )],
                'max_nb_results' => [new Assert\Optional(
                    [new Assert\Type('numeric'), new Assert\GreaterThanOrEqual(0), new Assert\DivisibleBy(1, message: "Cette valeur doit être entière")]
                )],
            ]);

            $this->responseValidatorService->checkContraintsValidation($parameters, $constraints);

            if(!array_key_exists('max_nb_results', $parameters)){
                $parameters['max_nb_results'] = 25;
            }

        }
        $helpRequests = $this->entityManager->getRepository(HelpRequest::class)->findHelpRequestsByCriteria($parameters);

        if ($parameters['status'] !== HelpRequestStatusLabel::CREATED) {
            $helpRequests = array_filter($helpRequests, function($helpRequest) {
                if ($this->security->isGranted('ROLE_OWNER')) {
                    return $helpRequest['owner']['id'] === $userconnect['id'];
                } else {
                    return $helpRequest['helper']['id'] === $userconnect['id'];
                }
            })
        }

        if(count($helpRequests) == 0){
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(["message" => "Demandes d'aides récupérées", 'data' =>$this->getInfos($helpRequests)], Response::HTTP_OK);

    }

    function getOwnHelpRequests(User $user, Request $request) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = $request->query->all();

        if($user != $userconnect)
        {
            throw new AccessDeniedException("Récupération des demandes d'aides non associé à l'utilisateur connecté interdite");
        }

        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER'))
        {
            $this->responseValidatorService->checkContraintsValidation($parameters,
                new Assert\Collection([
                    'treatment_type' => [new Assert\Optional(
                        [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestTreatmentType::class, 'label', true)]
                    )],
                    'status' => [new Assert\Optional(
                        [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestStatus::class, 'label', true)]
                    )],
                    ])
            );
            
            if(count($parameters) != 1)
            {
                return new JsonResponse(["message" => "Les données ne sont pas valides", "data" => ["treatment_type" => "Ce champ est requis sauf si et seulement si l'autre champ n'est pas renseigné", "status" => "Ce champ est requis sauf si et seulement si l'autre champ n'est pas renseigné"]], Response::HTTP_BAD_REQUEST);
            }

            $helpRequests = [];
            if(array_key_exists('treatment_type', $parameters))
            {
                $helpRequests = $this->entityManager->getRepository(HelpRequest::class)->findHelpRequestByTreatmentTypeUser($parameters['treatment_type'], $user);
            }
            if(array_key_exists('status', $parameters))
            {
                $helpRequests = $this->findHelpRequest([
                    'status' => $this->findHelpRequestStatusByLabel($parameters['status']),
                    'helper' => $user
                ], false,[
                    'createdAt' => 'DESC'
                ]
            );
            }

            return new JsonResponse(["message" => "Demandes récupérées", "data" => $this->getInfos($helpRequests)], count($helpRequests) != 0 ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
        }
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER'))
        {
            $this->responseValidatorService->checkContraintsValidation($parameters,
                new Assert\Collection([
                'status' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestStatus::class, 'label', true)],
                ])
            );

            $helpRequests = $this->findHelpRequest([
                'status' => $this->findHelpRequestStatusByLabel($parameters['status']),
                'owner' => $user
            ], false,[
                'createdAt' => 'DESC'
            ]);

            return new JsonResponse(["message" => "Demandes récupérées", "data" => $this->getInfos($helpRequests)], count($helpRequests) != 0 ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    function addHelpRequestCategory(Request $request) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
            'title' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', false)],
            ])
        );

        $helpRequestCategory = new HelpRequestCategory();
        $helpRequestCategory->setTitle($parameters['title']);
        $this->entityManager->persist($helpRequestCategory);
        $this->entityManager->flush();

        return new JsonResponse(["message" => "Catégorie de demandes d'aide ajoutée"], Response::HTTP_CREATED);
        
    }
    function removeHelpRequestCategory(HelpRequestCategory $helpRequestCategory) : JsonResponse
    {
        if($this->findHelpRequest(['category' => $helpRequestCategory])){
            return new JsonResponse(["message" => "Suppression impossible car cette catégorie est associée au moins une demande d'aide"], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->remove($helpRequestCategory);
        $this->entityManager->flush();

        return new JsonResponse(["message" => "Catégorie de demandes d'aide supprimée"], Response::HTTP_OK);
    }

    function editHelpRequestCategory(Request $request, HelpRequestCategory $helpRequestCategory) : JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection([
            'title' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', false)],
            ])
        );
        $helpRequestCategory->setTitle($parameters['title']);
        $this->entityManager->persist($helpRequestCategory);
        $this->entityManager->flush();

        return new JsonResponse(["message" => "Catégorie de demandes d'aide modifiée"], Response::HTTP_OK);
    }

    

    function getHelpRequestStats(Request $request) : JsonResponse
    {
        $userconnect = $this->security->getUser();
        $parameters = $request->query->all();

        $constraints = new Assert\Collection(
            fields: [
                'latitude' => [new Assert\Type('numeric'), new Assert\NotBlank],
                'longitude' => [new Assert\Type('numeric'), new Assert\NotBlank],
                'range' => [new Assert\NotBlank, new Assert\Type('numeric'), new Assert\GreaterThanOrEqual(0)],
                'start_date' => [new Assert\Date, new Assert\NotBlank],
                'end_date' => [new Assert\Date, new Assert\NotBlank],
                'category' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(HelpRequestCategory::class, 'title', true)],
        ],allowMissingFields:true);

        $this->responseValidatorService->checkContraintsValidation($parameters, $constraints);
        
        // Si la récupération de statistiques est faite par un membre mr, il n'aura qu'accès qu'aux donnée le concernant
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_OWNER'))
        {
            $parameters['owner_id'] = $userconnect->getId();
        }

        // Si la récupération de statistiques est faite par un volontaire, il n'aura qu'accès qu'aux donnée le concernant
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->isGranted('ROLE_HELPER'))
        {
            $parameters['helper_id'] = $userconnect->getId();
        }

        $helpRequestsStats = $this->entityManager->getRepository(HelpRequest::class)->findHelpRequestsStatsByCriteria($parameters);

        if(count($helpRequestsStats) == 0){
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(["message" => "Statistiques des demandes d'aides récupérées", 'data' =>$helpRequestsStats], Response::HTTP_OK);

    }

}