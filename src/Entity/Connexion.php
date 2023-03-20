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

    #[ORM\Column(name:"connexion_datedebut", type:"datetime", nullable:false)]
    private \DateTime $connexionDatedebut;

    #[ORM\Column(name:"connexion_datefin", type:"datetime", nullable:true)]
    private ?\DateTime $connexionDatefin;

    #[ORM\ManyToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "connexion_utilisateur_id", referencedColumnName: "utilisateur_id")]
    private ?Utilisateur $connexionUtilisateur;

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
    public function getDatedebut(): \DateTime
    {
        return $this->connexionDatedebut;
    }
    public function setDatedebut(\DateTime $connexionDatedebut): self
    {
        $this->connexionDatedebut = $connexionDatedebut;

        return $this;
    }
    public function getDatefin(): ?\DateTime
    {
        return $this->connexionDatefin;
    }
    public function setDatefin(?\DateTime $connexionDatefin): self
    {
        $this->connexionDatefin = $connexionDatefin;

        return $this;
    }
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->connexionUtilisateur;
    }
    public function setUtilisateur(?Utilisateur $connexionUtilisateur): self
    {
        $this->connexionUtilisateur = $connexionUtilisateur;

        return $this;
    }

}
