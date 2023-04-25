<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425201148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE connection ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE gift ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE help_request_category ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE help_request_status ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE help_request_treatment ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE help_request_treatment_type ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_status ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_type ADD created_at DATETIME NOT NULL DEFAULT NOW(), ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE connection DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE gift DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE help_request_category DROP created_at, DROP updated_at, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE help_request_status DROP created_at, DROP updated_at, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE help_request_treatment DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE help_request_treatment_type DROP created_at, DROP updated_at, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE Purchase DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user_status DROP created_at, DROP updated_at, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE user_type DROP created_at, DROP updated_at, CHANGE id id INT NOT NULL');
    }
}
