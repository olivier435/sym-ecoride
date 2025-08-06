<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250806072541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_2D5B02345E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip ADD departure_city_id INT NOT NULL, ADD arrival_city_id INT NOT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B918B251E FOREIGN KEY (departure_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B4067ACA7 FOREIGN KEY (arrival_city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_7656F53B918B251E ON trip (departure_city_id)');
        $this->addSql('CREATE INDEX IDX_7656F53B4067ACA7 ON trip (arrival_city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B918B251E');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B4067ACA7');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP INDEX IDX_7656F53B918B251E ON trip');
        $this->addSql('DROP INDEX IDX_7656F53B4067ACA7 ON trip');
        $this->addSql('ALTER TABLE trip DROP departure_city_id, DROP arrival_city_id');
    }
}
