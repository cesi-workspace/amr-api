<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UseRepository;

#[ORM\Table(name:"needcategory")]
#[ORM\Entity(repositoryClass: NeedcategoryRepository::class)]
class Needcategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"title", type:"string", nullable:false)]
    private string $title;

    #[ORM\Column(name:"hourmin", type:"time", nullable:false)]
    private \DateTime $hourmin;

    #[ORM\Column(name:"hourmax", type:"time", nullable:false)]
    private \DateTime $hourmax;

    #[ORM\Column(name:"coefpoint", type:"integer", nullable:false)]
    private int $coefpoint;

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
    public function getHourmin(): \DateTime
    {
        return $this->hourmin;
    }
    public function setHourmin(\DateTime $hourmin): self
    {
        $this->hourmin = $hourmin;

        return $this;
    }
    public function getHourmax(): \DateTime
    {
        return $this->hourmax;
    }
    public function setHourmax(\DateTime $hourmax): self
    {
        $this->hourmax = $hourmax;

        return $this;
    }
    public function getCoefpoint(): int
    {
        return $this->coefpoint;
    }
    public function setCoefpoint(int $coefpoint): self
    {
        $this->coefpoint = $coefpoint;

        return $this;
    }
}
