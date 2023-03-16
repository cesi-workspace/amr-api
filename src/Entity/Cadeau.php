<?php

namespace App\Entity;

use App\Repository\CadeauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"cadeau")]
#[ORM\Index(name:"FK_CADEAU_PARTENAIRE", columns:["cadeau_partenaire_id"])]
#[ORM\Entity(repositoryClass: CadeauRepository::class)]
class Cadeau
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"cadeau_id", type:"integer", nullable:false)]
    private int $cadeauId;

    #[ORM\Column(name:"cadeau_libelle", type:"string", length:3000, nullable:false)]
    private string $cadeauLibelle;

    #[ORM\Column(name:"cadeau_nbpointcout", type:"integer", nullable:false)]
    private int $cadeauNbpointcout;

    #[ORM\Column(name:"cadeau_datedebut", type:"datetime", nullable:true)]
    private ?\DateTime $cadeauDatedebut;

    #[ORM\Column(name:"cadeau_datefin", type:"datetime", nullable:true)]
    private ?\DateTime $cadeauDatefin;

    #[ORM\ManyToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name:"cadeau_partenaire_id", referencedColumnName:"utilisateur_id", nullable:false)]
    private Utilisateur $cadeauPartenaire;

    #[ORM\ManyToMany(targetEntity:Utilisateur::class, mappedBy:"achatCadeau")]
    private $achatMembrevolontaire = array();

    public function getId(): int
    {
        return $this->cadeauId;
    }
    public function getLibelle(): string
    {
        return $this->cadeauLibelle;
    }
    public function setLibelle(string $cadeauLibelle): self
    {
        $this->cadeauLibelle = $cadeauLibelle;

        return $this;
    }
    public function getNbpointcout(): int
    {
        return $this->cadeauNbpointcout;
    }
    public function setNbpointcout(int $cadeauNbpointcout): self
    {
        $this->cadeauNbpointcout = $cadeauNbpointcout;

        return $this;
    }
    public function getDatedebut(): ?\DateTime
    {
        return $this->cadeauDatedebut;
    }
    public function setDatedebut(?\DateTime $cadeauDatedebut): self
    {
        $this->cadeauDatedebut = $cadeauDatedebut;

        return $this;
    }
    public function getDatefin(): ?\DateTime
    {
        return $this->cadeauDatefin;
    }
    public function setDatefin(?\DateTime $cadeauDatefin): self
    {
        $this->cadeauDatefin = $cadeauDatefin;

        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->achatMembrevolontaire = new ArrayCollection();
    }

}
