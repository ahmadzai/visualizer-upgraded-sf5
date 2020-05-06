<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506150941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492F1CBF35');
        $this->addSql('DROP INDEX IDX_8D93D6492F1CBF35 ON user');
        $this->addSql('ALTER TABLE user CHANGE account_created_by_id author_id INT DEFAULT NULL, CHANGE account_created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F675F31B ON user (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F675F31B');
        $this->addSql('DROP INDEX IDX_8D93D649F675F31B ON user');
        $this->addSql('ALTER TABLE user CHANGE author_id account_created_by_id INT DEFAULT NULL, CHANGE created_at account_created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492F1CBF35 FOREIGN KEY (account_created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492F1CBF35 ON user (account_created_by_id)');
    }
}
