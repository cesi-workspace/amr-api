<?php

namespace App\Entity;

use App\Repository\StatututilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"statututilisateur")]
#[ORM\Entity(repositoryClass: StatututilisateurRepository::class)]
class Statututilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"statututilisateur_id", type:"integer", nullable:false)]
    private int $statututilisateurId;

    #[ORM\Column(name:"statututilisateur_libelle", type:"string", length:300, nullable:false)]
    private string $statututilisateurLibelle;
    public function getId(): int
    {
        return $this->statututilisateurId;
    }
    public function getLibelle(): string
    {
        return $this->statututilisateurLibelle;
    }
    public function setLibelle(string $statututilisateurLibelle): self
    {
        $this->statututilisateurLibelle = $statututilisateurLibelle;

        return $this;
    }

}
