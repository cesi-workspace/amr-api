<?php

namespace App\Entity;

use App\Repository\FavoriRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name:"favori")]
#[ORM\Index(name:"FK_FAVORI_MEMBREVOLONTAIRE", columns:["favori_membrevolontaire_id"])]
#[ORM\Index(name:"FK_FAVORI_MEMBREMR", columns:["favori_membremr_id"])]
#[ORM\Entity(repositoryClass: FavoriRepository::class)]
class Favori
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "favori_membrevolontaire_id", referencedColumnName: "utilisateur_id")]
    private Utilisateur $favoriMembrevolontaire;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "favori_membremr_id", referencedColumnName: "utilisateur_id")]
    private Utilisateur $favoriMembremr;

    public function getMembrevolontaire(): Utilisateur
    {
        return $this->favoriMembrevolontaire;
    }
    public function setMembrevolontaire(Utilisateur $favoriMembrevolontaire): self
    {
        $this->favoriMembrevolontaire = $favoriMembrevolontaire;

        return $this;
    }
    public function getMembremr(): Utilisateur
    {
        return $this->favoriMembremr;
    }
    public function setMembremr(Utilisateur $favoriMembremr): self
    {
        $this->favoriMembremr = $favoriMembremr;

        return $this;
    }
}
