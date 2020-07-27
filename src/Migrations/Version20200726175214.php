<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200726175214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86D1349DB3');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86F87CE91C');
        $this->addSql('DROP INDEX IDX_6D0E6A86D1349DB3 ON bphs_hf_indicator');
        $this->addSql('DROP INDEX IDX_6D0E6A86F87CE91C ON bphs_hf_indicator');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD bphs_health_facility INT DEFAULT NULL, ADD bphs_indicator INT DEFAULT NULL, DROP health_facility, DROP indicator');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A863C790A24 FOREIGN KEY (bphs_health_facility) REFERENCES bphs_health_facility (id)');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A863040341B FOREIGN KEY (bphs_indicator) REFERENCES bphs_indicator (id)');
        $this->addSql('CREATE INDEX IDX_6D0E6A863C790A24 ON bphs_hf_indicator (bphs_health_facility)');
        $this->addSql('CREATE INDEX IDX_6D0E6A863040341B ON bphs_hf_indicator (bphs_indicator)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A863C790A24');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A863040341B');
        $this->addSql('DROP INDEX IDX_6D0E6A863C790A24 ON bphs_hf_indicator');
        $this->addSql('DROP INDEX IDX_6D0E6A863040341B ON bphs_hf_indicator');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD health_facility INT DEFAULT NULL, ADD indicator INT DEFAULT NULL, DROP bphs_health_facility, DROP bphs_indicator');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86D1349DB3 FOREIGN KEY (indicator) REFERENCES bphs_indicator (id)');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86F87CE91C FOREIGN KEY (health_facility) REFERENCES bphs_health_facility (id)');
        $this->addSql('CREATE INDEX IDX_6D0E6A86D1349DB3 ON bphs_hf_indicator (indicator)');
        $this->addSql('CREATE INDEX IDX_6D0E6A86F87CE91C ON bphs_hf_indicator (health_facility)');
    }
}
