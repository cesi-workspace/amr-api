<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428214113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD mark INT DEFAULT NULL');
        $this->addSql('ALTER TABLE help_request ADD latitude DOUBLE PRECISION NOT NULL AFTER `date`, ADD longitude DOUBLE PRECISION NOT NULL AFTER `latitude`, DROP city, DROP postal_code, DROP mark');

        $this->addSql("INSERT INTO `user` (`id`, `type_id`, `status_id`, `email`, `surname`, `firstname`, `password`, `city`, `postal_code`, `point`, `created_at`, `updated_at`) VALUES (1, 6, 1, 'oTm5pr7rI3p5/tOHj3kBqQNrLoDviVzG7fvW98lWacM=', 'LesQ+mRVQXcRvYcXFRVnlA==', 'LesQ+mRVQXcRvYcXFRVnlA==', '$2y$"."13$"."J7IF6o4olJCWadofGjWkr.bHIegOvv9M0C6ASakisLq6fdx8dcN9C', NULL, NULL, NULL, '2023-04-28 23:57:44', '2023-04-29 00:08:08'),	(2, 1, 1, 'cHPDALK3ZCFvxxdJ977oCA==', 'q1rXQiD/5ujXX3IjDunEZA==', 'q1rXQiD/5ujXX3IjDunEZA==', '$2y$13$"."ybI9ngkHhSYhtu.7Iso5bOg74aAToEoJoIFaCrD0g06JbVUsimuka', NULL, NULL, NULL, '2023-04-29 00:01:13', '2023-04-29 00:01:13'),	(3, 4, 1, 'bDdbSj1v1PMqnp9rZnOkcvyVwQDnoJ472Sow0Gy49V4=', 'GhTdUBG1Q74uAhIBT7tX9w==', 'GhTdUBG1Q74uAhIBT7tX9w==', '$2y$13$"."rD4k6/0DOzZFmuV.hs2IyesxMrE27aTDc7K0p8m/bDOorIL7A9/g.', NULL, NULL, NULL, '2023-04-29 00:02:10', '2023-04-29 00:02:10'),	(4, 2, 1, 'OhycY0ai65+4K7TpVn4xwg==', 'Tl3cVS+IDUIDwBvKdbEZWQ==', 'Tl3cVS+IDUIDwBvKdbEZWQ==', '$2y$13$48PJxp3OiR/CjHGGVVo4Pur./v0N1Hmdh1P/joj/w0LHHNm7wxA9C', NULL, NULL, NULL, '2023-04-29 00:03:56', '2023-04-29 00:03:56'),	(5, 3, 1, 'HbnaxgbEqVkovXIrtKSk8Q==', 'KkP5l8pP/gosQGwH1kblnA==', 'KkP5l8pP/gosQGwH1kblnA==', '$2y$13$"."cOeQ6/nyjWzFgyyqCNUCeOeKorR6egICOpyqHwhLCPjYw6G/acO/a', '+OXN0hEAxf2xxfWuG6JBUQ==', NULL, NULL, '2023-04-29 00:05:17', '2023-04-29 00:05:17'),	(6, 7, 1, 'CyVYnAIOghvL3fBpSimXww==', 'CeAVYghIwz0CEXI1XiwLtQ==', 'CeAVYghIwz0CEXI1XiwLtQ==', '$2y$13$"."kA7JDHfhO3vcoY1xbFBDYuEvZYN6SSC6zS87Rtbj8hHYMQBkuJ1wO', NULL, NULL, NULL, '2023-04-29 00:06:21', '2023-04-29 00:06:21'),	(7, 5, 1, '2T/5qOOuAY949WfICQRuhw==', 'VODTu+lPPtrnP4ZZ9psiSw==', 'VODTu+lPPtrnP4ZZ9psiSw==', '$2y$13$"."ViiU8fHHwhAzM0ZKabn3hOx/0CX1EWX9tT8.sOfO2wHvdfsyiZ7Fi', NULL, NULL, NULL, '2023-04-29 00:08:08', '2023-04-29 00:08:08');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP mark');
        $this->addSql('ALTER TABLE help_request ADD city VARCHAR(300) NOT NULL AFTER `date`, ADD postal_code VARCHAR(300) NOT NULL AFTER `city`, ADD mark INT DEFAULT NULL AFTER `postal_code`,  DROP latitude, DROP longitude');
        $this->addSql('DELETE FROM `user`');

    }
}
