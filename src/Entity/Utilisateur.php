<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Index(name:"FK_UTILISATEUR_TYPEUTILISATEUR", columns:["utilisateur_typeutilisateur_id"])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"utilisateur_id")]
    private int $utilisateurId;

    #[ORM\Column(name:"utilisateur_email", length: 180, unique: true)]
    private ?string $utilisateurEmail = null;

    #[ORM\ManyToOne(targetEntity:Typeutilisateur::class)]
    #[ORM\JoinColumn(name:"utilisateur_typeutilisateur_id", referencedColumnName:"typeutilisateur_id", nullable:false)]
    private Typeutilisateur $utilisateurTypeutilisateur;

    #[ORM\Column(name:"utilisateur_nom", nullable:true)]
    private ?string $utilisateurNom = null;

    #[ORM\Column(name:"utilisateur_prenom", nullable:true)]
    private ?string $utilisateurPrenom = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name:"utilisateur_motdepasse")]
    private ?string $utilisateurMotdepasse = null;

    #[ORM\Column(name:"utilisateur_ville", nullable:true)]
    private ?string $utilisateurVille = null;

    #[ORM\Column(name:"utilisateur_codepostal", nullable:true)]
    private ?string $utilisateurCodepostal = null;

    #[ORM\Column(name:"utilisateur_active")]
    private ?bool $utilisateurActive = null;

    #[ORM\Column(name:"utilisateur_tokenapi", nullable:true)]
    public ?string $utilisateurTokenapi = null;

    #[ORM\Column(name:"utilisateur_point", nullable:true)]
    private ?int $utilisateurPoint = null;

    public function getId(): ?int
    {
        return $this->utilisateurId;
    }

    public function getEmail(): ?string
    {
        return $this->utilisateurEmail;
    }

    public function setEmail(?string $utilisateurEmail): self
    {
        $this->utilisateurEmail = $utilisateurEmail;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->utilisateurEmail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $utilisateurTypeutilisateur = $this->utilisateurTypeutilisateur;
        // guarantee every user at least has ROLE_USER
        // $utilisateurTypeutilisateur[] = 'ROLE_USER';
        return array_unique(['ROLE_USER', $utilisateurTypeutilisateur->getLibelle()]);
    }

    public function setRoles(Typeutilisateur $utilisateurTypeutilisateur): self
    {
        $this->utilisateurTypeutilisateur = $utilisateurTypeutilisateur;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->utilisateurMotdepasse;
    }

    public function setPassword(string $utilisateurMotdepasse): self
    {
        $this->utilisateurMotdepasse = $utilisateurMotdepasse;

        return $this;
    }
    public function getVille(): ?string
    {
        return $this->utilisateurVille;
    }

    public function setVille(?string $utilisateurVille): self
    {
        $this->utilisateurVille = $utilisateurVille;

        return $this;
    }
    public function getCodePostal(): ?string
    {
        return $this->utilisateurCodepostal;
    }

    public function setCodePostal(?string $utilisateurCodepostal): self
    {
        $this->utilisateurCodepostal = $utilisateurCodepostal;

        return $this;
    }
    public function getActive(): ?bool
    {
        return $this->utilisateurActive;
    }

    public function setActive(?bool $utilisateurActive): self
    {
        $this->utilisateurActive = $utilisateurActive;

        return $this;
    }
    public function getTokenapi(): ?string
    {
        return $this->utilisateurTokenapi;
    }

    public function setTokenapi(?string $utilisateurTokenapi): self
    {
        $this->utilisateurTokenapi = $utilisateurTokenapi;

        return $this;
    }
    public function getPoint(): ?int
    {
        return $this->utilisateurPoint;
    }

    public function setPoint(?int $utilisateurPoint): self
    {
        $this->utilisateurPoint = $utilisateurPoint;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /*
    #[ORM\ManyToMany(targetEntity:Cadeau::class, inversedBy:"achatMembrevolontaire")]
    #[ORM\JoinTable(name: "achat")]
    #[ORM\JoinColumn(name: "achat_membrevolontaire_id", referencedColumnName: "utilisateur_id")]
    #[ORM\InverseJoinColumn(name: "achat_cadeau_id", referencedColumnName: "cadeau_id")]
    private $achatCadeau = array();

    #[ORM\ManyToMany(targetEntity:Utilisateur::class, mappedBy:"favoriMembremr")]
    #[ORM\JoinTable(name: "utilisateur")]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id")]
    #[ORM\InverseJoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur")]
    private $favoriMembrevolontaire = array();
    */

    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->achatCadeau = new ArrayCollection();
        //$this->favoriMembrevolontaire = new ArrayCollection();
    }
}
