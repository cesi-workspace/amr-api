<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UseRepository;

#[ORM\Table(name:"help_request_category")]
#[ORM\Entity(repositoryClass: NeedcategoryRepository::class)]
class HelpRequestCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"title", type:"string", nullable:false)]
    private string $title;

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
}
