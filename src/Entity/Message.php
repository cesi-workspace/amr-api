<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message", indexes={@ORM\Index(name="FK_MESSAGE_UTILISATEURTO", columns={"message_to_utilisateur_id"}), @ORM\Index(name="FK_MESSAGE_UTILISATEURFROM", columns={"message_from_utilisateur_id"})})
 * @ORM\Entity
 */
class Message
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="message_date", type="datetime", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $messageDate;

    /**
     * @var binary
     *
     * @ORM\Column(name="message_contenu", type="binary", nullable=false)
     */
    private $messageContenu;

    /**
     * @var \Utilisateur
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="message_to_utilisateur_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $messageToUtilisateur;

    /**
     * @var \Utilisateur
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="message_from_utilisateur_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $messageFromUtilisateur;


}
