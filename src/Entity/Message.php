<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: "message")]
#[ORM\Index(columns: ["from_user_id"], name: "FK_MESSAGE_FROM_USER")]
#[ORM\Index(columns: ["to_user_id"], name: "FK_MESSAGE_TO_USER")]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "date", type: "datetime", nullable: false)]
    private \DateTime $date;

    #[ORM\Column(name: "content", type: "string", length: 3000, nullable: false)]
    private string $content;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "to_user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private User $toUser;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "from_user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private User $fromUser;

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getToUser(): User
    {
        return $this->toUser;
    }

    public function setToUser(User $toUser): self
    {
        $this->toUser = $toUser;

        return $this;
    }

    public function getFromUser(): User
    {
        return $this->fromUser;
    }

    public function setFromUser(User $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }
}
