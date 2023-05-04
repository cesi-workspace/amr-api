<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230504144729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD id INT AUTO_INCREMENT NOT NULL FIRST, CHANGE date date DATETIME NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UK_MESSAGE ON message (date, to_user_id, from_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UK_MESSAGE ON message');
        $this->addSql('DROP INDEX `PRIMARY` ON message');
        $this->addSql('ALTER TABLE message DROP id, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE message ADD PRIMARY KEY (date, to_user_id, from_user_id)');
    }
}
