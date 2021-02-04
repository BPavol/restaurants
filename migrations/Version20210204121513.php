<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204121513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cuisine (code VARCHAR(16) NOT NULL, title VARCHAR(16) NOT NULL, INDEX title_idx (title), PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opening_hour (id INT UNSIGNED AUTO_INCREMENT NOT NULL, restaurant_id INT UNSIGNED NOT NULL, open DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', close DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_969BD765B1E7706E (restaurant_id), INDEX open_idx (open), UNIQUE INDEX open_close_restaurant_id_uc (open, close, restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cuisine_code VARCHAR(16) DEFAULT NULL, identifier VARCHAR(16) NOT NULL, title VARCHAR(64) NOT NULL, price SMALLINT UNSIGNED DEFAULT NULL, rating SMALLINT UNSIGNED DEFAULT NULL, location VARCHAR(64) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_EB95123FEFB6F3D3 (cuisine_code), FULLTEXT INDEX title_location_fidx (title, location), INDEX rating_idx (rating), INDEX price_idx (price), UNIQUE INDEX identifier_uc (identifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE opening_hour ADD CONSTRAINT FK_969BD765B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123FEFB6F3D3 FOREIGN KEY (cuisine_code) REFERENCES cuisine (code) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123FEFB6F3D3');
        $this->addSql('ALTER TABLE opening_hour DROP FOREIGN KEY FK_969BD765B1E7706E');
        $this->addSql('DROP TABLE cuisine');
        $this->addSql('DROP TABLE opening_hour');
        $this->addSql('DROP TABLE restaurant');
    }

    /**
     * Temporary bugfix: https://github.com/doctrine/migrations/issues/1104
     *
     * @return bool
     */
    public function isTransactional(): bool
    {
        return false;
    }
}
