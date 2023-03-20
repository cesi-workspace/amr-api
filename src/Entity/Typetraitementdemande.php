<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TypetraitementdemandeRepository;

#[ORM\Table(name:"typetraitementdemande")]
#[ORM\Entity(repositoryClass: TypetraitementdemandeRepository::class)]
class Typetraitementdemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"typetraitementdemande_id", type:"integer", nullable:false)]
    private int $typetraitementdemandeId;

    #[ORM\Column(name:"typetraitementdemande_libelle", type:"string", length:300, nullable:false)]
    private string $typetraitementdemandeLibelle;

    public function getId(): ?int
    {
        return $this->typetraitementdemandeId;
    }
    public function getLibelle(): string
    {
        return $this->typetraitementdemandeLibelle;
    }
    public function setLibelle(string $typetraitementdemandeLibelle): self
    {
        $this->typetraitementdemandeLibelle = $typetraitementdemandeLibelle;

        return $this;
    }
}
