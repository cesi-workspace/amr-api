<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: "answer")]
#[ORM\Index(columns: ["user_id"], name: "FK_ANSWER_USER")]
#[ORM\Index(columns: ["comment_id"], name: "FK_ANSWER_COMMENT")]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Comment::class)]
    #[ORM\JoinColumn(name: "comment_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private Comment $comment;

    #[ORM\Column(name: "date", type: "datetime", nullable: false)]
    private \DateTime $date;

    #[ORM\Column(name: "content", type: "string", length: 3000, nullable: false)]
    private string $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function setComment(Comment $comment): self
    {
        $this->comment = $comment;

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

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
