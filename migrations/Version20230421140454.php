<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421140454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Purchase (helper_id INT NOT NULL, gift_id INT NOT NULL, code VARCHAR(200) NOT NULL, date DATETIME NOT NULL, INDEX FK_PURCHASE_HELPER (helper_id), INDEX FK_PURCHASE_GIFT (gift_id), PRIMARY KEY(helper_id, gift_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, `signal` TINYINT(1) NOT NULL, INDEX FK_COMMENT_USER (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE connection (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ip_address VARCHAR(300) NOT NULL, success TINYINT(1) NOT NULL, login_date DATETIME NOT NULL, logout_date DATETIME DEFAULT NULL, INDEX FK_CONNECTION_USER (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite (helper_id INT NOT NULL, owner_id INT NOT NULL, INDEX FK_FAVORITE_HELPER (helper_id), INDEX FK_FAVORITE_OWNER (owner_id), PRIMARY KEY(helper_id, owner_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, partner_id INT NOT NULL, label VARCHAR(3000) NOT NULL, price INT NOT NULL, from_date DATETIME DEFAULT NULL, to_date DATETIME DEFAULT NULL, INDEX FK_GIFT_PARTNER (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_request (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, helper_id INT DEFAULT NULL, category_id INT NOT NULL, status_id INT NOT NULL, title VARCHAR(300) NOT NULL, description VARCHAR(5000) DEFAULT NULL, estimated_delay TIME NOT NULL, date DATETIME NOT NULL, city VARCHAR(300) NOT NULL, postal_code VARCHAR(300) NOT NULL, mark INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX FK_HELP_REQUEST_CATEGORY (category_id), INDEX FK_HELP_REQUEST_OWNER (owner_id), INDEX FK_HELP_REQUEST_STATUS (status_id), INDEX FK_HELP_REQUEST_HELPER (helper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_request_category (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_request_status (id INT NOT NULL, label VARCHAR(300) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_request_treatment (help_request_id INT NOT NULL, helper_id INT NOT NULL, type_id INT NOT NULL, INDEX FK_HELP_REQUEST_TREATMENT_HELP_REQUEST (help_request_id), INDEX FK_HELP_REQUEST_TREATMENT_HELPER (helper_id), INDEX FK_HELP_REQUEST_TREATMENT_HELP_REQUEST_TREATMENT_TYPE (type_id), PRIMARY KEY(help_request_id, helper_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_request_treatment_type (id INT NOT NULL, label VARCHAR(300) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (date DATETIME NOT NULL, to_user_id INT NOT NULL, from_user_id INT NOT NULL, content VARCHAR(3000) NOT NULL, INDEX FK_MESSAGE_FROM_USER (from_user_id), INDEX FK_MESSAGE_TO_USER (to_user_id), PRIMARY KEY(date, to_user_id, from_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, status_id INT NOT NULL, email VARCHAR(180) NOT NULL, surname VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, point INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX FK_USER_USERTYPE (type_id), INDEX FK_USER_USER_STATUS (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_status (id INT NOT NULL, label VARCHAR(300) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type (id INT NOT NULL, label VARCHAR(300) NOT NULL, role VARCHAR(300) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Purchase ADD CONSTRAINT FK_PURCHASE_HELPER FOREIGN KEY (helper_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Purchase ADD CONSTRAINT FK_PURCHASE_GIFT FOREIGN KEY (gift_id) REFERENCES gift (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_PURCHASE_COMMENT FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_CONNECTION_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_FAVORITE_HELPER FOREIGN KEY (helper_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_FAVORITE_OWNER FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_GIFT_PARTNER FOREIGN KEY (partner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE help_request ADD CONSTRAINT FK_HELPREQUEST_OWNER FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE help_request ADD CONSTRAINT FK_HELPREQUEST_HELPER FOREIGN KEY (helper_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE help_request ADD CONSTRAINT FK_HELPREQUEST_CATEGORY FOREIGN KEY (category_id) REFERENCES help_request_category (id)');
        $this->addSql('ALTER TABLE help_request ADD CONSTRAINT FK_HELPREQUEST_STATUS FOREIGN KEY (status_id) REFERENCES help_request_status (id)');
        $this->addSql('ALTER TABLE help_request_treatment ADD CONSTRAINT FK_HELPREQUESTTREATMENT_TYPE FOREIGN KEY (type_id) REFERENCES help_request_treatment_type (id)');
        $this->addSql('ALTER TABLE help_request_treatment ADD CONSTRAINT FK_HELPREQUESTTREATMENT_HELPREQUEST FOREIGN KEY (help_request_id) REFERENCES help_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE help_request_treatment ADD CONSTRAINT FK_HELPREQUESTTREATMENT_HELPER FOREIGN KEY (helper_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_MESSAGE_USERTO FOREIGN KEY (to_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_MESSAGE_USERFROM FOREIGN KEY (from_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_USER_TYPE FOREIGN KEY (type_id) REFERENCES user_type (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_USER_STATUS FOREIGN KEY (status_id) REFERENCES user_status (id)');

        $this->addSql("INSERT INTO `user_type` (`id`, `label`, `role`) VALUES (1, 'Administrateur', 'ROLE_ADMIN'), (2, 'MembreMr', 'ROLE_OWNER'), (3, 'MembreVolontaire', 'ROLE_HELPER'), (4, 'Modérateur', 'ROLE_MODERATOR'),  (5, 'Partenaire', 'ROLE_PARTNER'),(6, 'Superadministrateur', 'ROLE_SUPERADMIN'),(7, 'MembreEtat', 'ROLE_GOV');");
        $this->addSql("INSERT INTO `user_status` (`id`, `label`) VALUES	(1, 'Activé'),(2, 'En demande'),(3, 'Désactivé'),(4, 'Refusé');");
        $this->addSql("INSERT INTO `help_request_treatment_type` (`id`, `label`) VALUES	(1, 'Favorisée'),(2, 'Acceptée'),(3, 'Refusée');");
        $this->addSql("INSERT INTO `help_request_category` (`id`, `title`) VALUES  (1, 'Tâches ménagères'), (2, 'Espaces verts'), (3, 'Courses'), (4, 'Soutien informatique'), (5, 'Transports'), (6, 'Bricolage'), (7, 'Alimentation');");
        $this->addSql("INSERT INTO `help_request_status` (`id`, `label`) VALUES  (1, 'Créée'), (2, 'Acceptée'), (3, 'Terminée');");
        }
        
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Purchase DROP FOREIGN KEY FK_PURCHASE_USER');
        $this->addSql('ALTER TABLE Purchase DROP FOREIGN KEY FK_PURCHASE_GIFT');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_PURCHASE_COMMENT');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_CONNECTION_USER');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_FAVORITE_HELPER');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_FAVORITE_OWNER');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_FAVORITE_HELPER');
        $this->addSql('ALTER TABLE help_request DROP FOREIGN KEY FK_HELPREQUEST_OWNER');
        $this->addSql('ALTER TABLE help_request DROP FOREIGN KEY FK_HELPREQUEST_HELPER');
        $this->addSql('ALTER TABLE help_request DROP FOREIGN KEY FK_HELPREQUEST_CATEGORY');
        $this->addSql('ALTER TABLE help_request DROP FOREIGN KEY FK_HELPREQUEST_STATUS');
        $this->addSql('ALTER TABLE help_request_treatment DROP FOREIGN KEY FK_HELPREQUESTTREATMENT_TYPE');
        $this->addSql('ALTER TABLE help_request_treatment DROP FOREIGN KEY FK_HELPREQUESTTREATMENT_HELPREQUEST');
        $this->addSql('ALTER TABLE help_request_treatment DROP FOREIGN KEY FK_HELPREQUESTTREATMENT_HELPER');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_MESSAGE_USERTO');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_MESSAGE_USERFROM');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_USER_TYPE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_USER_STATUS');
        $this->addSql('DROP TABLE Purchase');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE connection');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE help_request');
        $this->addSql('DROP TABLE help_request_category');
        $this->addSql('DROP TABLE help_request_status');
        $this->addSql('DROP TABLE help_request_treatment');
        $this->addSql('DROP TABLE help_request_treatment_type');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_status');
        $this->addSql('DROP TABLE user_type');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
