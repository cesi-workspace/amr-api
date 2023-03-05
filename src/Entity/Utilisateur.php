<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur", indexes={@ORM\Index(name="FK_UTILISATEUR_TYPEUTILISATEUR", columns={"utilisateur_typeutilisateur_id"})})
 * @ORM\Entity
 */
class Utilisateur
{
    /**
     * @var int
     *
     * @ORM\Column(name="utilisateur_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $utilisateurId;

    /**
     * @var binary|null
     *
     * @ORM\Column(name="utilisateur_nom", type="binary", nullable=true)
     */
    private $utilisateurNom;

    /**
     * @var binary|null
     *
     * @ORM\Column(name="utilisateur_prenom", type="binary", nullable=true)
     */
    private $utilisateurPrenom;

    /**
     * @var binary|null
     *
     * @ORM\Column(name="utilisateur_motdepasse", type="binary", nullable=true)
     */
    private $utilisateurMotdepasse;

    /**
     * @var binary
     *
     * @ORM\Column(name="utilisateur_email", type="binary", nullable=false)
     */
    private $utilisateurEmail;

    /**
     * @var binary|null
     *
     * @ORM\Column(name="utilisateur_ville", type="binary", nullable=true)
     */
    private $utilisateurVille;

    /**
     * @var binary|null
     *
     * @ORM\Column(name="utilisateur_codepostal", type="binary", nullable=true)
     */
    private $utilisateurCodepostal;

    /**
     * @var bool
     *
     * @ORM\Column(name="utilisateur_active", type="boolean", nullable=false)
     */
    private $utilisateurActive;

    /**
     * @var binary|null
     *
     * @ORM\Column(name="utilisateur_tokenapi", type="binary", nullable=true)
     */
    private $utilisateurTokenapi;

    /**
     * @var int|null
     *
     * @ORM\Column(name="utilisateur_point", type="integer", nullable=true)
     */
    private $utilisateurPoint;

    /**
     * @var \Typeutilisateur
     *
     * @ORM\ManyToOne(targetEntity="Typeutilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_typeutilisateur_id", referencedColumnName="typeutilisateur_id")
     * })
     */
    private $utilisateurTypeutilisateur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Cadeau", inversedBy="achatMembrevolontaire")
     * @ORM\JoinTable(name="achat",
     *   joinColumns={
     *     @ORM\JoinColumn(name="achat_membrevolontaire_id", referencedColumnName="utilisateur_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="achat_cadeau_id", referencedColumnName="cadeau_id")
     *   }
     * )
     */
    private $achatCadeau = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="favoriMembremr")
     */
    private $favoriMembrevolontaire = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->achatCadeau = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favoriMembrevolontaire = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
