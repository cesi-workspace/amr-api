<?php

namespace App\Controller;

use App\Entity\Statututilisateur;
use App\Entity\Utilisateur;
use App\Service\APIGeo;
use App\Service\CryptService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Typeutilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

class CompteController extends AbstractController
{
    #[Route('/compte/addmembremr', name: 'app_compte_addmembremr')]
    public function addmembremr(Request $request, EntityManagerInterface $em, CryptService $cryptService, ValidatorInterface $validator, APIGeo $apigeoService): Response
    {
        $parameters = json_decode($request->getContent(), true);
        if(!array_key_exists('utilisateur_nom', $parameters) || !array_key_exists('utilisateur_email', $parameters) || !array_key_exists('utilisateur_prenom', $parameters) || !array_key_exists('utilisateur_motdepasse', $parameters) || !array_key_exists('utilisateur_ville', $parameters) || !array_key_exists('utilisateur_codepostal', $parameters)){
            return new JsonResponse(['code' => -1, 'message' => 'Structure des données incorrecte'], Response::HTTP_BAD_REQUEST);
        }
        if(count($parameters) != 6){
            return new JsonResponse(['code' => -1, 'message' => 'Structure des données incorrecte'], Response::HTTP_BAD_REQUEST);
        }
        $parameters['utilisateur_ville,utilisateur_codepostal'] = [$parameters['utilisateur_ville'], $parameters['utilisateur_codepostal']];

        $constraints = new Assert\Collection([
            'utilisateur_nom' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_email' => [new Assert\Type('string'), new Assert\Email(), new Assert\notBlank, new CustomAssert\UniqueDB(Utilisateur::class, 'utilisateurEmail', true)],
            'utilisateur_prenom' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_motdepasse' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_ville' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_codepostal' => [new Assert\Type('string'), new Assert\NotBlank],
            'utilisateur_ville,utilisateur_codepostal' => [new CustomAssert\CityCP]
        ]);

        $violations = $validator->validate($parameters, $constraints);
        
        $errorMessages = [];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($violations as $violation) {

            $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
        }
        if(count($errorMessages) !=0 ){
            return new JsonResponse(['code' => 1, 'message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $membremr = new Utilisateur();
        $membremr->setEmail($parameters["utilisateur_email"]);
        $membremr->setNom($parameters["utilisateur_nom"]);
        $membremr->setPrenom($parameters["utilisateur_prenom"]);
        $membremr->setPassword(hash('SHA512',$parameters["utilisateur_motdepasse"]));
        $membremr->setVille($parameters["utilisateur_ville"]);
        $membremr->setCodePostal($parameters["utilisateur_codepostal"]);
        $membremr->setRoles($em->getRepository(Typeutilisateur::class)->findOneBy([
            'typeutilisateurLibelle' => 'MembreMr'
        ]));
        $membremr->setStatututilisateur($em->getRepository(Statututilisateur::class)->findOneBy([
            'statututilisateurLibelle' => 'Demande d\'activation'
        ]));

        $em->persist($membremr);
        $em->flush();
        
        return new JsonResponse(['code' => 0, 'message' => 'MembreMr crée avec succès'], Response::HTTP_OK);
    }
}
