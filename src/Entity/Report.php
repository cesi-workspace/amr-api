<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: "report")]
#[ORM\Index(columns: ["user_id"], name: "FK_REPORT_USER")]
#[ORM\Index(columns: ["comment_id"], name: "FK_REPORT_COMMENT")]
#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Comment::class)]
    #[ORM\JoinColumn(name: "comment_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private Comment $comment;
    
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $user;

    #[ORM\Column(name: "date", type: "datetime", nullable: false)]
    private \DateTime $date;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
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
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }


}
