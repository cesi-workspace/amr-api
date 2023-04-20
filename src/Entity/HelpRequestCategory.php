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

    #[ORM\Column(name:"limit_minimum_delay", type:"time", nullable:false)]
    private \DateTime $limitMinimumDelay;

    #[ORM\Column(name:"limit_maximum_delay", type:"time", nullable:false)]
    private \DateTime $limitMaximumDelay;

    #[ORM\Column(name:"coefficient", type:"integer", nullable:false)]
    private int $coefficient;

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
    public function getLimitMinimumDelay(): \DateTime
    {
        return $this->limitMinimumDelay;
    }
    public function setLimitMinimumDelay(\DateTime $limitMinimumDelay): self
    {
        $this->limitMinimumDelay = $limitMinimumDelay;

        return $this;
    }
    public function getLimitMaximumDelay(): \DateTime
    {
        return $this->limitMaximumDelay;
    }
    public function setLimitMaximumDelay(\DateTime $limitMaximumDelay): self
    {
        $this->limitMaximumDelay = $limitMaximumDelay;

        return $this;
    }
    public function getCoefficient(): int
    {
        return $this->coefficient;
    }
    public function setCoefficient(int $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }
}
