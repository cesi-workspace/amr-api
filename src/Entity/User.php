<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(name:"FK_USER_USERTYPE", columns:["type_id"])]
#[ORM\Index(name:"FK_USER_USERSTATUS", columns:["status_id"])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id", type:"integer")]
    private int $id;

    #[ORM\Column(name:"email", type:"string", length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity:Usertype::class)]
    #[ORM\JoinColumn(name:"type_id", referencedColumnName:"id", nullable:false)]
    private Usertype $type;

    #[ORM\Column(name:"surname", type:"string", nullable:true)]
    private ?string $surname = null;

    #[ORM\Column(name:"firstname", type:"string", nullable:true)]
    private ?string $firstname = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name:"password", type:"string")]
    private string $password;

    #[ORM\Column(name:"city", type:"string", nullable:true)]
    private ?string $city = null;

    #[ORM\Column(name:"postalcode", type:"string", nullable:true)]
    private ?string $postalcode = null;

    #[ORM\ManyToOne(targetEntity:Userstatus::class)]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id", nullable:false)]
    private Userstatus $status;

    #[ORM\Column(name:"point", type:"integer", nullable:true)]
    private ?int $point = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name:"dateinsert", type:"datetime", nullable:false)]
    private \DateTime $dateinsert;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name:"dateupdate", type:"datetime", nullable:true)]
    private ?\DateTime $dateupdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $type = $this->type;
        // guarantee every user at least has ROLE_USER
        // $Typeuser[] = 'ROLE_USER';
        return array_unique(['ROLE_USER', $type->getRole()]);
    }

    public function setRoles(Usertype $type): self
    {
        $this->type = $type;

        return $this;
    }
    public function getType(): Usertype
    {
        return $this->type;
    }

    public function setType(Usertype $type): self
    {
        $this->type = $type;

        return $this;
    }
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(?string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }
    public function getStatus(): Userstatus
    {
        return $this->status;
    }

    public function setStatus(Userstatus $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(?int $point): self
    {
        $this->point = $point;

        return $this;
    }
    public function getDateinsert(): \DateTime
    {
        return $this->dateinsert;
    }
    public function getDateupdate(): ?\DateTime
    {
        return $this->dateupdate;
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
    #[ORM\JoinColumn(name: "achat_membrevolontaire_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "achat_cadeau_id", referencedColumnName: "cadeau_id")]
    private $achatCadeau = array();

    #[ORM\ManyToMany(targetEntity:User::class, mappedBy:"favoriMembremr")]
    #[ORM\JoinTable(name: "user")]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "id", referencedColumnName: "user")]
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
