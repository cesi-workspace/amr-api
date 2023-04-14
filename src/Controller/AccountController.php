<?php

namespace App\Controller;

use App\Entity\Userstatus;
use App\Entity\User;
use App\Service\CryptService;
use App\Service\EmailService;
use App\Service\ResponseValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usertype;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class AccountController extends AbstractController
{

    // Route seulement pour ajouter des comptes MembreMr et MembreVolontaire, les autres types de comptes ne peuvent être ajoutées que par l'administrateur via la route POST /users
    #[Route('/account', name: 'account_add', methods: ['POST'])]
    public function addcompte(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('city', $parameters) && array_key_exists('postalcode', $parameters)){
            $parameters['city,postalcode'] = [$parameters['city'], $parameters['postalcode']];
        }
        
        $constraints = new Assert\Collection([
            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
            'email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank, new CustomAssert\ExistDB(User::class, 'email', false, true)],
            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
            'password' => [new Assert\Type('string'), new Assert\NotBlank],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postalcode' => [new Assert\Type('string'), new Assert\NotBlank],
            'usertype' => [new Assert\Type('string'), new Assert\NotBlank, new Assert\Choice(["MembreVolontaire", "MembreMr"], message:"Cette valeur doit être l'un des choix proposés : ({{ choices }})."), new CustomAssert\ExistDB(Usertype::class, 'label', true)],
            'city,postalcode' => [new CustomAssert\CityCP]
        ]);

        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);
        
        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($parameters["email"]);
        $user->setSurname($parameters["surname"]);
        $user->setFirstname($parameters["firstname"]);
        $user->setCity($parameters["city"]);
        $user->setPostalcode($parameters["postalcode"]);
        $user->setRoles($em->getRepository(Usertype::class)->findOneBy([
            'label' => $parameters["usertype"]
        ]));
        $user->setStatus($em->getRepository(Userstatus::class)->findOneBy([
            'label' => 'Demande d\'activation'
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
    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'account_get', methods: ['GET'])]
    public function getmycompte(Request $request, ResponseValidatorService $responseValidatorService, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager): Response
    {
        $parametersURL = $request->query->all();

        $constraints = new Assert\Collection([
            'mode' => [new Assert\Choice(["0", "1"], message:"Cette valeur doit être l'un des choix proposés : ({{ choices }})."), new Assert\NotBlank]
        ]);

        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parametersURL, $constraints);
        
        if(count($errorMessages) != 0){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        
        $userconnect = $this->getUser();
        
        $myuser = [
            'id' => $userconnect->getId(),
            'email' => $userconnect->getEmail(),
            'surname' => $userconnect->getSurname(),
            'firstname' => $userconnect->getFirstname(),
            'city' => $userconnect->getCity(),
            'postalcode' => $userconnect->getPostalcode(),
            'point' => $userconnect->getPoint(),
            'usertype' => $userconnect->getType()->getLabel(),
        ];

        return new JsonResponse(['message' => 'Utilisateur récupéré', 'data' => $myuser], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'account_edit', methods: ['PUT'])]
    public function editmycompte(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('city', $parameters) && array_key_exists('postalcode', $parameters)){
            $parameters['city,postalcode'] = [$parameters['city'], $parameters['postalcode']];
        }
        
        $constraints = new Assert\Collection(
            fields: [
            'surname' => [new Assert\Type('string'), new Assert\NotBlank],
            'firstname' => [new Assert\Type('string'), new Assert\NotBlank],
            'city' => [new Assert\Type('string'), new Assert\NotBlank],
            'postalcode' => [new Assert\Type('string'), new Assert\NotBlank],
            'city,postalcode' => [new CustomAssert\CityCP]
        ],
        allowMissingFields: true);

        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);
        
        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
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

        $emailService->sendText(to:$email, subject:"Modification de vos données de compte membreMR", text:"Vos données ont bien été modifiées");

        return new JsonResponse(['message' => 'Données de l\'utilisateur connecté avec succès'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'account_delete', methods: ['DELETE'])]
    public function removemycompte(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService, CryptService $cryptService): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $userconnect=$this->getUser();

        $constraints = new Assert\Collection([
            'password' => [new Assert\Type('string'), new Assert\NotBlank]
        ]);
        
        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);

        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $cryptService->encrypt($userconnect->getEmail()),
            'password' => hash('sha512',$parameters['password'])
        ]);

        if($user == null){
            $retour = ['message' => 'Authentification échouée, vérifiez le login et le mot de passe'];
            return new JsonResponse($retour, Response::HTTP_UNAUTHORIZED);
        }

        $userconnect = $this->getUser();

        $emailService->sendText(to:$userconnect->getEmail(), subject:"Compte AMR supprimée", text:"Vos données ont bien été supprimées");


        $em->remove($userconnect);
        $em->flush();

        return new JsonResponse(['message' => 'L\'utilisateur a bien été supprimé'], Response::HTTP_OK);
    }
}
