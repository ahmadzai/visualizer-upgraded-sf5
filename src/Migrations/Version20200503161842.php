<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503161842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD account_created_by_id INT NOT NULL, ADD email VARCHAR(150) NOT NULL, ADD job_title VARCHAR(100) DEFAULT NULL, ADD country VARCHAR(100) DEFAULT NULL, ADD city VARCHAR(100) DEFAULT NULL, ADD mobile_no VARCHAR(20) DEFAULT NULL, ADD has_api_access TINYINT(1) DEFAULT NULL, ADD last_password_change_at DATETIME DEFAULT NULL, ADD account_created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492F1CBF35 FOREIGN KEY (account_created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492F1CBF35 ON user (account_created_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492F1CBF35');
        $this->addSql('DROP INDEX IDX_8D93D6492F1CBF35 ON user');
        $this->addSql('ALTER TABLE user DROP account_created_by_id, DROP email, DROP job_title, DROP country, DROP city, DROP mobile_no, DROP has_api_access, DROP last_password_change_at, DROP account_created_at');
    }
}
