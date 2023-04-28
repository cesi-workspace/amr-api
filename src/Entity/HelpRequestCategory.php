<?php

namespace App\Entity;

use App\Repository\HelpRequestCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name:"help_request_category")]
#[ORM\Entity(repositoryClass: HelpRequestCategoryRepository::class)]
class HelpRequestCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"title", type:"string", nullable:false)]
    private string $title;

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
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;

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
