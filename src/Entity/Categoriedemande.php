<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoriedemandeRepository;

#[ORM\Table(name:"categoriedemande")]
#[ORM\Entity(repositoryClass: CategoriedemandeRepository::class)]
class Categoriedemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"categoriedemande_id", type:"integer", nullable:false)]
    private int $categoriedemandeId;

    #[ORM\Column(name:"categoriedemande_heuremin", type:"time", nullable:false)]
    private \DateTime $categoriedemandeHeuremin;

    #[ORM\Column(name:"categoriedemande_heuremax", type:"time", nullable:false)]
    private \DateTime $categoriedemandeHeuremax;

    #[ORM\Column(name:"categoriedemande_coefpoint", type:"integer", nullable:false)]
    private int $categoriedemandeCoefpoint;

    public function getId(): int
    {
        return $this->categoriedemandeId;
    }
    public function getHeuremin(): \DateTime
    {
        return $this->categoriedemandeHeuremin;
    }
    public function setHeuremin(\DateTime $categoriedemandeHeuremin): self
    {
        $this->categoriedemandeHeuremin = $categoriedemandeHeuremin;

        return $this;
    }
    public function getHeuremax(): \DateTime
    {
        return $this->categoriedemandeHeuremax;
    }
    public function setHeuremax(\DateTime $categoriedemandeHeuremax): self
    {
        $this->categoriedemandeHeuremax = $categoriedemandeHeuremax;

        return $this;
    }
    public function getCoefpoint(): int
    {
        return $this->categoriedemandeCoefpoint;
    }
    public function setCoefpoint(int $categoriedemandeCoefpoint): self
    {
        $this->categoriedemandeCoefpoint = $categoriedemandeCoefpoint;

        return $this;
    }
}
