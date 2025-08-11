<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250811080842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_passenger (id INT AUTO_INCREMENT NOT NULL, trip_id INT NOT NULL, user_id INT NOT NULL, validation_status VARCHAR(20) DEFAULT \'pending\' NOT NULL, INDEX IDX_26F0A98AA5BC2E0E (trip_id), INDEX IDX_26F0A98AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip_passenger ADD CONSTRAINT FK_26F0A98AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('ALTER TABLE trip_passenger ADD CONSTRAINT FK_26F0A98AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trip_passengers DROP FOREIGN KEY FK_1645559CA5BC2E0E');
        $this->addSql('ALTER TABLE trip_passengers DROP FOREIGN KEY FK_1645559CA76ED395');
        $this->addSql('DROP TABLE trip_passengers');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_passengers (trip_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1645559CA5BC2E0E (trip_id), INDEX IDX_1645559CA76ED395 (user_id), PRIMARY KEY(trip_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE trip_passengers ADD CONSTRAINT FK_1645559CA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_passengers ADD CONSTRAINT FK_1645559CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_passenger DROP FOREIGN KEY FK_26F0A98AA5BC2E0E');
        $this->addSql('ALTER TABLE trip_passenger DROP FOREIGN KEY FK_26F0A98AA76ED395');
        $this->addSql('DROP TABLE trip_passenger');
    }
}
