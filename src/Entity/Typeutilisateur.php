<?php

namespace App\Entity;

use App\Repository\TypeutilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"typeutilisateur")]
#[ORM\Entity(repositoryClass: TypeutilisateurRepository::class)]
class Typeutilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"typeutilisateur_id", type:"integer", nullable:false)]
    private ?int $typeutilisateurId;

    #[ORM\Column(name:"typeutilisateur_libelle", type:"string", length:300, nullable:false)]
    private ?string $typeutilisateurLibelle;
    public function getId(): ?int
    {
        return $this->typeutilisateurId;
    }
    public function getLibelle(): string
    {
        return $this->typeutilisateurLibelle;
    }
    public function setLibelle(string $typeutilisateurLibelle): self
    {
        $this->typeutilisateurLibelle = $typeutilisateurLibelle;

        return $this;
    }

}
