<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190601105151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE resources_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE resources (id INT NOT NULL, user_id INT DEFAULT NULL, external_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, permission_user_readable BOOLEAN NOT NULL, permission_user_writable BOOLEAN NOT NULL, permission_user_executable BOOLEAN NOT NULL, permission_group_readable BOOLEAN NOT NULL, permission_group_writable BOOLEAN NOT NULL, permission_group_executable BOOLEAN NOT NULL, permission_other_readable BOOLEAN NOT NULL, permission_other_writable BOOLEAN NOT NULL, permission_other_executable BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EF66EBAEA76ED395 ON resources (user_id)');
        $this->addSql('ALTER TABLE resources ADD CONSTRAINT FK_EF66EBAEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE resources_id_seq CASCADE');
        $this->addSql('DROP TABLE resources');
    }
}
