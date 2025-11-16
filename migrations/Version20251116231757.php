<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116231757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE branch (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB861B1F989D9B62 ON branch (slug)');
        $this->addSql('COMMENT ON COLUMN branch.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN branch.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE branch_skill (branch_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(branch_id, skill_id))');
        $this->addSql('CREATE INDEX IDX_308CE620DCD6CC49 ON branch_skill (branch_id)');
        $this->addSql('CREATE INDEX IDX_308CE6205585C142 ON branch_skill (skill_id)');
        $this->addSql('CREATE TABLE domain (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A7A91E0B989D9B62 ON domain (slug)');
        $this->addSql('COMMENT ON COLUMN domain.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN domain.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE domain_branch (domain_id INT NOT NULL, branch_id INT NOT NULL, PRIMARY KEY(domain_id, branch_id))');
        $this->addSql('CREATE INDEX IDX_D4E517D6115F0EE5 ON domain_branch (domain_id)');
        $this->addSql('CREATE INDEX IDX_D4E517D6DCD6CC49 ON domain_branch (branch_id)');
        $this->addSql('CREATE TABLE objective (id SERIAL NOT NULL, skill_id INT NOT NULL, difficulty INT NOT NULL, duration INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B996F101989D9B62 ON objective (slug)');
        $this->addSql('CREATE INDEX IDX_B996F1015585C142 ON objective (skill_id)');
        $this->addSql('COMMENT ON COLUMN objective.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN objective.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE skill (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E3DE477989D9B62 ON skill (slug)');
        $this->addSql('COMMENT ON COLUMN skill.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN skill.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE branch_skill ADD CONSTRAINT FK_308CE620DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE branch_skill ADD CONSTRAINT FK_308CE6205585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domain_branch ADD CONSTRAINT FK_D4E517D6115F0EE5 FOREIGN KEY (domain_id) REFERENCES domain (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domain_branch ADD CONSTRAINT FK_D4E517D6DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F1015585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE branch_skill DROP CONSTRAINT FK_308CE620DCD6CC49');
        $this->addSql('ALTER TABLE branch_skill DROP CONSTRAINT FK_308CE6205585C142');
        $this->addSql('ALTER TABLE domain_branch DROP CONSTRAINT FK_D4E517D6115F0EE5');
        $this->addSql('ALTER TABLE domain_branch DROP CONSTRAINT FK_D4E517D6DCD6CC49');
        $this->addSql('ALTER TABLE objective DROP CONSTRAINT FK_B996F1015585C142');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE branch_skill');
        $this->addSql('DROP TABLE domain');
        $this->addSql('DROP TABLE domain_branch');
        $this->addSql('DROP TABLE objective');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
