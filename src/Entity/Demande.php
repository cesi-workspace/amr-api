<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DemandeRepository;

#[ORM\Table(name:"demande")]
#[ORM\Index(name:"FK_DEMANDE_CATEGORIEDEMANDE", columns:["demande_categoriedemande_id"])]
#[ORM\Index(name:"FK_DEMANDE_MEMBREMR", columns:["demande_membremr_id"])]
#[ORM\Index(name:"FK_DEMANDE_STATUTDEMANDE", columns:["demande_statutdemande_id"])]
#[ORM\Index(name:"FK_DEMANDE_MEMBREVOLONTAIRE", columns:["demande_effectue_membrevolontaire_id"])]
#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"demande_id", type:"integer", nullable:false)]
    private int $demandeId;

    #[ORM\Column(name:"demande_titretache", type:"string", length:300, nullable:false)]
    private string $demandeTitretache;

    #[ORM\Column(name:"demande_tempsestime", type:"time", nullable:false)]
    private \DateTime $demandeTempsestime;

    #[ORM\Column(name:"demande_date", type:"datetime", nullable:false)]
    private \DateTime $demandeDate;

    #[ORM\Column(name:"demande_ville", type:"string", nullable:false)]
    private string $demandeVille;

    #[ORM\Column(name:"demande_codepostal", type:"string", nullable:false)]
    private string $demandeCodepostal;

    #[ORM\Column(name:"demande_note", type:"integer", nullable:true)]
    private ?int $demandeNote;

    #[ORM\Column(name:"demande_commentaire", type:"string", length:3000,nullable:true)]
    private ?string $demandeCommentaire;

    #[ORM\Column(name:"demande_datecommentaire", type:"datetime", nullable:true)]
    private ?\DateTime $demandeDatecommentaire;

    #[ORM\Column(name:"demande_commentaireestsignale", type:"boolean", nullable:true)]
    private ?bool $demandeCommentaireestsignale;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "demande_membremr_id", referencedColumnName: "utilisateur_id", nullable:false)]
    private Utilisateur $demandeMembremr;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "demande_effectue_membrevolontaire_id", referencedColumnName: "utilisateur_id")]
    private ?Utilisateur $demandeEffectueMembrevolontaire;

    #[ORM\ManyToOne(targetEntity: Categoriedemande::class)]
    #[ORM\JoinColumn(name: "demande_categoriedemande_id", referencedColumnName: "categoriedemande_id", nullable:false)]
    private Categoriedemande $demandeCategoriedemande;

    #[ORM\ManyToOne(targetEntity: Statutdemande::class)]
    #[ORM\JoinColumn(name: "demande_statutdemande_id", referencedColumnName: "statutdemande_id", nullable:false)]
    private Statutdemande $demandeStatutdemande;


    public function getId(): int
    {
        return $this->demandeId;
    }
    public function getTitretache(): string
    {
        return $this->demandeTitretache;
    }
    public function setTitretache(string $demandeTitretache): self
    {
        $this->demandeTitretache = $demandeTitretache;

        return $this;
    }
    public function getTempsestime(): \DateTime
    {
        return $this->demandeTempsestime;
    }
    public function setTempsestime(\DateTime $demandeTempsestime): self
    {
        $this->demandeTempsestime = $demandeTempsestime;

        return $this;
    }
    public function getDate(): \DateTime
    {
        return $this->demandeDate;
    }
    public function setDate(\DateTime $demandeDate): self
    {
        $this->demandeDate = $demandeDate;

        return $this;
    }
    public function getVille(): string
    {
        return $this->demandeVille;
    }
    public function setVille(string $demandeVille): self
    {
        $this->demandeVille = $demandeVille;

        return $this;
    }
    public function getCodepostal(): string
    {
        return $this->demandeCodepostal;
    }
    public function setCodepostal(string $demandeCodepostal): self
    {
        $this->demandeCodepostal = $demandeCodepostal;

        return $this;
    }
    public function getNote(): ?int
    {
        return $this->demandeNote;
    }
    public function setNote(?int $demandeNote): self
    {
        $this->demandeNote = $demandeNote;

        return $this;
    }
    public function getCommentaire(): ?string
    {
        return $this->demandeCommentaire;
    }
    public function setCommentaire(?string $demandeCommentaire): self
    {
        $this->demandeCommentaire = $demandeCommentaire;

        return $this;
    }
    public function getDatecommentaire(): ?\DateTime
    {
        return $this->demandeDatecommentaire;
    }
    public function setDatecommentaire(?\DateTime $demandeDatecommentaire): self
    {
        $this->demandeDatecommentaire = $demandeDatecommentaire;

        return $this;
    }
    public function getCommentaireestsignale(): ?bool
    {
        return $this->demandeCommentaireestsignale;
    }
    public function setCommentaireestsignale(?bool $demandeCommentaireestsignale): self
    {
        $this->demandeCommentaireestsignale = $demandeCommentaireestsignale;

        return $this;
    }
    public function getMembremr(): Utilisateur
    {
        return $this->demandeMembremr;
    }
    public function setMembremr(Utilisateur $demandeMembremr): self
    {
        $this->demandeMembremr = $demandeMembremr;

        return $this;
    }
    public function getEffectueMembrevolontaire(): ?Utilisateur
    {
        return $this->demandeEffectueMembrevolontaire;
    }
    public function setEffectueMembrevolontaire(?Utilisateur $demandeEffectueMembrevolontaire): self
    {
        $this->demandeEffectueMembrevolontaire = $demandeEffectueMembrevolontaire;

        return $this;
    }
    public function getCategoriedemande(): Categoriedemande
    {
        return $this->demandeCategoriedemande;
    }
    public function setCategoriedemande(Categoriedemande $demandeCategoriedemande): self
    {
        $this->demandeCategoriedemande = $demandeCategoriedemande;

        return $this;
    }
    public function getStatutdemande(): Statutdemande
    {
        return $this->demandeStatutdemande;
    }
    public function setStatutdemande(Statutdemande $demandeStatutdemande): self
    {
        $this->demandeStatutdemande = $demandeStatutdemande;

        return $this;
    }
}
