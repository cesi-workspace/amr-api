<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503195501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FUNCTION get_distance_kms (lat1 DOUBLE, lng1 DOUBLE, lat2 DOUBLE, lng2 DOUBLE) RETURNS DOUBLE
        BEGIN
            DECLARE rlo1 DOUBLE;
            DECLARE rla1 DOUBLE;
            DECLARE rlo2 DOUBLE;
            DECLARE rla2 DOUBLE;
            DECLARE dlo DOUBLE;
            DECLARE dla DOUBLE;
            DECLARE a DOUBLE;
            SET rlo1 = RADIANS(lng1);
            SET rla1 = RADIANS(lat1);
            SET rlo2 = RADIANS(lng2);
            SET rla2 = RADIANS(lat2);
            SET dlo = (rlo2 - rlo1) / 2;
            SET dla = (rla2 - rla1) / 2;
            SET a = SIN(dla) * SIN(dla) + COS(rla1) * COS(rla2) * SIN(dlo) * SIN(dlo);
            RETURN (6378137 * 2 * ATAN2(SQRT(a), SQRT(1 - a))) / 1000;
        END');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP FUNCTION IF EXISTS get_distance_kms');
    }
}
