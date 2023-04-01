<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NeddRepository;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name:"need")]
#[ORM\Index(name:"FK_NEED_NEEDCATEGORY", columns:["category_id"])]
#[ORM\Index(name:"FK_NEED_RMMEMBER", columns:["rmmember_id"])]
#[ORM\Index(name:"FK_NEED_NEEDSTATUS", columns:["status_id"])]
#[ORM\Index(name:"FK_NEED_VOLUNTEERMEMBER", columns:["do_volunteermember_id"])]
#[ORM\Entity(repositoryClass: NeddRepository::class)]
class Need
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"title", type:"string", length:300, nullable:false)]
    private string $title;

    #[ORM\Column(name:"description", type:"string", length:5000, nullable:true)]
    private string $description;

    #[ORM\Column(name:"estimatedtime", type:"time", nullable:false)]
    private \DateTime $estimatedtime;

    #[ORM\Column(name:"date", type:"datetime", nullable:false)]
    private \DateTime $date;

    #[ORM\Column(name:"city", type:"string", length:300, nullable:false)]
    private string $city;

    #[ORM\Column(name:"postalcode", type:"string", length:300, nullable:false)]
    private string $postalcode;

    #[ORM\Column(name:"mark", type:"integer", nullable:true)]
    private ?int $mark;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "rmmember_id", referencedColumnName: "id", nullable:false, onDelete:"CASCADE")]
    private User $rmmember;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "do_volunteermember_id", referencedColumnName: "id", onDelete:"SET NULL")]
    private ?User $dovolunteermember;

    #[ORM\ManyToOne(targetEntity: Needcategory::class)]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id", nullable:false)]
    private Needcategory $category;

    #[ORM\ManyToOne(targetEntity: Needstatus::class)]
    #[ORM\JoinColumn(name: "status_id", referencedColumnName: "id", nullable:false)]
    private Needstatus $status;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name:"dateinsert", type:"datetime", nullable:false)]
    private \DateTime $dateinsert;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name:"dateupdate", type:"datetime", nullable:true)]
    private ?\DateTime $dateupdate;


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
    public function getEstimatedtime(): \DateTime
    {
        return $this->estimatedtime;
    }
    public function setEstimatedtime(\DateTime $estimatedtime): self
    {
        $this->estimatedtime = $estimatedtime;

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
    public function getPostalcode(): string
    {
        return $this->postalcode;
    }
    public function setPostalcode(string $postalcode): self
    {
        $this->postalcode = $postalcode;

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
    public function getRmmember(): User
    {
        return $this->rmmember;
    }
    public function setRmmember(User $rmmember): self
    {
        $this->rmmember = $rmmember;

        return $this;
    }
    public function getDoVolunteermember(): ?User
    {
        return $this->dovolunteermember;
    }
    public function setDoVolunteermember(?User $dovolunteermember): self
    {
        $this->dovolunteermember = $dovolunteermember;

        return $this;
    }
    public function getNeedcategory(): Needcategory
    {
        return $this->category;
    }
    public function setNeedcategory(Needcategory $category): self
    {
        $this->category = $category;

        return $this;
    }
    public function getNeedstatus(): Needstatus
    {
        return $this->status;
    }
    public function setNeedstatus(Needstatus $status): self
    {
        $this->status = $status;

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
}
