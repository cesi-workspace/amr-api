<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: "favorite")]
#[ORM\Index(columns: ["helper_id"], name: "FK_FAVORITE_HELPER")]
#[ORM\Index(columns: ["owner_id"], name: "FK_FAVORITE_OWNER")]
#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "helper_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private User $helper;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private User $owner;

    public function getHelper(): User
    {
        return $this->helper;
    }

    public function setHelper(User $helper): self
    {
        $this->helper = $helper;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
