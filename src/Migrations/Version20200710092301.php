<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710092301 extends AbstractMigration
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
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB592C8D43A');
        $this->addSql('DROP INDEX IDX_5938EB592C8D43A ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE hf_indicator bphs_hf_indicator INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB56D0E6A86 FOREIGN KEY (bphs_hf_indicator) REFERENCES bphs_hf_indicator (id)');
        $this->addSql('CREATE INDEX IDX_5938EB56D0E6A86 ON bphs_indicator_reach (bphs_hf_indicator)');
        $this->addSql('ALTER TABLE upload_manager CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_health_facility CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB56D0E6A86');
        $this->addSql('DROP INDEX IDX_5938EB56D0E6A86 ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE bphs_hf_indicator hf_indicator INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB592C8D43A FOREIGN KEY (hf_indicator) REFERENCES bphs_hf_indicator (id)');
        $this->addSql('CREATE INDEX IDX_5938EB592C8D43A ON bphs_indicator_reach (hf_indicator)');
        $this->addSql('ALTER TABLE upload_manager CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }
}
