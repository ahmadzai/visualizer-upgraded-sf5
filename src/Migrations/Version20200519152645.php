<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519152645 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_health_facility CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86BDAFD8C8');
        $this->addSql('DROP INDEX IDX_6D0E6A86BDAFD8C8 ON bphs_hf_indicator');
        $this->addSql('DROP INDEX UNIQ_6D0E6A865E15A06E ON bphs_hf_indicator');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD updated_by_id INT DEFAULT NULL, ADD deleted_by_id INT DEFAULT NULL, ADD slug VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP indicator_slug, DROP unique_slug, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE author created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6D0E6A86B03A8386 ON bphs_hf_indicator (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6D0E6A86896DBBDE ON bphs_hf_indicator (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_6D0E6A86C76F1F52 ON bphs_hf_indicator (deleted_by_id)');
        $this->addSql('ALTER TABLE bphs_indicator DROP FOREIGN KEY FK_3040341BBDAFD8C8');
        $this->addSql('DROP INDEX IDX_3040341BBDAFD8C8 ON bphs_indicator');
        $this->addSql('ALTER TABLE bphs_indicator ADD updated_by_id INT DEFAULT NULL, ADD deleted_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE author created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator ADD CONSTRAINT FK_3040341BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_indicator ADD CONSTRAINT FK_3040341B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_indicator ADD CONSTRAINT FK_3040341BC76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3040341BB03A8386 ON bphs_indicator (created_by_id)');
        $this->addSql('CREATE INDEX IDX_3040341B896DBBDE ON bphs_indicator (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_3040341BC76F1F52 ON bphs_indicator (deleted_by_id)');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5BDAFD8C8');
        $this->addSql('DROP INDEX UNIQ_5938EB5989D9B62 ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5BDAFD8C8 ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD updated_by_id INT DEFAULT NULL, ADD deleted_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE author created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5938EB5B03A8386 ON bphs_indicator_reach (created_by_id)');
        $this->addSql('CREATE INDEX IDX_5938EB5896DBBDE ON bphs_indicator_reach (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_5938EB5C76F1F52 ON bphs_indicator_reach (deleted_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_health_facility CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86B03A8386');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86896DBBDE');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86C76F1F52');
        $this->addSql('DROP INDEX IDX_6D0E6A86B03A8386 ON bphs_hf_indicator');
        $this->addSql('DROP INDEX IDX_6D0E6A86896DBBDE ON bphs_hf_indicator');
        $this->addSql('DROP INDEX IDX_6D0E6A86C76F1F52 ON bphs_hf_indicator');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD author INT DEFAULT NULL, ADD indicator_slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD unique_slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP created_by_id, DROP updated_by_id, DROP deleted_by_id, DROP slug, DROP updated_at, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86BDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6D0E6A86BDAFD8C8 ON bphs_hf_indicator (author)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D0E6A865E15A06E ON bphs_hf_indicator (unique_slug)');
        $this->addSql('ALTER TABLE bphs_indicator DROP FOREIGN KEY FK_3040341BB03A8386');
        $this->addSql('ALTER TABLE bphs_indicator DROP FOREIGN KEY FK_3040341B896DBBDE');
        $this->addSql('ALTER TABLE bphs_indicator DROP FOREIGN KEY FK_3040341BC76F1F52');
        $this->addSql('DROP INDEX IDX_3040341BB03A8386 ON bphs_indicator');
        $this->addSql('DROP INDEX IDX_3040341B896DBBDE ON bphs_indicator');
        $this->addSql('DROP INDEX IDX_3040341BC76F1F52 ON bphs_indicator');
        $this->addSql('ALTER TABLE bphs_indicator ADD author INT DEFAULT NULL, DROP created_by_id, DROP updated_by_id, DROP deleted_by_id, DROP updated_at, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator ADD CONSTRAINT FK_3040341BBDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3040341BBDAFD8C8 ON bphs_indicator (author)');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5B03A8386');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5896DBBDE');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB5C76F1F52');
        $this->addSql('DROP INDEX IDX_5938EB5B03A8386 ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5896DBBDE ON bphs_indicator_reach');
        $this->addSql('DROP INDEX IDX_5938EB5C76F1F52 ON bphs_indicator_reach');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD author INT DEFAULT NULL, DROP created_by_id, DROP updated_by_id, DROP deleted_by_id, DROP updated_at, CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5BDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5938EB5989D9B62 ON bphs_indicator_reach (slug)');
        $this->addSql('CREATE INDEX IDX_5938EB5BDAFD8C8 ON bphs_indicator_reach (author)');
    }
}
