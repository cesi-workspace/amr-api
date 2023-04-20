<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(columns: ["type_id"], name: "FK_USER_USERTYPE")]
#[ORM\Index(columns: ["status_id"], name: "FK_USER_USER_STATUS")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id", type:"integer")]
    private int $id;

    #[ORM\Column(name:"email", type:"string", length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity:UserType::class)]
    #[ORM\JoinColumn(name:"type_id", referencedColumnName:"id", nullable:false)]
    private UserType $type;

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

    #[ORM\Column(name:"postal_code", type:"string", nullable:true)]
    private ?string $postal_code = null;

    #[ORM\ManyToOne(targetEntity:UserStatus::class)]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id", nullable:false)]
    private UserStatus $status;

    #[ORM\Column(name:"point", type:"integer", nullable:true)]
    private ?int $point = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name:"created_at", type:"datetime", nullable:false)]
    private \DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name:"updated_at", type:"datetime", nullable:true)]
    private ?\DateTime $updatedAt;

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

    public function setRoles(UserType $type): self
    {
        $this->type = $type;

        return $this;
    }
    public function getType(): UserType
    {
        return $this->type;
    }

    public function setType(UserType $type): self
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
    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }
    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): self
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
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
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

    public function getInfo(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'firstname' => $this->getFirstname(),
            'surname' => $this->getSurname(),
            'city' => $this->getCity(),
            'postal_code' => $this->getPostalCode(),
            'userstatus' => $this->getStatus()->getLabel(),
            'usertype' => $this->getType()->getLabel(),
        ];
    }
}
