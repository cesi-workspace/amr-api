<?php

namespace App\Entity;

use App\Repository\UserTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"user_type")]
#[ORM\Entity(repositoryClass: UserTypeRepository::class)]
class UserType
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private int $id;

    #[ORM\Column(name:"label", type:"string", length:300, nullable:false)]
    private string $label;

    #[ORM\Column(name:"role", type:"string", length:300, nullable:false)]
    private string $role;
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
    public function getRole(): string
    {
        return $this->role;
    }
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

}