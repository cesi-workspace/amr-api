<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name:"message")]
#[ORM\Index(name:"FK_MESSAGE_UTILISATEURFROM", columns:["message_from_utilisateur_id"])]
#[ORM\Index(name:"FK_MESSAGE_UTILISATEURTO", columns:["message_to_utilisateur_id"])]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\Column(name:"message_date", type:"datetime", nullable:false)]
    private \DateTime $messageDate;

    #[ORM\Column(name:"message_contenu", type:"string", length:3000, nullable:false)]
    private string $messageContenu;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "message_to_utilisateur_id", referencedColumnName: "utilisateur_id")]
    private Utilisateur $messageToUtilisateur;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToOne(targetEntity:Utilisateur::class)]
    #[ORM\JoinColumn(name: "message_from_utilisateur_id", referencedColumnName: "utilisateur_id")]
    private Utilisateur $messageFromUtilisateur;

    public function getDate(): \DateTime
    {
        return $this->messageDate;
    }
    public function setDate(\DateTime $messageDate): self
    {
        $this->messageDate = $messageDate;

        return $this;
    }
    public function getContenu(): string
    {
        return $this->messageContenu;
    }
    public function setContenu(string $messageContenu): self
    {
        $this->messageContenu = $messageContenu;

        return $this;
    }
    public function getToUtilisateur(): Utilisateur
    {
        return $this->messageToUtilisateur;
    }
    public function setToUtilisateur(Utilisateur $messageToUtilisateur): self
    {
        $this->messageToUtilisateur = $messageToUtilisateur;

        return $this;
    }
    public function getFromUtilisateur(): Utilisateur
    {
        return $this->messageFromUtilisateur;
    }
    public function setFromUtilisateur(Utilisateur $messageFromUtilisateur): self
    {
        $this->messageFromUtilisateur = $messageFromUtilisateur;

        return $this;
    }
}
