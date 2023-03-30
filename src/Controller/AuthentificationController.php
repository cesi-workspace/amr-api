<?php

namespace App\Controller;

use App\Entity\Connexion;
use App\Entity\Statututilisateur;
use App\Entity\Utilisateur;
use App\Service\CryptService;
use App\Service\ResponseValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

class AuthentificationController extends AbstractController
{
    #[Route('/session', name: 'authentification_login', methods: ['POST'])]
    public function login(Request $userrequest, EntityManagerInterface $em, CryptService $cryptService, ResponseValidatorService $responseValidatorService): Response
    {
        $connexion = new Connexion();
        $datetimenow = (new \DateTime('',new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s.u');
        $connexion->setDatedebut(new \DateTime($datetimenow));

        $parameters = json_decode($userrequest->getContent(), true);
        
        $constraints = new Assert\Collection([
            'utilisateur_email' => [new Assert\Type('string'), new Assert\Email(), new Assert\NotBlank],
            'utilisateur_motdepasse' => [new Assert\Type('string'), new Assert\NotBlank]
        ]);

        $errorMessages = $responseValidatorService->getErrorMessagesValidation($parameters, $constraints);
        
        if(count($errorMessages) !=0 ){
            return new JsonResponse(['message' => 'Erreur lors de la validation des données', 'data' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy([
            'utilisateurEmail' => $cryptService->encrypt($parameters['utilisateur_email']),
            'utilisateurMotdepasse' => hash('sha512',$parameters['utilisateur_motdepasse']),
            'utilisateurStatututilisateur' => $em->getRepository(Statututilisateur::class)->find(1)
        ]);

        if($user == null){
            $connexion->setResultat(false);
            $em->persist($connexion);
            $em->flush();
            $retour = ['message' => 'Authentification échouée, vérifiez le login et le mot de passe'];
            return new JsonResponse($retour, Response::HTTP_UNAUTHORIZED);
        }

        $connexion->setUtilisateur($user);
        $connexion->setResultat(true);
        $em->persist($connexion);

        $authToken=base64_encode(random_bytes(50));
        $user->setTokenapi($authToken);
        $em->flush();

        $data = ['utilisateur_tokenapi' => $authToken];
        $retour = ['message' => 'Authentification réussie', 'data' => $data];
        
        return new JsonResponse($retour, Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/session', name: 'authentification_logout', methods: ['DELETE'])]
    public function logout(Request $userrequest, EntityManagerInterface $em): Response
    {
        $userconnect = $this->getUser();

        $connexion=$em->getRepository(Connexion::class)->findBy([
            'connexionUtilisateur'=>$userconnect,
            'connexionResultat' => true
        ], [
            'connexionDatedebut' => 'DESC'
        ],1)[0];
        
        $datetimenow = (new \DateTime('',new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s.u');
        $connexion->setDateFin(new \DateTime($datetimenow));
        
        $userconnect->setTokenapi(null);

        $em->flush();

        $retour = ['message' => 'Déconnexion réussie'];

        return new JsonResponse($retour, Response::HTTP_OK);
    }
}
