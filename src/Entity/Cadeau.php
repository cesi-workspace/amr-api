<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cadeau
 *
 * @ORM\Table(name="cadeau", indexes={@ORM\Index(name="FK_CADEAU_PARTENAIRE", columns={"cadeau_partenaire_id"})})
 * @ORM\Entity
 */
class Cadeau
{
    /**
     * @var int
     *
     * @ORM\Column(name="cadeau_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cadeauId;

    /**
     * @var string
     *
     * @ORM\Column(name="cadeau_libelle", type="string", length=3000, nullable=false)
     */
    private $cadeauLibelle;

    /**
     * @var int
     *
     * @ORM\Column(name="cadeau_nbpointcout", type="integer", nullable=false)
     */
    private $cadeauNbpointcout;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="cadeau_datedebut", type="datetime", nullable=true)
     */
    private $cadeauDatedebut;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="cadeau_datefin", type="datetime", nullable=true)
     */
    private $cadeauDatefin;

    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cadeau_partenaire_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $cadeauPartenaire;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="achatCadeau")
     */
    private $achatMembrevolontaire = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->achatMembrevolontaire = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
