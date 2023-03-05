<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statutdemande
 *
 * @ORM\Table(name="statutdemande")
 * @ORM\Entity
 */
class Statutdemande
{
    /**
     * @var int
     *
     * @ORM\Column(name="statutdemande_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $statutdemandeId;

    /**
     * @var string
     *
     * @ORM\Column(name="statutdemande_libelle", type="string", length=300, nullable=false)
     */
    private $statutdemandeLibelle;


}
