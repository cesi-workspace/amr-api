<?php

namespace App\Entity;

use App\Repository\ConnectionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: "connection")]
#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
#[ORM\Index(columns: ["user_id"], name: "FK_CONNECTION_USER")]
class Connection
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "ip_address", type: "string", length: 300, nullable: false)]
    private string $ipAddress;

    #[ORM\Column(name: "success", type: "boolean", nullable: false)]
    private bool $success;

    #[ORM\Column(name: "login_date", type: "datetime", nullable: false)]
    private \DateTime $loginDate;

    #[ORM\Column(name: "logout_date", type: "datetime", nullable: true)]
    private ?\DateTime $logoutDate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "SET NULL")]
    private ?User $user;

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

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
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

    public function getLoginDate(): \DateTime
    {
        return $this->loginDate;
    }

    public function setLoginDate(\DateTime $loginDate): self
    {
        $this->loginDate = $loginDate;

        return $this;
    }

    public function getLogoutDate(): ?\DateTime
    {
        return $this->logoutDate;
    }

    public function setLogoutDate(?\DateTime $logoutDate): self
    {
        $this->logoutDate = $logoutDate;

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
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

}
