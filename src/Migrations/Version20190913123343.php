<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913123343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_1483a5e99f75d7b0');
        $this->addSql('ALTER TABLE users DROP email');
        $this->addSql('ALTER TABLE users RENAME COLUMN external_id TO uuid');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_1483A5E9D17F50A6');
        $this->addSql('ALTER TABLE users ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users RENAME COLUMN uuid TO external_id');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e99f75d7b0 ON users (external_id)');
    }
}
