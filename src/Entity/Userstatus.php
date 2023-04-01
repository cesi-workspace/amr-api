<?php

namespace App\Entity;

use App\Repository\UserstatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"userstatus")]
#[ORM\Entity(repositoryClass: UserstatusRepository::class)]
class Userstatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $Id;

    #[ORM\Column(name:"label", type:"string", length:300, nullable:false)]
    private string $label;
    public function getId(): int
    {
        return $this->Id;
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

}
