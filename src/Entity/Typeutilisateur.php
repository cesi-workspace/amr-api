<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typeutilisateur
 *
 * @ORM\Table(name="typeutilisateur")
 * @ORM\Entity
 */
class Typeutilisateur
{
    /**
     * @var int
     *
     * @ORM\Column(name="typeutilisateur_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typeutilisateurId;

    /**
     * @var string
     *
     * @ORM\Column(name="typeutilisateur_libelle", type="string", length=300, nullable=false)
     */
    private $typeutilisateurLibelle;


}
