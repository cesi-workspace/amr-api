<?php

namespace App\Controller;

use App\Entity\UserStatus;
use App\Entity\User;
use App\Service\Contract\IUserService;
use App\Service\CryptService;
use App\Service\EmailService;
use App\Service\ResponseValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class UserController extends AbstractController
{

    public function __construct(
        private readonly IUserService $userService
    ){}
    
    //Route pour ajouter un utilisateur
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users', name: 'users_add', methods: ['POST'])]
    public function adduser(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if(!$this->isGranted('ROLE_SUPERADMIN') && array_key_exists('usertype', $parameters) && ($parameters['usertype'] == 'Administrateur' || $parameters['usertype'] == 'Superadministrateur')){
            throw new AccessDeniedException('Accès interdit, votre habilitation ne vous permet d\'accéder à cette route"');
        }

        if(array_key_exists('city', $parameters) && array_key_exists('postalcode', $parameters)){
            $parameters['city,postalcode'] = [$parameters['city'], $parameters['postalcode']];
        }
        if(array_key_exists('usertype', $parameters) && ($parameters['usertype'] == 'MembreMr' || $parameters['usertype'] == 'MembreVolontaire')){
            $constraints = new Assert\Collection([
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'email', false, true)],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'password' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Type('string'), new Assert\NotBlank],
                'postalcode' => [new Assert\Type('string'), new Assert\NotBlank],
                'usertype' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Usertype::class, 'label', true)],
                'city,postalcode' => [new CustomAssert\CityCP]
            ]);
        }else{
            $constraints = new Assert\Collection([
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'email', false, true)],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                'password' => [new Assert\Type('string'), new Assert\NotBlank],
                'city' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank]
                )],
                'postalcode' => [new Assert\Optional(
                    [new Assert\Type('string'), new Assert\NotBlank]
                )],
                'usertype' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Usertype::class, 'label', true)],
                'city,postalcode' => [new Assert\Optional(
                    [new CustomAssert\CityCP]
                )]
            ]);
        }

        $responseValidatorService->checkContraintsValidation($parameters, $constraints);

        $user = new User();
        $user->setEmail($parameters["email"]);
        $user->setSurname($parameters["surname"]);
        $user->setFirstname($parameters["firstname"]);
        if(array_key_exists('city', $parameters)){
            $user->setCity($parameters["city"]);
        }
        if(array_key_exists('postalcode', $parameters)){
            $user->setPostalcode($parameters["postalcode"]);
        }
        $user->setRoles($em->getRepository(Usertype::class)->findOneBy([
            'label' => $parameters["usertype"]
        ]));
        $user->setStatus($em->getRepository(Userstatus::class)->findOneBy([
            'label' => 'Actif'
        ]));

        if($parameters["usertype"] == 'MembreVolontaire'){
            $user->setPoint(0);
        }
        $user->setPassword($userPasswordHasher->hashPassword($user, $parameters["password"]));
        $em->persist($user);
        $em->flush();

        $emailService->sendText(to:$parameters["email"], subject:"Demande de création de compte membreMR", text:"Votre demande de création de compte a bien été prise en compte");

        return new JsonResponse(['message' => 'Utilisateur crée avec succès'], Response::HTTP_OK);
    }
    // Récupérer les données de l'utilisateur connecté seulement
    /*
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/users', name: 'users_get', methods: ['GET'])]
    public function getallusers(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager): Response
    {
        $users=$em->getRepository(User::class)->findAll();
        
        $parametersURL = $request->query->all();

        $constraints = new Assert\Collection([
            'fields' => [
                'usertype' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Usertype::class, 'label', true, false)],
                'userstatus' => [new Assert\Type('string'), new Assert\NotBlank, new CustomAssert\ExistDB(Userstatus::class, 'label', true, false)],
                'email' => [new Assert\Type('string'), new Assert\NotBlank],
                'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                'firstname' => [new Assert\Type('string'), new Assert\NotBlank]
            ],
            'allowMissingFields' => true,
        ]);

        return new JsonResponse(['message' => 'Utilisateurs récupérés', 'data' => $users], Response::HTTP_OK);
    }*/
    // Récupérer les données de l'utilisateur connecté seulement
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{iduser}', name: 'users_get', methods: ['GET'])]
    public function getauser(Request $request, int $iduser, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService): Response
    {
        $parametersURL = $request->query->all();

        $responseValidatorService->checkContraintsValidation($parametersURL, 
            new Assert\Collection([
                'mode' => [new Assert\Choice(["0", "1"], message:"Cette valeur doit être l'un des choix proposés : ({{ choices }})."), new Assert\NotBlank]
            ])
        );
        
        $user=$em->getRepository(User::class)->find($iduser);

        if(!$user){
            return new JsonResponse(['message' => 'Utilisateur inexistant'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'surname' => $user->getSurname(),
            'firstname' => $user->getFirstname(),
            'city' => $user->getCity(),
            'postalcode' => $user->getPostalcode(),
            'point' => $user->getPoint(),
            'usertype' => $user->getType()->getLabel(),
            'userstatus' => $user->getStatus()->getLabel()
        ];
        
        return new JsonResponse(['message' => 'Utilisateur récupéré', 'data' => $data], Response::HTTP_OK);
    }
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{iduser}', name: 'user_edit', methods: ['PUT'])]
    public function edituser(Request $request, int $iduser, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('city', $parameters) && array_key_exists('postalcode', $parameters)){
            $parameters['city,postalcode'] = [$parameters['city'], $parameters['postalcode']];
        }

        $responseValidatorService->checkContraintsValidation($parameters,
            new Assert\Collection(
                fields: [
                    'surname' => [new Assert\Type('string'), new Assert\NotBlank],
                    'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
                    'city' => [new Assert\Type('string'), new Assert\NotBlank],
                    'postalcode' => [new Assert\Type('string'), new Assert\NotBlank],
                    'city,postalcode' => [new CustomAssert\CityCP]
            ],
            allowMissingFields: true)
        );

        $user=$em->getRepository(User::class)->find($iduser);

        if(!$user){
            return new JsonResponse(['message' => 'Utilisateur inexistant'], Response::HTTP_NOT_FOUND);
        }

        $hasAccess = $this->isGranted('ROLE_SUPERADMIN');
        if(!$hasAccess && $user->getType()->getLabel() != 'Modérateur' && $user->getType()->getLabel() != 'MembreMr' && $user->getType()->getLabel() != 'MembreVolontaire' && $user->getType()->getLabel() != 'Partenaire')
        {
            $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        }

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
        if(array_key_exists('postalcode', $parameters)){
            $user->setPostalCode($parameters["postalcode"]);
        }
        $em->persist($user);
        $em->flush();

        $emailService->sendText(to:$email, subject:"Modification de vos données de compte membreMR", text:"Les données de votre compte ont été modifiées");

        return new JsonResponse(['message' => 'Données de l\'utilisateur modifiée avec succès'], Response::HTTP_OK);
    }
    /*
    #[IsGranted(['ROLE_ADMIN', 'ROLE_SUPERADMIN'])]
    #[Route('/api/users/{iduser}', name: 'user_delete', methods: ['DELETE'])]
    public function removeauser(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService, CryptService $cryptService): Response
    {
        return new JsonResponse(['message' => 'L\'utilisateur a bien été supprimé'], Response::HTTP_OK);
    }*/
}
