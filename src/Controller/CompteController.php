<?php

namespace App\Controller;

use App\Entity\Statututilisateur;
use App\Entity\Utilisateur;
use App\Service\APIGeo;
use App\Service\CryptService;
use App\Service\EmailService;
use App\Service\ResponseValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Typeutilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class CompteController extends AbstractController
{

    // Route seulement pour ajouter des comptes MembreMr et MembreVolontaire, les autres types de comptes ne peuvent être ajoutées que par l'administrateur via la route POST /users
    #[Route('/compte', name: 'compte_add', methods: ['POST'])]
    public function adduser(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('utilisateur_ville', $parameters) && array_key_exists('utilisateur_codepostal', $parameters)){
            $parameters['utilisateur_ville,utilisateur_codepostal'] = [$parameters['utilisateur_ville'], $parameters['utilisateur_codepostal']];
        }
        
        $constraints = new Assert\Collection([
            'utilisateur_nom' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank, new CustomAssert\ExistDB(Utilisateur::class, 'utilisateurEmail', false, true)],
            'utilisateur_prenom' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_motdepasse' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_ville' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_codepostal' => [new Assert\Type('string'), new Assert\NotBlank],
            'typeutilisateur_libelle' => [new Assert\Type('string'), new Assert\NotBlank, new Assert\Choice(["MembreVolontaire", "MembreMr"], message:"Cette valeur doit être l'un des choix proposés : ({{ choices }})."), new CustomAssert\ExistDB(Typeutilisateur::class, 'typeutilisateurLibelle', true)],
            'utilisateur_ville,utilisateur_codepostal' => [new CustomAssert\CityCP]
        ]);

        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);
        
        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = new Utilisateur();
        $user->setEmail($parameters["utilisateur_email"]);
        $user->setNom($parameters["utilisateur_nom"]);
        $user->setPrenom($parameters["utilisateur_prenom"]);
        $user->setPassword(hash('SHA512',$parameters["utilisateur_motdepasse"]));
        $user->setVille($parameters["utilisateur_ville"]);
        $user->setCodePostal($parameters["utilisateur_codepostal"]);
        $user->setRoles($em->getRepository(Typeutilisateur::class)->findOneBy([
            'typeutilisateurLibelle' => $parameters["typeutilisateur_libelle"]
        ]));
        $user->setStatututilisateur($em->getRepository(Statututilisateur::class)->findOneBy([
            'statututilisateurLibelle' => 'Demande d\'activation'
        ]));

        if($parameters["typeutilisateur_libelle"] == 'MembreVolontaire'){
            $user->setPoint(0);
        }

        $em->persist($user);
        $em->flush();

        $emailService->sendText(to:$parameters["utilisateur_email"], subject:"Demande de création de compte membreMR", text:"Votre demande de création de compte a bien été prise en compte");

        return new JsonResponse(['message' => 'Utilisateur crée avec succès'], Response::HTTP_OK);
    }
    // Récupérer les données de l'utilisateur connecté seulement
    #[IsGranted('ROLE_USER')]
    #[Route('/compte', name: 'compte_get', methods: ['GET'])]
    public function getmyaccount(Request $request, ResponseValidatorService $responseValidatorService): Response
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
            'utilisateur_id' => $userconnect->getId(),
            'utilisateur_email' => $userconnect->getEmail(),
            'utilisateur_nom' => $userconnect->getNom(),
            'utilisateur_prenom' => $userconnect->getPrenom(),
            'utilisateur_ville' => $userconnect->getVille(),
            'utilisateur_codepostal' => $userconnect->getCodepostal(),
            'utilisateur_point' => $userconnect->getPoint(),
            'typeutilisateur_libelle' => $userconnect->getTypeutilisateur()->getLibelle(),
        ];

        return new JsonResponse(['message' => 'Utilisateur récupéré', 'data' => $myuser], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/compte', name: 'compte_edit', methods: ['PUT'])]
    public function edituser(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if(array_key_exists('utilisateur_ville', $parameters) && array_key_exists('utilisateur_codepostal', $parameters)){
            $parameters['utilisateur_ville,utilisateur_codepostal'] = [$parameters['utilisateur_ville'], $parameters['utilisateur_codepostal']];
        }
        
        $constraints = new Assert\Collection(
            fields: [
            'utilisateur_nom' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_prenom' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_ville' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_codepostal' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_ville,utilisateur_codepostal' => [new CustomAssert\CityCP]
        ],
        allowMissingFields: true);

        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);
        
        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $email = $user->getEmail();
        if(array_key_exists('utilisateur_nom', $parameters)){
            $user->setNom($parameters["utilisateur_nom"]);
        }
        if(array_key_exists('utilisateur_prenom', $parameters)){
            $user->setPrenom($parameters["utilisateur_prenom"]);
        }
        if(array_key_exists('utilisateur_ville', $parameters)){
            $user->setVille($parameters["utilisateur_ville"]);
        }
        if(array_key_exists('utilisateur_codepostal', $parameters)){
            $user->setCodePostal($parameters["utilisateur_codepostal"]);
        }
        $em->persist($user);
        $em->flush();

        $emailService->sendText(to:$email, subject:"Modification de vos données de compte membreMR", text:"Vos données ont bien été modifiées");

        return new JsonResponse(['message' => 'Données de l\'utilisateur connecté avec succès'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/compte', name: 'compte_delete', methods: ['DELETE'])]
    public function removeuser(Request $request, EntityManagerInterface $em, ResponseValidatorService $responseValidatorService, EmailService $emailService, CryptService $cryptService): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $userconnect=$this->getUser();

        $constraints = new Assert\Collection([
            'utilisateur_motdepasse' => [new Assert\Type('string'), new Assert\NotBlank]
        ]);
        
        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);

        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy([
            'utilisateurEmail' => $cryptService->encrypt($userconnect->getEmail()),
            'utilisateurMotdepasse' => hash('sha512',$parameters['utilisateur_motdepasse'])
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
