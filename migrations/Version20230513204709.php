<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230513204709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("set names 'utf8'; SET block_encryption_mode = 'aes-256-ecb'; SET @cle='".$_ENV['APP_SECRET']."';");
        $this->addSql('UPDATE connection set ip_address=TO_BASE64(AES_ENCRYPT(ip_address,FROM_BASE64(@cle)))');
        $this->addSql('UPDATE message set content=TO_BASE64(AES_ENCRYPT(content,FROM_BASE64(@cle)))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("set names 'utf8'; SET block_encryption_mode = 'aes-256-ecb'; SET @cle='".$_ENV['APP_SECRET']."';");
        $this->addSql('UPDATE connection set ip_address=AES_DECRYPT(FROM_BASE64(ip_address), FROM_BASE64(@cle))');
        $this->addSql('UPDATE message set content=AES_DECRYPT(FROM_BASE64(content), FROM_BASE64(@cle))');
    }
}
