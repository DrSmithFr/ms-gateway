<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190531233736 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users ADD external_id UUID NOT NULL');
        $this->addSql('ALTER TABLE users ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE users ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE users ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN users.external_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E99F75D7B0 ON users (external_id)');
        $this->addSql('ALTER TABLE groups ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE groups ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE groups ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE groups ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE roles ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE roles ADD created_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD updated_by VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_1483A5E99F75D7B0');
        $this->addSql('ALTER TABLE users DROP external_id');
        $this->addSql('ALTER TABLE users DROP created_at');
        $this->addSql('ALTER TABLE users DROP updated_at');
        $this->addSql('ALTER TABLE users DROP created_by');
        $this->addSql('ALTER TABLE users DROP updated_by');
        $this->addSql('ALTER TABLE roles DROP created_at');
        $this->addSql('ALTER TABLE roles DROP updated_at');
        $this->addSql('ALTER TABLE roles DROP created_by');
        $this->addSql('ALTER TABLE roles DROP updated_by');
        $this->addSql('ALTER TABLE groups DROP created_at');
        $this->addSql('ALTER TABLE groups DROP updated_at');
        $this->addSql('ALTER TABLE groups DROP created_by');
        $this->addSql('ALTER TABLE groups DROP updated_by');
    }
}
