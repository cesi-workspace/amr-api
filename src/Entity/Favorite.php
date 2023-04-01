<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name:"favorite")]
#[ORM\Index(name:"FK_FAVORITE_VOLUNTEERMEMBER", columns:["volunteermember_id"])]
#[ORM\Index(name:"FK_FAVORITE_RMMEMBER", columns:["rmmember_id"])]
#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name: "volunteermember_id", referencedColumnName: "id", onDelete:"CASCADE")]
    private User $volunteermember;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name: "rmmember_id", referencedColumnName: "id", onDelete:"CASCADE")]
    private User $rmmember;

    public function getVolunteermember(): User
    {
        return $this->volunteermember;
    }
    public function setVolunteermember(User $volunteermember): self
    {
        $this->volunteermember = $volunteermember;

        return $this;
    }
    public function getRmmember(): User
    {
        return $this->rmmember;
    }
    public function setRmmember(User $rmmember): self
    {
        $this->rmmember = $rmmember;

        return $this;
    }
}
