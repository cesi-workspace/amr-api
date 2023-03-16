<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ConnexionRepository;

#[ORM\Table(name:"connexion")]
#[ORM\Entity(repositoryClass: ConnexionRepository::class)]
#[ORM\Index(name:"FK_CONNEXION_UTILISATEUR", columns:["connexion_utilisateur_id"])]

class Connexion
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"connexion_id", type:"integer", nullable:false)]
    private int $connexionId;

    #[ORM\Column(name:"connexion_resultat", type:"boolean", nullable:false)]
    private bool $connexionResultat;

    #[ORM\Column(name:"connexion_date", type:"datetime", nullable:false)]
    private \DateTime $connexionDate;

    #[ORM\ManyToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "connexion_utilisateur_id", referencedColumnName: "utilisateur_id")]
    private Utilisateur $connexionUtilisateur;

    public function getId(): int
    {
        return $this->connexionId;
    }
    public function getResultat(): bool
    {
        return $this->connexionResultat;
    }
    public function setResultat(bool $connexionResultat): self
    {
        $this->connexionResultat = $connexionResultat;

        return $this;
    }
    public function getDate(): \DateTime
    {
        return $this->connexionDate;
    }
    public function setDate(\DateTime $connexionDate): self
    {
        $this->connexionDate = $connexionDate;

        return $this;
    }
    public function getUtilisateur(): Utilisateur
    {
        return $this->connexionUtilisateur;
    }
    public function setUtilisateur(Utilisateur $connexionUtilisateur): self
    {
        $this->connexionUtilisateur = $connexionUtilisateur;

        return $this;
    }

}
