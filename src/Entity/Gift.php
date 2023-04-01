<?php

namespace App\Entity;

use App\Repository\GiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"gift")]
#[ORM\Index(name:"FK_GIFT_PARTNER", columns:["partner_id"])]
#[ORM\Entity(repositoryClass: GiftRepository::class)]
class Gift
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"label", type:"string", length:3000, nullable:false)]
    private string $label;

    #[ORM\Column(name:"nbpointcost", type:"integer", nullable:false)]
    private int $nbpointcost;

    #[ORM\Column(name:"datebegin", type:"datetime", nullable:true)]
    private ?\DateTime $datebegin;

    #[ORM\Column(name:"dateend", type:"datetime", nullable:true)]
    private ?\DateTime $dateend;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name:"partner_id", referencedColumnName:"id", nullable:false, onDelete:"CASCADE")]
    private User $partner;

    #[ORM\ManyToMany(targetEntity:User::class, mappedBy:"gift")]
    private $volunteermembergift = array();

    public function getId(): int
    {
        return $this->id;
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
    public function getNbpointcost(): int
    {
        return $this->nbpointcost;
    }
    public function setNbpointcost(int $nbpointcost): self
    {
        $this->nbpointcost = $nbpointcost;

        return $this;
    }
    public function getDatebegin(): ?\DateTime
    {
        return $this->datebegin;
    }
    public function setDatebegin(?\DateTime $datebegin): self
    {
        $this->datebegin = $datebegin;

        return $this;
    }
    public function getDateend(): ?\DateTime
    {
        return $this->dateend;
    }
    public function setDateend(?\DateTime $dateend): self
    {
        $this->dateend = $dateend;

        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->volunteermembergift = new ArrayCollection();
    }

}
