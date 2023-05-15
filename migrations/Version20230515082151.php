<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515082151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, comment_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, content VARCHAR(3000) NOT NULL, INDEX FK_ANSWER_USER (user_id), INDEX FK_ANSWER_COMMENT (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_ANSWER_COMMENT FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_ANSWER_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment DROP answer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_ANSWER_COMMENT');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_ANSWER_USER');
        $this->addSql('DROP TABLE answer');
        $this->addSql('ALTER TABLE comment ADD answer VARCHAR(3000) DEFAULT NULL');
    }
}
