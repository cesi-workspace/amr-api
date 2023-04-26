<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426215056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_PURCHASE_COMMENT');
        $this->addSql('DROP INDEX FK_COMMENT_USER ON comment');
        $this->addSql('ALTER TABLE comment ADD helper_id INT NOT NULL, ADD content VARCHAR(3000) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE user_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_COMMENT_OWNER FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_COMMENT_HELPER FOREIGN KEY (helper_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX FK_COMMENT_OWNER ON comment (owner_id)');
        $this->addSql('CREATE INDEX FK_COMMENT_HELPER ON comment (helper_id)');
        $this->addSql('ALTER TABLE connection CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE gift CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE help_request_category CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE help_request_status CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE help_request_treatment CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE help_request_treatment_type CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_status CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_type CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_COMMENT_OWNER');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_COMMENT_HELPER');
        $this->addSql('DROP INDEX FK_COMMENT_OWNER ON comment');
        $this->addSql('DROP INDEX FK_COMMENT_HELPER ON comment');
        $this->addSql('ALTER TABLE comment ADD user_id INT NOT NULL, DROP owner_id, DROP helper_id, DROP content, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_PURCHASE_COMMENT FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX FK_COMMENT_USER ON comment (user_id)');
        $this->addSql('ALTER TABLE connection CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE gift CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE help_request_category CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE help_request_status CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE help_request_treatment CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE help_request_treatment_type CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE Purchase CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE user_status CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE user_type CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
