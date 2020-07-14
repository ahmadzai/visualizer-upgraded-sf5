<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200714135236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5387314DF');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB54402854A');
        $this->addSql('DROP INDEX IDX_5938EB54402854A ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5387314DF ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD hf_code INT DEFAULT NULL, ADD indicator INT DEFAULT NULL, DROP hf_code_id, DROP indicator_id');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5178A8625 FOREIGN KEY (hf_code) REFERENCES bphs_health_facility (id)');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5D1349DB3 FOREIGN KEY (indicator) REFERENCES bphs_indicator (id)');
        $this->addSql('CREATE INDEX IDX_5938EB5178A8625 ON bphs_indicator_reach (hf_code)');
        $this->addSql('CREATE INDEX IDX_5938EB5D1349DB3 ON bphs_indicator_reach (indicator)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5178A8625');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5D1349DB3');
        $this->addSql('DROP INDEX IDX_5938EB5178A8625 ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5D1349DB3 ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD hf_code_id INT DEFAULT NULL, ADD indicator_id INT DEFAULT NULL, DROP hf_code, DROP indicator');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5387314DF FOREIGN KEY (hf_code_id) REFERENCES bphs_health_facility (id)');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB54402854A FOREIGN KEY (indicator_id) REFERENCES bphs_indicator (id)');
        $this->addSql('CREATE INDEX IDX_5938EB54402854A ON bphs_indicator_reach (indicator_id)');
        $this->addSql('CREATE INDEX IDX_5938EB5387314DF ON bphs_indicator_reach (hf_code_id)');
    }
}
