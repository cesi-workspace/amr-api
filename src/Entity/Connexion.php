<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connexion
 *
 * @ORM\Table(name="connexion", indexes={@ORM\Index(name="FK_CONNEXION_UTILISATEUR", columns={"connexion_utilisateur_id"})})
 * @ORM\Entity
 */
class Connexion
{
    /**
     * @var int
     *
     * @ORM\Column(name="connexion_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $connexionId;

    /**
     * @var bool
     *
     * @ORM\Column(name="connexion_resultat", type="boolean", nullable=false)
     */
    private $connexionResultat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="connexion_date", type="datetime", nullable=false)
     */
    private $connexionDate;

    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="connexion_utilisateur_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $connexionUtilisateur;


}
