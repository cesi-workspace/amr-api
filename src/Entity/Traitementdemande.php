<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Traitementdemande
 *
 * @ORM\Table(name="traitementdemande", indexes={@ORM\Index(name="FK_TRAITEMENTDEMANDE_TYPETRAITEMENTDEMANDE", columns={"traitementdemande_typetraitementdemande_id"}), @ORM\Index(name="FK_TRAITEMENTDEMANDE_DEMANDE", columns={"traitementdemande_demande_id"}), @ORM\Index(name="FK_TRAITEMENTDEMANDE_MEMBREVOLONTAIRE", columns={"traitementdemande_membrevolontaire_id"})})
 * @ORM\Entity
 */
class Traitementdemande
{
    /**
     * @var \Typetraitementdemande
     *
     * @ORM\ManyToOne(targetEntity="Typetraitementdemande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="traitementdemande_typetraitementdemande_id", referencedColumnName="typetraitementdemande_id")
     * })
     */
    private $traitementdemandeTypetraitementdemande;

    /**
     * @var \Demande
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Demande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="traitementdemande_demande_id", referencedColumnName="demande_id")
     * })
     */
    private $traitementdemandeDemande;

    /**
     * @var \Utilisateur
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="traitementdemande_membrevolontaire_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $traitementdemandeMembrevolontaire;


}
