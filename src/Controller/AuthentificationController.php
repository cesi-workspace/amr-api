<?php

namespace App\Controller;

use App\Entity\Connexion;
use App\Entity\Utilisateur;
use App\Service\CryptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AuthentificationController extends AbstractController
{
    #[Route('/login', name: 'authentification_login', methods: ['POST'])]
    public function login(Request $userrequest, EntityManagerInterface $em, CryptService $cryptService): Response
    {
        $connexion = new Connexion();
        $datetimenow = (new \DateTime('',new \DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s.u');
        $connexion->setDatedebut(new \DateTime($datetimenow));

        $parameters = json_decode($userrequest->getContent(), true);
        
        $user = $em->getRepository(Utilisateur::class)->findOneBy([
            'utilisateurEmail' => $cryptService->encrypt($parameters['utilisateur_email']),
            'utilisateurMotdepasse' => hash('sha512',$parameters['utilisateur_motdepasse'])
        ]);

        if($user == null){
            $connexion->setResultat(false);
            $em->persist($connexion);
            $em->flush();
            $retour = ['code' => 1, 'message' => 'Authentification échouée, vérifiez le login et le mot de passe'];
            return new JsonResponse($retour, Response::HTTP_UNAUTHORIZED);
        }

        $connexion->setUtilisateur($user);
        $connexion->setResultat(true);
        $em->persist($connexion);

        $authToken=base64_encode(random_bytes(50));
        $user->setTokenapi($authToken);
        $em->flush();

        $data = ['utilisateur_tokenapi' => $authToken];
        $retour = ['code' => 0 , 'message' => 'Authentification réussie', 'data' => $data];
        
        return new JsonResponse($retour, Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/logout', name: 'authentification_logout', methods: ['POST'])]
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

        $retour = ['code' => 0 , 'message' => 'Déconnexion réussie'];

        return new JsonResponse($retour, Response::HTTP_OK);
    }
}
