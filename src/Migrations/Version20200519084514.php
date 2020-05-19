<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519084514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_indicator_reach ADD hf_code_id INT DEFAULT NULL, ADD indicator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5387314DF FOREIGN KEY (hf_code_id) REFERENCES bphs_health_facility (id)');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB54402854A FOREIGN KEY (indicator_id) REFERENCES bphs_indicator (id)');
        $this->addSql('CREATE INDEX IDX_5938EB5387314DF ON bphs_indicator_reach (hf_code_id)');
        $this->addSql('CREATE INDEX IDX_5938EB54402854A ON bphs_indicator_reach (indicator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5387314DF');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB54402854A');
        $this->addSql('DROP INDEX IDX_5938EB5387314DF ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB54402854A ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP hf_code_id, DROP indicator_id');
    }
}
