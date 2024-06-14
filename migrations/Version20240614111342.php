<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240614111342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie_character DROP CONSTRAINT FK_AFB9321136BE75');
        $this->addSql('ALTER TABLE movie_character ADD CONSTRAINT FK_AFB9321136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE movie_character DROP CONSTRAINT fk_afb9321136be75');
        $this->addSql('ALTER TABLE movie_character ADD CONSTRAINT fk_afb9321136be75 FOREIGN KEY (character_id) REFERENCES "character" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
