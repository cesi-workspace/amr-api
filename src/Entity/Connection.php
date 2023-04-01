<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ConnectionRepository;

#[ORM\Table(name:"connection")]
#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
#[ORM\Index(name:"FK_CONNECTION_USER", columns:["user_id"])]

class Connection
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"success", type:"boolean", nullable:false)]
    private bool $success;

    #[ORM\Column(name:"datebegin", type:"datetime", nullable:false)]
    private \DateTime $datebegin;

    #[ORM\Column(name:"dateend", type:"datetime", nullable:true)]
    private ?\DateTime $dateend;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete:"SET NULL")]
    private ?User $user;

    public function getId(): int
    {
        return $this->id;
    }
    public function getSuccess(): bool
    {
        return $this->success;
    }
    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }
    public function getDatebegin(): \DateTime
    {
        return $this->datebegin;
    }
    public function setDatebegin(\DateTime $datebegin): self
    {
        $this->datebegin = $datebegin;

        return $this;
    }
    public function getDateend(): ?\DateTime
    {
        return $this->dateend;
    }
    public function setDateend(?\DateTime $dateend): self
    {
        $this->dateend = $dateend;

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
