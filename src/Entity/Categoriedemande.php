<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoriedemande
 *
 * @ORM\Table(name="categoriedemande")
 * @ORM\Entity
 */
class Categoriedemande
{
    /**
     * @var int
     *
     * @ORM\Column(name="categoriedemande_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $categoriedemandeId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="categoriedemande_heuremin", type="time", nullable=false)
     */
    private $categoriedemandeHeuremin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="categoriedemande_heuremax", type="time", nullable=false)
     */
    private $categoriedemandeHeuremax;

    /**
     * @var int
     *
     * @ORM\Column(name="categoriedemande_coefpoint", type="integer", nullable=false)
     */
    private $categoriedemandeCoefpoint;


}
