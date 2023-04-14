<?php

namespace App\Entity;

use App\Repository\NeddRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: "help_request")]
#[ORM\Index(columns: ["category_id"], name: "FK_HELP_REQUEST_CATEGORY")]
#[ORM\Index(columns: ["owner_id"], name: "FK_HELP_REQUEST_OWNER")]
#[ORM\Index(columns: ["status_id"], name: "FK_HELP_REQUEST_STATUS")]
#[ORM\Index(columns: ["helper_id"], name: "FK_HELP_REQUEST_HELPER")]
#[ORM\Entity(repositoryClass: NeddRepository::class)]
class HelpRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "title", type: "string", length: 300, nullable: false)]
    private string $title;

    #[ORM\Column(name: "description", type: "string", length: 5000, nullable: true)]
    private string $description;

    #[ORM\Column(name: "estimated_delay", type: "time", nullable: false)]
    private \DateTime $estimatedDelay;

    #[ORM\Column(name: "date", type: "datetime", nullable: false)]
    private \DateTime $date;

    #[ORM\Column(name: "city", type: "string", length: 300, nullable: false)]
    private string $city;

    #[ORM\Column(name: "postal_code", type: "string", length: 300, nullable: false)]
    private string $postalCode;

    #[ORM\Column(name: "mark", type: "integer", nullable: true)]
    private ?int $mark;

    /*
     * It corresponds to the reduced mobility member, which is a user
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $owner;

    /*
     * It corresponds to the member who offers help, which is a user
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "helper_id", referencedColumnName: "id", onDelete: "SET NULL")]
    private ?User $helper;

    #[ORM\ManyToOne(targetEntity: HelpRequestCategory::class)]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id", nullable: false)]
    private HelpRequestCategory $category;

    #[ORM\ManyToOne(targetEntity: HelpRequestStatus::class)]
    #[ORM\JoinColumn(name: "status_id", referencedColumnName: "id", nullable: false)]
    private HelpRequestStatus $status;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: "created_at", type: "datetime", nullable: false)]
    private \DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name: "updated_at", type: "datetime", nullable: true)]
    private ?\DateTime $updatedAt;


    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEstimatedDelay(): \DateTime
    {
        return $this->estimatedDelay;
    }

    public function setEstimatedDelay(\DateTime $estimatedDelay): self
    {
        $this->estimatedDelay = $estimatedDelay;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getMark(): ?int
    {
        return $this->mark;
    }

    public function setMark(?int $mark): self
    {
        $this->mark = $mark;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getHelper(): ?User
    {
        return $this->helper;
    }

    public function setHelper(?User $helper): self
    {
        $this->helper = $helper;

        return $this;
    }

    public function getCategory(): HelpRequestCategory
    {
        return $this->category;
    }

    public function setCategory(HelpRequestCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStatus(): HelpRequestStatus
    {
        return $this->status;
    }

    public function setStatus(HelpRequestStatus $status): self
    {
        $this->status = $status;

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
}