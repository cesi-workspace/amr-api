<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501232439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (comment_id INT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, INDEX FK_REPORT_USER (user_id), INDEX FK_REPORT_COMMENT (comment_id), PRIMARY KEY(comment_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_REPORT_COMMENT FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_REPORT_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment DROP `signal`');
        $this->addSql('ALTER TABLE comment MODIFY content VARCHAR(3000) NOT NULL AFTER owner_id');
        $this->addSql('ALTER TABLE comment MODIFY mark INT DEFAULT NULL AFTER content');
        $this->addSql('ALTER TABLE comment MODIFY created_at DATETIME NOT NULL DEFAULT NOW() AFTER helper_id');
        $this->addSql('ALTER TABLE comment MODIFY updated_at DATETIME DEFAULT NULL AFTER created_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_REPORT_COMMENT');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_REPORT_USER');
        $this->addSql('DROP TABLE report');
        $this->addSql('ALTER TABLE comment MODIFY content VARCHAR(3000) NOT NULL AFTER helper_id');
        $this->addSql('ALTER TABLE comment MODIFY mark INT DEFAULT NULL AFTER content');
        $this->addSql('ALTER TABLE comment MODIFY created_at DATETIME NOT NULL DEFAULT NOW() AFTER date');
        $this->addSql('ALTER TABLE comment MODIFY updated_at DATETIME DEFAULT NULL AFTER created_at');
        $this->addSql('ALTER TABLE comment ADD `signal` TINYINT(1) NOT NULL AFTER date');
    }
}
