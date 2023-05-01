<?php

namespace App\Service;

use App\Entity\Favorite;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Entity\UserType;
use App\Service\Contract\IResponseValidatorService;
use App\Service\Contract\IUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

enum UserStatusLabel: string
{
    case ENABLE = 'Activé';
    case REQUESTFOREACTIVATION = 'En demande';
    case DISABLED = 'Désactivé';
    case REFUSED = 'Refusé';
}

enum UserTypeLabel: string
{
    case ADMIN = 'Administrateur';
    case OWNER = 'MembreMr';
    case HELPER = 'MembreVolontaire';
    case MODERATOR = 'Modérateur';
    case PARTNER = 'Partenaire';
    case SUPERADMIN = 'Superadministrateur';
    case GOV = 'MembreEtat';
}

class UserService implements IUserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly IResponseValidatorService $responseValidatorService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EmailService $emailService
    ){}

    function isUserExists(array $findQuery): bool
    {
        return $this->entityManager->getRepository(User::class)->count($findQuery) > 0;
    }

    function findUser(array $findQuery): User|null
    {
        return $this->entityManager->getRepository(User::class)->findOneBy($findQuery);
    }

    function findUsers(array $findQuery): array | null
    {
        $usersquery = $this->entityManager->getRepository(User::class)->findBy($findQuery);
        $users = [];
        foreach($usersquery as $key => $value){
            $users[$key] = $this->getInfo($value);
        }
        return $users;
    }

    function findUserType(array $findQuery): UserType|null
    {
        return $this->entityManager->getRepository(UserType::class)->findOneBy($findQuery);
    }

    function findUserStatus(array $findQuery): UserStatus|null
    {
        return $this->entityManager->getRepository(UserStatus::class)->findOneBy($findQuery);
    }

    function findUserStatusByLabel(UserStatusLabel|string $userStatusLabel): UserStatus|null
    {
        return $this->findUserStatus([
            'label' => $userStatusLabel
        ]);
    }
    function findUserTypeByLabel(UserTypeLabel|string $userTypeLabel): UserType|null
    {
        return $this->findUserType([
            'label' => $userTypeLabel
        ]);
    }

    public function getInfo(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'surname' => $user->getSurname(),
            'city' => $user->getCity(),
            'postal_code' => $user->getPostalCode(),
            'status' => $user->getStatus()->getLabel(),
            'type' => $user->getType()->getLabel(),
        ];
    }
    function createUser(Request $request) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('city', $parameters) && array_key_exists('postal_code', $parameters)){
            $parameters['city,postal_code'] = [$parameters['city'], $parameters['postal_code']];
        }

        if(!$this->security->isGranted('ROLE_ADMIN') && array_key_exists('usertype', $parameters) && $parameters['usertype'] != UserTypeLabel::OWNER && $parameters['usertype'] != UserTypeLabel::HELPER){
            throw new AccessDeniedException("Création d'utilisateur non MembreMr et non MembreVolontaire interdite");
        }

        if(!$this->security->isGranted('ROLE_SUPERADMIN') && array_key_exists('usertype', $parameters) && ($parameters['usertype'] == UserTypeLabel::ADMIN || $parameters['usertype'] == UserTypeLabel::SUPERADMIN)){
            throw new AccessDeniedException("Création d'administrateur interdite");
        }

        $constraints = new Assert\Collection([
            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
            'email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'email', false, true)],
            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
            'password' => [new Assert\Type('string'), new Assert\NotBlank],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
            'type' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Usertype::class, 'label', true)],
            'city,postal_code' => [new CustomAssert\CityCP]
        ]);

        if($this->security->isGranted('ROLE_ADMIN')){
            $constraints = new Assert\Collection([
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'email', false, true)],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'password' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank]
                )],
                'postal_code' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank]
                )],
                'type' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(UserType::class, 'label', true)],
                'city,postal_code' => [new Assert\Optional(
                    [new CustomAssert\CityCP]
                )]
            ]);
        }

        $this->responseValidatorService->checkContraintsValidation($parameters,$constraints);

        $user = new User();
        $user->setEmail($parameters["email"]);
        $user->setSurname($parameters["surname"]);
        $user->setFirstname($parameters["firstname"]);

        if(array_key_exists('city', $parameters)){
            $user->setCity($parameters["city"]);
        }

        if(array_key_exists('postal_code', $parameters)){
            $user->setPostalcode($parameters["postal_code"]);
        }

        $user->setRoles($this->findUserTypeByLabel($parameters["user_type"]));
        
        if(!$this->security->isGranted('ROLE_ADMIN')){
            $user->setStatus($this->findUserStatusByLabel(UserStatusLabel::REQUESTFOREACTIVATION));
        }else{
            $user->setStatus($this->findUserStatusByLabel(UserStatusLabel::ENABLE));
        }

        if($parameters["type"] == UserTypeLabel::HELPER){
            $user->setPoint(0);
        }

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $parameters["password"]));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->emailService->sendText(to:$parameters["email"], subject:"Demande de création de compte", text:"Votre demande de création de compte a bien été prise en compte");

        return new JsonResponse(['message' => 'Utilisateur crée avec succès'], Response::HTTP_OK);
    }

    public function getUser(Request $request, User $user) : JsonResponse
    {

        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->getUser()->getId()!=$user->getId()){
            throw new AccessDeniedException("Récupération de données d'utilisateur différent du sein interdite");
        }
        
        $parametersURL = $request->query->all();

        $this->responseValidatorService->checkContraintsValidation($parametersURL, 
            new Assert\Collection([
                'mode' => [new Assert\Choice(["0", "1"], message:"Cette valeur doit être l'un des choix proposés : ({{ choices }})."), new Assert\NotBlank]
            ])
        );

        return new JsonResponse(['message' => 'Utilisateur récupéré', 'data' => $this->getInfo($user)], Response::HTTP_OK);

    }

    public function getUsers(Request $request) : JsonResponse
    {
        
        $parametersURL = $request->query->all();

        $this->responseValidatorService->checkContraintsValidation($parametersURL,
            new Assert\Collection([
                'fields' => [
                    'type' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(UserType::class, 'label', true, false)],
                    'status' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(UserStatus::class, 'label', true, false)],
                ],
                'allowMissingFields' => true,
            ])
        );
        
        $users = $this->findUsers($parametersURL);

        return new JsonResponse(['message' => 'Utilisateurs récupérés', 'data' => [$users]], Response::HTTP_OK);
    }

    public function editUser(Request $request, User $user) : JsonResponse
    {

        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->getUser()->getId()!=$user->getId()){
            throw new AccessDeniedException("Edition d'utilisateur différent du sein interdite");
        }
        if(!$this->security->isGranted('ROLE_SUPERADMIN') && $this->security->getUser()->getId()!=$user->getId() && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPERADMIN', $user->getRoles()))){
            throw new AccessDeniedException("Edition d'administrateur interdite");
        }
        
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('city', $parameters) && array_key_exists('postal_code', $parameters)){
            $parameters['city,postal_code'] = [$parameters['city'], $parameters['postal_code']];
        }

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection(
                fields: [
                    'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                    'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                    'city' => [new Assert\Type('string'), new Assert\NotBlank],
                    'postal_code' => [new Assert\Type('string'), new Assert\NotBlank],
                    'city,postal_code' => [new CustomAssert\CityCP]
            ],
            allowMissingFields: true)
        );

        $email = $user->getEmail();

        if(array_key_exists('surname', $parameters)){
            $user->setSurname($parameters["surname"]);
        }
        if(array_key_exists('firstname', $parameters)){
            $user->setFirstname($parameters["firstname"]);
        }
        if(array_key_exists('city', $parameters)){
            $user->setCity($parameters["city"]);
        }
        if(array_key_exists('postal_code', $parameters)){
            $user->setPostalCode($parameters["postal_code"]);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->emailService->sendText(to:$email, subject:"Modification de vos données de compte", text:"Les données de votre compte ont été modifiées");

        return new JsonResponse(['message' => 'Données de l\'utilisateur modifiée avec succès'], Response::HTTP_OK);

    }

    public function removeUser(Request $request, User $user) : JsonResponse
    {

        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->getUser()->getId()!=$user->getId()){
            throw new AccessDeniedException("Suppression d'utilisateur différent du sein interdite");
        }
        if(!$this->security->isGranted('ROLE_SUPERADMIN') && $this->security->getUser()->getId()!=$user->getId() && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPERADMIN', $user->getRoles()))){
            throw new AccessDeniedException("Suppression d'administrateur interdite");
        }

        if(!$this->security->isGranted('ROLE_ADMIN') && $this->security->getUser()->getId()!=$user->getId())
        {
            $parameters = json_decode($request->getContent(), true);

            $this->responseValidatorService->checkContraintsValidation($parameters, 
                new Assert\Collection([
                    'password' => [new Assert\Type('string'), new Assert\NotBlank]
                ])
            );

            if(!$this->userPasswordHasher->isPasswordValid($this->security->getUser(), $parameters['password']))
            {
                return new JsonResponse(['message' => 'Authentification échouée, vérifiez le login et le mot de passe'], Response::HTTP_UNAUTHORIZED);
            }

        }
        
        $email = $user->getEmail();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->emailService->sendText(to:$email, subject:"Compte supprimée", text:"Vos données ont bien été supprimées");

        return new JsonResponse(['message' => 'L\'utilisateur a bien été supprimé'], Response::HTTP_OK);
    }

    public function editStatusUser(Request $request, User $user) : JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        if(!$this->security->isGranted('ROLE_ADMIN') && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPERADMIN', $user->getRoles()) || in_array('ROLE_PARTNER', $user->getRoles()) || in_array('ROLE_MODERATOR', $user->getRoles()) || in_array('ROLE_GOV', $user->getRoles()))){
            throw new AccessDeniedException("Edition de statut d'utilisateur volontaire et membremr uniquement");
        }

        if(!$this->security->isGranted('ROLE_SUPERADMIN') && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPERADMIN', $user->getRoles()))){
            throw new AccessDeniedException("Edition de statut d'administrateur interdite");
        }
        

        $this->responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection(
                [
                    'status' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(UserStatus::class, 'label', true)]
                ])
        );

        $email = $user->getEmail();
        
        $user->setStatus($this->findUserStatusByLabel($parameters["status"]));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if($parameters["status"]==UserStatusLabel::REFUSED){
            $this->emailService->sendText(to:$email, subject:"Demande de compte AMR refusée", text:"Nous vous informons que la demande de création de compte membreMR a été refusée");
        }
        if($parameters["status"]==UserStatusLabel::ENABLE){
            $this->emailService->sendText(to:$email, subject:"Demande de compte AMR acceptée", text:"Nous vous informons que la demande de création de compte membreMR a été acceptée");
        }

        return new JsonResponse(['message' => 'Statut de l\'utilisateur mis à jour'], Response::HTTP_OK);

    }

    public function sendProofsUser(Request $request, User $user) : JsonResponse
    {
        return new JsonResponse(['message' => 'Statut de l\'utilisateur mis à jour'], Response::HTTP_OK);

    }

    public function addFavoriteUser(Request $request, User $user) : JsonResponse
    {
        if($this->security->getUser()->getId()!=$user->getId()){
            throw new AccessDeniedException("Ajout d'utilisateur en favori seulement avec utilisateur connecté");
        }

        $parameters = json_decode($request->getContent(), true);

        $this->responseValidatorService->checkContraintsValidation($parameters, 
            new Assert\Collection([
                'id' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'id', true), ]
            ])
        );

        $helper = $this->findUser([
            'id' => $parameters['id'],
            'type' => $this->findUserTypeByLabel(UserTypeLabel::HELPER)
        ]);

        if($helper == null){
            return new JsonResponse(['message' => 'Les données ne sont pas valides', 'data' => ['userid' => 'Il s\'agit pas d\'un utilisateur membrevolontaire']], Response::HTTP_BAD_REQUEST);
        }

        $favoritetest = $this->entityManager->getRepository(Favorite::class)->findBy([
            'helper' => $helper,
            'owner' => $user
        ]);

        if($favoritetest != null){
            return new JsonResponse(['message' => 'Les données ne sont pas valides', 'data' => ['id' => 'Le favori existe déjà']], Response::HTTP_BAD_REQUEST);
        }
        
        $favorite = new Favorite();
        $favorite->setOwner($user);
        $favorite->setHelper($helper);
        
        $this->entityManager->persist($favorite);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur ajouté aux favoris'], Response::HTTP_OK);

    }

    public function removeFavoriteUser(User $owner, User $helper) : JsonResponse
    {
        if($this->security->getUser()->getId()!=$owner->getId()){
            throw new AccessDeniedException("Suppression d'utilisateur en favori seulement avec utilisateur connecté");
        }

        $favorite = $this->entityManager->getRepository(Favorite::class)->findOneBy([
            'helper' => $helper,
            'owner' => $owner
        ]);

        if($favorite == null){
            return new JsonResponse(['message' => 'Ressource non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($favorite);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur supprimée des favoris'], Response::HTTP_OK);
    }
}