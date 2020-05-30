<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519142859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_health_facility DROP FOREIGN KEY FK_3C790A24BDAFD8C8');
        $this->addSql('DROP INDEX UNIQ_3C790A24B432C190 ON bphs_health_facility');
        $this->addSql('DROP INDEX IDX_3C790A24BDAFD8C8 ON bphs_health_facility');
        $this->addSql('ALTER TABLE bphs_health_facility ADD updated_by_id INT DEFAULT NULL, ADD deleted_by_id INT DEFAULT NULL, ADD slug VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP facility_slug, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE author created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_health_facility ADD CONSTRAINT FK_3C790A24B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_health_facility ADD CONSTRAINT FK_3C790A24896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bphs_health_facility ADD CONSTRAINT FK_3C790A24C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3C790A24B03A8386 ON bphs_health_facility (created_by_id)');
        $this->addSql('CREATE INDEX IDX_3C790A24896DBBDE ON bphs_health_facility (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_3C790A24C76F1F52 ON bphs_health_facility (deleted_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_health_facility DROP FOREIGN KEY FK_3C790A24B03A8386');
        $this->addSql('ALTER TABLE bphs_health_facility DROP FOREIGN KEY FK_3C790A24896DBBDE');
        $this->addSql('ALTER TABLE bphs_health_facility DROP FOREIGN KEY FK_3C790A24C76F1F52');
        $this->addSql('DROP INDEX IDX_3C790A24B03A8386 ON bphs_health_facility');
        $this->addSql('DROP INDEX IDX_3C790A24896DBBDE ON bphs_health_facility');
        $this->addSql('DROP INDEX IDX_3C790A24C76F1F52 ON bphs_health_facility');
        $this->addSql('ALTER TABLE bphs_health_facility ADD author INT DEFAULT NULL, ADD facility_slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP created_by_id, DROP updated_by_id, DROP deleted_by_id, DROP slug, DROP updated_at, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE bphs_health_facility ADD CONSTRAINT FK_3C790A24BDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C790A24B432C190 ON bphs_health_facility (facility_slug)');
        $this->addSql('CREATE INDEX IDX_3C790A24BDAFD8C8 ON bphs_health_facility (author)');
    }
}
