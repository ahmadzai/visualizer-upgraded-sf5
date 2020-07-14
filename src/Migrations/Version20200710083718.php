<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710083718 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_health_facility CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE upload_manager DROP FOREIGN KEY FK_3F133F578D93D649');
        $this->addSql('DROP INDEX IDX_3F133F578D93D649 ON upload_manager');
        $this->addSql('ALTER TABLE upload_manager ADD updated_by_id INT DEFAULT NULL, ADD deleted_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE user created_by_id INT DEFAULT NULL, CHANGE modified_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE upload_manager ADD CONSTRAINT FK_3F133F57B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE upload_manager ADD CONSTRAINT FK_3F133F57896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE upload_manager ADD CONSTRAINT FK_3F133F57C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3F133F57B03A8386 ON upload_manager (created_by_id)');
        $this->addSql('CREATE INDEX IDX_3F133F57896DBBDE ON upload_manager (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_3F133F57C76F1F52 ON upload_manager (deleted_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_health_facility CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE upload_manager DROP FOREIGN KEY FK_3F133F57B03A8386');
        $this->addSql('ALTER TABLE upload_manager DROP FOREIGN KEY FK_3F133F57896DBBDE');
        $this->addSql('ALTER TABLE upload_manager DROP FOREIGN KEY FK_3F133F57C76F1F52');
        $this->addSql('DROP INDEX IDX_3F133F57B03A8386 ON upload_manager');
        $this->addSql('DROP INDEX IDX_3F133F57896DBBDE ON upload_manager');
        $this->addSql('DROP INDEX IDX_3F133F57C76F1F52 ON upload_manager');
        $this->addSql('ALTER TABLE upload_manager ADD user INT DEFAULT NULL, DROP created_by_id, DROP updated_by_id, DROP deleted_by_id, DROP updated_at, CHANGE created_at modified_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE upload_manager ADD CONSTRAINT FK_3F133F578D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3F133F578D93D649 ON upload_manager (user)');
    }
}
