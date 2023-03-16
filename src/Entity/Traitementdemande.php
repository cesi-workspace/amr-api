<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"traitementdemande")]
#[ORM\Entity(repositoryClass: TraitementdemandeRepository::class)]
#[ORM\Index(name:"FK_TRAITEMENTDEMANDE_DEMANDE", columns:["traitementdemande_demande_id"])]
#[ORM\Index(name:"FK_TRAITEMENTDEMANDE_MEMBREVOLONTAIRE", columns:["traitementdemande_membrevolontaire_id"])]
#[ORM\Index(name:"FK_TRAITEMENTDEMANDE_TYPETRAITEMENTDEMANDE", columns:["traitementdemande_typetraitementdemande_id"])]

class Traitementdemande
{
    #[ORM\ManyToOne(targetEntity:Typetraitementdemande::class)]
    #[ORM\JoinColumn(name:"traitementdemande_typetraitementdemande_id", referencedColumnName:"typetraitementdemande_id", nullable:false)]
    private Typetraitementdemande $traitementdemandeTypetraitementdemande;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Demande::class)]
    #[ORM\JoinColumn(name:"traitementdemande_demande_id", referencedColumnName:"demande_id")]
    private ?Demande $traitementdemandeDemande;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name:"traitementdemande_membrevolontaire_id", referencedColumnName:"utilisateur_id")]
    private ?Utilisateur $traitementdemandeMembrevolontaire;

    public function getTypetraitementdemande(): Typetraitementdemande
    {
        return $this->traitementdemandeTypetraitementdemande;
    }
    public function setTypetraitementdemande(Typetraitementdemande $traitementdemandeTypetraitementdemande): self
    {
        $this->traitementdemandeTypetraitementdemande = $traitementdemandeTypetraitementdemande;

        return $this;
    }
    public function getDemande(): Demande
    {
        return $this->traitementdemandeDemande;
    }
    public function setDemande(Demande $traitementdemandeDemande): self
    {
        $this->traitementdemandeDemande = $traitementdemandeDemande;

        return $this;
    }
    public function getMembrevolontaire(): Utilisateur
    {
        return $this->traitementdemandeMembrevolontaire;
    }
    public function setMembrevolontaire(Utilisateur $traitementdemandeMembrevolontaire): self
    {
        $this->traitementdemandeMembrevolontaire = $traitementdemandeMembrevolontaire;

        return $this;
    }
}
