<?php

namespace App\Entity;

use App\Repository\NeedstatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"needstatus")]
#[ORM\Entity(repositoryClass: NeedstatusRepository::class)]
class Needstatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"label", type:"string", length:300, nullable:false)]
    private string $label;
    public function getId(): int
    {
        return $this->id;
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
