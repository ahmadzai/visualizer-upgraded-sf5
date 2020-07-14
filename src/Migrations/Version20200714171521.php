<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200714171521 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5896DBBDE');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5B03A8386');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5C76F1F52');
        $this->addSql('DROP INDEX IDX_5938EB5896DBBDE ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5B03A8386 ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5C76F1F52 ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP created_by_id, DROP updated_by_id, DROP deleted_by_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD deleted_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5938EB5896DBBDE ON bphs_indicator_reach (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_5938EB5B03A8386 ON bphs_indicator_reach (created_by_id)');
        $this->addSql('CREATE INDEX IDX_5938EB5C76F1F52 ON bphs_indicator_reach (deleted_by_id)');
    }
}
