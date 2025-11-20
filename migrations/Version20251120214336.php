<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120214336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quest (id SERIAL NOT NULL, step_id INT NOT NULL, before_label VARCHAR(255) NOT NULL, before_scene TEXT NOT NULL, success_label VARCHAR(255) NOT NULL, success_scene TEXT NOT NULL, failure_label VARCHAR(255) NOT NULL, failure_scene TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4317F81773B21E9C ON quest (step_id)');
        $this->addSql('COMMENT ON COLUMN quest.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quest.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE quest ADD CONSTRAINT FK_4317F81773B21E9C FOREIGN KEY (step_id) REFERENCES step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quest DROP CONSTRAINT FK_4317F81773B21E9C');
        $this->addSql('DROP TABLE quest');
    }
}
