<?php

namespace App\Entity;

use App\Repository\GiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: "gift")]
#[ORM\Index(columns: ["partner_id"], name: "FK_GIFT_PARTNER")]
#[ORM\Entity(repositoryClass: GiftRepository::class)]
class Gift
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "label", type: "string", length: 3000, nullable: false)]
    private string $label;

    #[ORM\Column(name: "price", type: "integer", nullable: false)]
    private int $price;

    #[ORM\Column(name: "from_date", type: "datetime", nullable: true)]
    private ?\DateTime $fromDate;

    #[ORM\Column(name: "to_date", type: "datetime", nullable: true)]
    private ?\DateTime $toDate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "partner_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $partner;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "gift")]
    private ArrayCollection $helpers;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name:"created_at", type:"datetime", nullable:false)]
    private \DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name:"updated_at", type:"datetime", nullable:true)]
    private ?\DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFromDate(): ?\DateTime
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTime $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTime
    {
        return $this->toDate;
    }

    public function setToDate(?\DateTime $toDate): self
    {
        $this->toDate = $toDate;

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
     * Constructor
     */
    public function __construct()
    {
        $this->helpers = new ArrayCollection();
    }

}
