<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demande
 *
 * @ORM\Table(name="demande", indexes={@ORM\Index(name="FK_DEMANDE_STATUTDEMANDE", columns={"demande_statutdemande_id"}), @ORM\Index(name="FK_DEMANDE_MEMBREVOLONTAIRE", columns={"demande_effectue_membrevolontaire_id"}), @ORM\Index(name="FK_DEMANDE_MEMBREMR", columns={"demande_membremr_id"}), @ORM\Index(name="FK_DEMANDE_CATEGORIEDEMANDE", columns={"demande_categoriedemande_id"})})
 * @ORM\Entity
 */
class Demande
{
    /**
     * @var int
     *
     * @ORM\Column(name="demande_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $demandeId;

    /**
     * @var string
     *
     * @ORM\Column(name="demande_titretache", type="string", length=300, nullable=false)
     */
    private $demandeTitretache;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demande_tempsestime", type="time", nullable=false)
     */
    private $demandeTempsestime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demande_date", type="datetime", nullable=false)
     */
    private $demandeDate;

    /**
     * @var binary
     *
     * @ORM\Column(name="demande_ville", type="binary", nullable=false)
     */
    private $demandeVille;

    /**
     * @var binary
     *
     * @ORM\Column(name="demande_codepostal", type="binary", nullable=false)
     */
    private $demandeCodepostal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="demande_note", type="integer", nullable=true)
     */
    private $demandeNote;

    /**
     * @var string|null
     *
     * @ORM\Column(name="demande_commentaire", type="string", length=3000, nullable=true)
     */
    private $demandeCommentaire;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="demande_datecommentaire", type="datetime", nullable=true)
     */
    private $demandeDatecommentaire;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="demande_commentaireestsignale", type="boolean", nullable=true)
     */
    private $demandeCommentaireestsignale;

    /**
     * @var \Utilisateur
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_membremr_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $demandeMembremr;

    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_effectue_membrevolontaire_id", referencedColumnName="utilisateur_id")
     * })
     */
    private $demandeEffectueMembrevolontaire;

    /**
     * @var \Categoriedemande
     *
     * @ORM\ManyToOne(targetEntity="Categoriedemande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_categoriedemande_id", referencedColumnName="categoriedemande_id")
     * })
     */
    private $demandeCategoriedemande;

    /**
     * @var \Statutdemande
     *
     * @ORM\ManyToOne(targetEntity="Statutdemande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_statutdemande_id", referencedColumnName="statutdemande_id")
     * })
     */
    private $demandeStatutdemande;


}
