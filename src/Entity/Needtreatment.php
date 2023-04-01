<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"needtreatment")]
#[ORM\Entity(repositoryClass: NeedtreatmentRepository::class)]
#[ORM\Index(name:"FK_NEEDTREATMENT_NEED", columns:["need_id"])]
#[ORM\Index(name:"FK_NEEDTREATMENT_VOLUNTEERMEMBER", columns:["volunteer_id"])]
#[ORM\Index(name:"FK_NEEDTREATMENT_TYPENEEDTREATMENT", columns:["needtreatmenttype_id"])]

class Needtreatment
{
    #[ORM\ManyToOne(targetEntity:Needtreatmenttype::class)]
    #[ORM\JoinColumn(name:"needtreatmenttype_id", referencedColumnName:"id", nullable:false)]
    private Needtreatmenttype $needtreatmenttype;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Need::class)]
    #[ORM\JoinColumn(name:"need_id", referencedColumnName:"id", onDelete:"CASCADE")]
    private Need $need;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name:"volunteer_id", referencedColumnName:"id", onDelete:"CASCADE")]
    private User $volunteermember;

    public function getNeedtreatmenttype(): Needtreatmenttype
    {
        return $this->needtreatmenttype;
    }
    public function setNeedtreatmenttype(Needtreatmenttype $needtreatmenttype): self
    {
        $this->needtreatmenttype = $needtreatmenttype;

        return $this;
    }
    public function getNeed(): Need
    {
        return $this->need;
    }
    public function setNeed(Need $need): self
    {
        $this->need = $need;

        return $this;
    }
    public function getvolunteermember(): User
    {
        return $this->volunteermember;
    }
    public function setvolunteermember(User $volunteermember): self
    {
        $this->volunteermember = $volunteermember;

        return $this;
    }
}
