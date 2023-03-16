<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name:"Achat")]
#[ORM\Index(name:"FK_ACHAT_MEMBREVOLONTAIRE", columns:["achat_membrevolontaire_id"])]
#[ORM\Index(name:"FK_ACHAT_CADEAU", columns:["achat_cadeau_id"])]
#[ORM\Entity(repositoryClass: AchatRepository::class)]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "achat_membrevolontaire_id", referencedColumnName: "utilisateur_id")]
    private Utilisateur $achatMembreVolontaire;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Cadeau::class)]
    #[ORM\JoinColumn(name: "achat_cadeau_id", referencedColumnName: "cadeau_id")]
    private Cadeau $achatCadeau;

    #[ORM\Column(name:"achat_code", type:"string", length:200, nullable:false)]
    private string $achatCode;

    #[ORM\Column(name:"achat_date", type:"datetime", nullable:false)]
    private \DateTime $achatDate;

    public function getMembreVolontaire(): Utilisateur
    {
        return $this->achatMembreVolontaire;
    }
    public function setMembreVolontaire(Utilisateur $achatMembreVolontaire): self
    {
        $this->achatMembreVolontaire = $achatMembreVolontaire;

        return $this;
    }
    public function getCadeau(): Cadeau
    {
        return $this->achatCadeau;
    }
    public function setCadeau(Cadeau $achatCadeau): self
    {
        $this->achatCadeau = $achatCadeau;

        return $this;
    }
    public function getCode(): string
    {
        return $this->achatCode;
    }
    public function setCode(string $achatCode): self
    {
        $this->achatCode = $achatCode;

        return $this;
    }
    public function getDate(): \DateTime
    {
        return $this->achatDate;
    }
    public function setDate(\DateTime $achatDate): self
    {
        $this->achatDate = $achatDate;

        return $this;
    }
}
