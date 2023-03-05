<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typetraitementdemande
 *
 * @ORM\Table(name="typetraitementdemande")
 * @ORM\Entity
 */
class Typetraitementdemande
{
    /**
     * @var int
     *
     * @ORM\Column(name="typetraitementdemande_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $typetraitementdemandeId;

    /**
     * @var string
     *
     * @ORM\Column(name="typetraitementdemande_libelle", type="string", length=300, nullable=false)
     */
    private $typetraitementdemandeLibelle;


}
