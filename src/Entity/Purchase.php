<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name:"Purchase")]
#[ORM\Index(name:"FK_PURCHASE_VOLUNTEERMEMBER", columns:["volunteermember_id"])]
#[ORM\Index(name:"FK_PURCHASE_GIFT", columns:["gift_id"])]
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name: "volunteermember_id", referencedColumnName: "id", onDelete:"CASCADE")]
    private User $volunteermember;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Gift::class)]
    #[ORM\JoinColumn(name: "gift_id", referencedColumnName: "id", onDelete:"CASCADE")]
    private Gift $gift;

    #[ORM\Column(name:"code", type:"string", length:200, nullable:false)]
    private string $code;

    #[ORM\Column(name:"date", type:"datetime", nullable:false)]
    private \DateTime $date;

    public function getVolunteermember(): User
    {
        return $this->volunteermember;
    }
    public function setVolunteermember(User $volunteermember): self
    {
        $this->volunteermember = $volunteermember;

        return $this;
    }
    public function getGift(): Gift
    {
        return $this->gift;
    }
    public function setGift(Gift $gift): self
    {
        $this->gift = $gift;

        return $this;
    }
    public function getCode(): string
    {
        return $this->code;
    }
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
    public function getDate(): \DateTime
    {
        return $this->date;
    }
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}
