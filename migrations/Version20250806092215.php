<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250806092215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE travel_preference (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, discussion VARCHAR(255) NOT NULL, music VARCHAR(255) NOT NULL, smoking VARCHAR(255) NOT NULL, pets VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4DC5180A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE travel_preference ADD CONSTRAINT FK_4DC5180A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE travel_preference DROP FOREIGN KEY FK_4DC5180A76ED395');
        $this->addSql('DROP TABLE travel_preference');
    }
}
