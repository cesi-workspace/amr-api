<?php

namespace App\Entity;

use App\Repository\StatutdemandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"statutdemande")]
#[ORM\Entity(repositoryClass: StatutdemandeRepository::class)]
class Statutdemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"statutdemande_id", type:"integer", nullable:false)]
    private ?int $statutdemandeId;

    #[ORM\Column(name:"statutdemande_libelle", type:"string", length:300, nullable:false)]
    private ?string $statutdemandeLibelle;
    public function getId(): ?int
    {
        return $this->statutdemandeId;
    }
    public function getLibelle(): string
    {
        return $this->statutdemandeLibelle;
    }
    public function setLibelle(string $statutdemandeLibelle): self
    {
        $this->statutdemandeLibelle = $statutdemandeLibelle;

        return $this;
    }

}
