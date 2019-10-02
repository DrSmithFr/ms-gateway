<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190531221155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'user basics';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE groups_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE roles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_roles (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_54FCD59FA76ED395 ON user_roles (user_id)');
        $this->addSql('CREATE INDEX IDX_54FCD59FD60322AC ON user_roles (role_id)');
        $this->addSql('CREATE TABLE groups (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE group_user (group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A4C98D39FE54D947 ON group_user (group_id)');
        $this->addSql('CREATE INDEX IDX_A4C98D39A76ED395 ON group_user (user_id)');
        $this->addSql('CREATE TABLE group_roles (group_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(group_id, role_id))');
        $this->addSql('CREATE INDEX IDX_E61205A0FE54D947 ON group_roles (group_id)');
        $this->addSql('CREATE INDEX IDX_E61205A0D60322AC ON group_roles (role_id)');
        $this->addSql('CREATE TABLE roles (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_roles ADD CONSTRAINT FK_E61205A0FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_roles ADD CONSTRAINT FK_E61205A0D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_roles DROP CONSTRAINT FK_54FCD59FA76ED395');
        $this->addSql('ALTER TABLE group_user DROP CONSTRAINT FK_A4C98D39A76ED395');
        $this->addSql('ALTER TABLE group_user DROP CONSTRAINT FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE group_roles DROP CONSTRAINT FK_E61205A0FE54D947');
        $this->addSql('ALTER TABLE user_roles DROP CONSTRAINT FK_54FCD59FD60322AC');
        $this->addSql('ALTER TABLE group_roles DROP CONSTRAINT FK_E61205A0D60322AC');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE groups_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE roles_id_seq CASCADE');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE group_user');
        $this->addSql('DROP TABLE group_roles');
        $this->addSql('DROP TABLE roles');
    }
}
