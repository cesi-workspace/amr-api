<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509065703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE help_request DROP FOREIGN KEY FK_HELPREQUEST_CATEGORY');
        $this->addSql('ALTER TABLE help_request_category CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE help_request ADD CONSTRAINT FK_HELPREQUEST_CATEGORY FOREIGN KEY (category_id) REFERENCES help_request_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE help_request DROP FOREIGN KEY FK_HELPREQUEST_CATEGORY');
        $this->addSql('ALTER TABLE help_request_category CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE help_request ADD CONSTRAINT FK_HELPREQUEST_CATEGORY FOREIGN KEY (category_id) REFERENCES help_request_category (id)');
    }
}
