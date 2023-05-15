<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "comment")]
#[ORM\Index(columns: ["owner_id"], name: "FK_COMMENT_OWNER")]
#[ORM\Index(columns: ["helper_id"], name: "FK_COMMENT_HELPER")]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "date", type: "datetime", nullable: false)]
    private \DateTime $date;

    #[ORM\Column(name: "content", type: "string", length: 3000, nullable: false)]
    private string $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $owner;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "helper_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $helper;

    #[ORM\Column(name: "mark", type: "integer", nullable: true)]
    private ?int $mark;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name:"created_at", type:"datetime", nullable:false)]
    private \DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name:"updated_at", type:"datetime", nullable:true)]
    private ?\DateTime $updatedAt;

    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'comment')]
    private Collection $answers;

    public function getId(): int
    {
        return $this->id;
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

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

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

    public function getHelper(): User
    {
        return $this->helper;
    }

    public function setHelper(User $helper): self
    {
        $this->helper = $helper;

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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getAnswers() : Collection
    {
        return $this->answers;
    }

    public function setAnswers(Collection $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function __construct() {
        $this->answers = new ArrayCollection();
    }

}
