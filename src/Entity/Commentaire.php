<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"commentaire")]
#[ORM\Index(name:"FK_COMMENTAIRE_UTILISATEUR", columns:["commentaire_utilisateur_id"])]
#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"commentaire_id", type:"integer", nullable:false)]
    private int $commentaireId;

    #[ORM\Column(name:"commentaire_date", type:"datetime", nullable:false)]
    private \DateTime $commentaireDate;

    #[ORM\Column(name:"commentaire_signale", type:"boolean", nullable:false)]
    private bool $commentaireSignale;

    #[ORM\ManyToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name:"commentaire_utilisateur_id", referencedColumnName:"utilisateur_id", nullable:false)]
    private Utilisateur $commentaireUtilisateur;

    public function getId(): int
    {
        return $this->commentaireId;
    }
    public function getUtilisateur(): Utilisateur
    {
        return $this->commentaireUtilisateur;
    }
    public function setUtilisateur(Utilisateur $commentaireUtilisateur): self
    {
        $this->commentaireUtilisateur = $commentaireUtilisateur;

        return $this;
    }
    public function getDate(): \DateTime
    {
        return $this->commentaireDate;
    }
    public function setDate(\DateTime $commentaireDate): self
    {
        $this->commentaireDate = $commentaireDate;

        return $this;
    }
    public function getSignale(): bool
    {
        return $this->commentaireSignale;
    }
    public function setSignale(bool $commentaireSignale): self
    {
        $this->commentaireSignale = $commentaireSignale;

        return $this;
    }

}
