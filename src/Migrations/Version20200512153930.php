<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200512153930 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_connect (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, api_service_name VARCHAR(255) DEFAULT NULL, api_service_url VARCHAR(255) DEFAULT NULL, api_key VARCHAR(255) DEFAULT NULL, api_user VARCHAR(255) DEFAULT NULL, api_pass VARCHAR(255) DEFAULT NULL, api_login_type VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_99F18D788D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bphs_health_facility (id INT NOT NULL, district INT DEFAULT NULL, author INT DEFAULT NULL, facility_name VARCHAR(255) DEFAULT NULL, facility_slug VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3C790A24B432C190 (facility_slug), INDEX IDX_3C790A2431C15487 (district), INDEX IDX_3C790A24BDAFD8C8 (author), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bphs_hf_indicator (id INT AUTO_INCREMENT NOT NULL, health_facility INT DEFAULT NULL, indicator INT DEFAULT NULL, author INT DEFAULT NULL, annual_target INT DEFAULT NULL, monthly_target INT DEFAULT NULL, target_year INT DEFAULT NULL, indicator_slug VARCHAR(255) NOT NULL, unique_slug VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6D0E6A865E15A06E (unique_slug), INDEX IDX_6D0E6A86F87CE91C (health_facility), INDEX IDX_6D0E6A86D1349DB3 (indicator), INDEX IDX_6D0E6A86BDAFD8C8 (author), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bphs_indicator (id INT AUTO_INCREMENT NOT NULL, author INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, short_name VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3040341B3EE4B093 (short_name), INDEX IDX_3040341BBDAFD8C8 (author), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bphs_indicator_reach (id INT AUTO_INCREMENT NOT NULL, hf_indicator INT DEFAULT NULL, author INT DEFAULT NULL, reach INT DEFAULT NULL, report_month VARCHAR(3) DEFAULT NULL, report_year INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5938EB5989D9B62 (slug), INDEX IDX_5938EB592C8D43A (hf_indicator), INDEX IDX_5938EB5BDAFD8C8 (author), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, campaign_sort_no INT DEFAULT NULL, campaign_name TEXT DEFAULT NULL, campaign_type TEXT DEFAULT NULL, campaign_start_date TEXT DEFAULT NULL, campaign_end_date TEXT DEFAULT NULL, entry_date DATETIME DEFAULT NULL, campaign_year INT DEFAULT NULL, campaign_month VARCHAR(20) DEFAULT NULL, INDEX IDX_1F1512DD8D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE catchup_data (id INT AUTO_INCREMENT NOT NULL, campaign INT DEFAULT NULL, district INT DEFAULT NULL, user INT DEFAULT NULL, data_source TINYTEXT DEFAULT NULL, cluster_name TINYTEXT DEFAULT NULL, cluster_no TINYTEXT DEFAULT NULL, sub_district TINYTEXT DEFAULT NULL, area_name TINYTEXT DEFAULT NULL, no_sm INT DEFAULT NULL, no_hh INT DEFAULT NULL, no_u5 INT DEFAULT NULL, reg_absent INT DEFAULT NULL, vac_absent INT DEFAULT NULL, reg_nss INT DEFAULT NULL, vac_nss INT DEFAULT NULL, reg_refusal INT DEFAULT NULL, vac_refusal INT DEFAULT NULL, un_recorded INT DEFAULT NULL, vac_un_recorded INT DEFAULT NULL, vac_guest INT DEFAULT NULL, INDEX IDX_AF8794CA8D93D649 (user), INDEX campaign_catchup_idx (campaign), INDEX district_catchup_idx (district), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coverage_data (id INT AUTO_INCREMENT NOT NULL, campaign INT DEFAULT NULL, district INT DEFAULT NULL, user INT DEFAULT NULL, sub_district TINYTEXT DEFAULT NULL, cluster_no TINYTEXT DEFAULT NULL, cluster_name TINYTEXT DEFAULT NULL, no_teams INT DEFAULT NULL, no_houses INT DEFAULT NULL, target_children INT DEFAULT NULL, vials_received INT DEFAULT NULL, vials_used INT DEFAULT NULL, no_child_vac_by_tt INT DEFAULT NULL, no_houses_visited INT DEFAULT NULL, no_resident_children INT DEFAULT NULL, no_guest_children INT DEFAULT NULL, no_child_in_house_vac INT DEFAULT NULL, no_child_outside_vac INT DEFAULT NULL, no_vac_nomad INT DEFAULT NULL, no_absent_same_day INT DEFAULT NULL, no_absent_same_day_found_vac INT DEFAULT NULL, no_absent_same_day_vac_by_team INT DEFAULT NULL, no_absent_not_same_day INT DEFAULT NULL, no_absent_not_same_day_found_vac INT DEFAULT NULL, no_absent_not_same_day_vac_by_team INT DEFAULT NULL, no_nss INT DEFAULT NULL, no_nss_found_vac INT DEFAULT NULL, no_nss_vac_by_team INT DEFAULT NULL, no_refusal INT DEFAULT NULL, no_refusal_found_vac INT DEFAULT NULL, no_refusal_vac_by_team INT DEFAULT NULL, afp_case INT DEFAULT NULL, vac_day INT DEFAULT NULL, tally_type TINYTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_251A877E1F1512DD (campaign), INDEX IDX_251A877E31C15487 (district), INDEX IDX_251A877E8D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE covid19_cases (id INT AUTO_INCREMENT NOT NULL, province INT DEFAULT NULL, no_of_confirmed_cases INT DEFAULT NULL, no_of_suspected_cases INT DEFAULT NULL, no_of_recovered_cases INT DEFAULT NULL, no_of_deaths INT DEFAULT NULL, last_updated DATETIME NOT NULL, INDEX IDX_8F1C991F4ADAD40B (province), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE district (id INT AUTO_INCREMENT NOT NULL, province INT DEFAULT NULL, district_name TEXT DEFAULT NULL, district_name_alt TEXT DEFAULT NULL, district_name_pashtu TEXT DEFAULT NULL, district_name_dari TEXT DEFAULT NULL, district_lpd_status TEXT DEFAULT NULL, district_risk_status VARCHAR(5) DEFAULT NULL, district_icn_status VARCHAR(20) DEFAULT NULL, entry_date DATETIME DEFAULT NULL, INDEX fk_d_p_idx (province), UNIQUE INDEX district_code_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heatmap_benchmark (id INT AUTO_INCREMENT NOT NULL, min_value DOUBLE PRECISION NOT NULL, max_value DOUBLE PRECISION NOT NULL, mid_stop DOUBLE PRECISION NOT NULL, min_color VARCHAR(255) NOT NULL, max_color VARCHAR(255) NOT NULL, mid_color VARCHAR(255) NOT NULL, data_source VARCHAR(255) NOT NULL, indicator VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE imported_files (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, file_size INT NOT NULL, updated_at DATETIME NOT NULL, data_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE polio_cases (id INT AUTO_INCREMENT NOT NULL, district INT DEFAULT NULL, user INT DEFAULT NULL, epid VARCHAR(50) DEFAULT NULL, onset_date DATE DEFAULT NULL, sex VARCHAR(2) DEFAULT NULL, age_in_months INT DEFAULT NULL, no_routine_doses INT DEFAULT NULL, no_sia_doses INT DEFAULT NULL, last_opv_date DATE DEFAULT NULL, stool_date DATE DEFAULT NULL, cluster VARCHAR(50) DEFAULT NULL, linkage VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, INDEX IDX_9AA5077A31C15487 (district), INDEX IDX_9AA5077A8D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, province_region VARCHAR(10) DEFAULT NULL, province_name VARCHAR(30) DEFAULT NULL, province_name_pashtu VARCHAR(45) DEFAULT NULL, province_name_dari VARCHAR(45) DEFAULT NULL, entry_date DATETIME DEFAULT NULL, UNIQUE INDEX province_code_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refusal_comm (id INT AUTO_INCREMENT NOT NULL, campaign INT DEFAULT NULL, district INT DEFAULT NULL, user INT DEFAULT NULL, cluster_no TINYTEXT DEFAULT NULL, sub_district TINYTEXT DEFAULT NULL, reg_refusal INT DEFAULT NULL, refusal_vac_in_catchup INT DEFAULT NULL, refusal_vac_by_crc INT DEFAULT NULL, refusal_vac_by_rc INT DEFAULT NULL, refusal_vac_by_cip INT DEFAULT NULL, refusal_vac_by_senior_staff INT DEFAULT NULL, campaign_phase VARCHAR(100) DEFAULT NULL, INDEX IDX_B7C98B351F1512DD (campaign), INDEX IDX_B7C98B3531C15487 (district), INDEX IDX_B7C98B358D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE staff_icn (id INT AUTO_INCREMENT NOT NULL, district INT DEFAULT NULL, no_of_dco INT DEFAULT NULL, no_of_female_dco INT DEFAULT NULL, no_of_ccs INT DEFAULT NULL, no_of_female_ccs INT DEFAULT NULL, no_of_sm INT DEFAULT NULL, no_of_female_sm INT DEFAULT NULL, no_of_fmv INT DEFAULT NULL, no_of_ext INT DEFAULT NULL, no_of_female_ext INT DEFAULT NULL, as_of_month VARCHAR(50) DEFAULT NULL, as_of_year INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D291C64F31C15487 (district), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE staff_pco (id INT AUTO_INCREMENT NOT NULL, province INT DEFAULT NULL, no_of_pco INT DEFAULT NULL, no_of_female_pco INT DEFAULT NULL, as_of_month VARCHAR(50) DEFAULT NULL, as_of_year INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6616E264ADAD40B (province), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tables_manager (id INT AUTO_INCREMENT NOT NULL, table_name VARCHAR(100) NOT NULL, table_long_name VARCHAR(100) NOT NULL, table_type VARCHAR(100) NOT NULL, source VARCHAR(100) DEFAULT NULL, dashboard TINYINT(1) NOT NULL, upload_form TINYINT(1) NOT NULL, entry_form TINYINT(1) NOT NULL, download_form TINYINT(1) NOT NULL, data_level VARCHAR(100) NOT NULL, sort_no INT NOT NULL, enabled TINYINT(1) NOT NULL, entry_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temp_catchup_data (id INT AUTO_INCREMENT NOT NULL, data_source TINYTEXT DEFAULT NULL, cluster_name TINYTEXT DEFAULT NULL, cluster_no TINYTEXT DEFAULT NULL, sub_district TINYTEXT DEFAULT NULL, area_name TINYTEXT DEFAULT NULL, no_sm INT DEFAULT NULL, no_hh INT DEFAULT NULL, no_u5 INT DEFAULT NULL, reg_absent INT DEFAULT NULL, vac_absent INT DEFAULT NULL, reg_nss INT DEFAULT NULL, vac_nss INT DEFAULT NULL, reg_refusal INT DEFAULT NULL, vac_refusal INT DEFAULT NULL, un_recorded INT DEFAULT NULL, vac_un_recorded INT DEFAULT NULL, vac_guest INT DEFAULT NULL, campaign INT DEFAULT NULL, district INT DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temp_coverage_data (id INT AUTO_INCREMENT NOT NULL, district INT DEFAULT NULL, sub_district TINYTEXT DEFAULT NULL, cluster_no TINYTEXT DEFAULT NULL, cluster_name TINYTEXT DEFAULT NULL, no_teams INT DEFAULT NULL, no_houses INT DEFAULT NULL, target_children INT DEFAULT NULL, vials_received INT DEFAULT NULL, vials_used INT DEFAULT NULL, no_child_vac_by_tt INT DEFAULT NULL, no_houses_visited INT DEFAULT NULL, no_resident_children INT DEFAULT NULL, no_guest_children INT DEFAULT NULL, no_child_in_house_vac INT DEFAULT NULL, no_child_outside_vac INT DEFAULT NULL, no_vac_nomad INT DEFAULT NULL, no_absent_same_day INT DEFAULT NULL, no_absent_same_day_found_vac INT DEFAULT NULL, no_absent_same_day_vac_by_team INT DEFAULT NULL, no_absent_not_same_day INT DEFAULT NULL, no_absent_not_same_day_found_vac INT DEFAULT NULL, no_absent_not_same_day_vac_by_team INT DEFAULT NULL, no_nss INT DEFAULT NULL, no_nss_found_vac INT DEFAULT NULL, no_nss_vac_by_team INT DEFAULT NULL, no_refusal INT DEFAULT NULL, no_refusal_found_vac INT DEFAULT NULL, no_refusal_vac_by_team INT DEFAULT NULL, afp_case INT DEFAULT NULL, vac_day INT DEFAULT NULL, tally_type TINYTEXT DEFAULT NULL, campaign INT DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temp_icm_data (data_id INT AUTO_INCREMENT NOT NULL, district_code INT DEFAULT NULL, no_team_monitored INT DEFAULT NULL, team_resident_area INT DEFAULT NULL, vaccinator_trained INT DEFAULT NULL, vacc_stage_3 INT DEFAULT NULL, team_supervised INT DEFAULT NULL, team_with_chw INT DEFAULT NULL, team_with_female INT DEFAULT NULL, team_accom_sm INT DEFAULT NULL, no_missed_no_team_visit INT DEFAULT NULL, no_child_seen INT DEFAULT NULL, no_child_with_fm INT DEFAULT NULL, no_missed_child INT DEFAULT NULL, no_missed_10 INT DEFAULT NULL, campaign_id INT DEFAULT NULL, PRIMARY KEY(data_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload_manager (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, table_name VARCHAR(255) DEFAULT NULL, excluded_columns LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', entity_columns LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', unique_columns LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', enabled TINYINT(1) DEFAULT NULL, has_temp TINYINT(1) DEFAULT NULL, modified_at DATETIME NOT NULL, INDEX IDX_3F133F578D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_connect ADD CONSTRAINT FK_99F18D788D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bphs_health_facility ADD CONSTRAINT FK_3C790A2431C15487 FOREIGN KEY (district) REFERENCES district (id)');
        $this->addSql('ALTER TABLE bphs_health_facility ADD CONSTRAINT FK_3C790A24BDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86F87CE91C FOREIGN KEY (health_facility) REFERENCES bphs_health_facility (id)');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86D1349DB3 FOREIGN KEY (indicator) REFERENCES bphs_indicator (id)');
        $this->addSql('ALTER TABLE bphs_hf_indicator ADD CONSTRAINT FK_6D0E6A86BDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bphs_indicator ADD CONSTRAINT FK_3040341BBDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB592C8D43A FOREIGN KEY (hf_indicator) REFERENCES bphs_hf_indicator (id)');
        $this->addSql('ALTER TABLE bphs_indicator_reach ADD CONSTRAINT FK_5938EB5BDAFD8C8 FOREIGN KEY (author) REFERENCES user (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DD8D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE catchup_data ADD CONSTRAINT FK_AF8794CA1F1512DD FOREIGN KEY (campaign) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE catchup_data ADD CONSTRAINT FK_AF8794CA31C15487 FOREIGN KEY (district) REFERENCES district (id)');
        $this->addSql('ALTER TABLE catchup_data ADD CONSTRAINT FK_AF8794CA8D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE coverage_data ADD CONSTRAINT FK_251A877E1F1512DD FOREIGN KEY (campaign) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE coverage_data ADD CONSTRAINT FK_251A877E31C15487 FOREIGN KEY (district) REFERENCES district (id)');
        $this->addSql('ALTER TABLE coverage_data ADD CONSTRAINT FK_251A877E8D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE covid19_cases ADD CONSTRAINT FK_8F1C991F4ADAD40B FOREIGN KEY (province) REFERENCES province (id)');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C154874ADAD40B FOREIGN KEY (province) REFERENCES province (id)');
        $this->addSql('ALTER TABLE polio_cases ADD CONSTRAINT FK_9AA5077A31C15487 FOREIGN KEY (district) REFERENCES district (id)');
        $this->addSql('ALTER TABLE polio_cases ADD CONSTRAINT FK_9AA5077A8D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE refusal_comm ADD CONSTRAINT FK_B7C98B351F1512DD FOREIGN KEY (campaign) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE refusal_comm ADD CONSTRAINT FK_B7C98B3531C15487 FOREIGN KEY (district) REFERENCES district (id)');
        $this->addSql('ALTER TABLE refusal_comm ADD CONSTRAINT FK_B7C98B358D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE staff_icn ADD CONSTRAINT FK_D291C64F31C15487 FOREIGN KEY (district) REFERENCES district (id)');
        $this->addSql('ALTER TABLE staff_pco ADD CONSTRAINT FK_B6616E264ADAD40B FOREIGN KEY (province) REFERENCES province (id)');
        $this->addSql('ALTER TABLE upload_manager ADD CONSTRAINT FK_3F133F578D93D649 FOREIGN KEY (user) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86F87CE91C');
        $this->addSql('ALTER TABLE bphs_indicator_reach DROP FOREIGN KEY FK_5938EB592C8D43A');
        $this->addSql('ALTER TABLE bphs_hf_indicator DROP FOREIGN KEY FK_6D0E6A86D1349DB3');
        $this->addSql('ALTER TABLE catchup_data DROP FOREIGN KEY FK_AF8794CA1F1512DD');
        $this->addSql('ALTER TABLE coverage_data DROP FOREIGN KEY FK_251A877E1F1512DD');
        $this->addSql('ALTER TABLE refusal_comm DROP FOREIGN KEY FK_B7C98B351F1512DD');
        $this->addSql('ALTER TABLE bphs_health_facility DROP FOREIGN KEY FK_3C790A2431C15487');
        $this->addSql('ALTER TABLE catchup_data DROP FOREIGN KEY FK_AF8794CA31C15487');
        $this->addSql('ALTER TABLE coverage_data DROP FOREIGN KEY FK_251A877E31C15487');
        $this->addSql('ALTER TABLE polio_cases DROP FOREIGN KEY FK_9AA5077A31C15487');
        $this->addSql('ALTER TABLE refusal_comm DROP FOREIGN KEY FK_B7C98B3531C15487');
        $this->addSql('ALTER TABLE staff_icn DROP FOREIGN KEY FK_D291C64F31C15487');
        $this->addSql('ALTER TABLE covid19_cases DROP FOREIGN KEY FK_8F1C991F4ADAD40B');
        $this->addSql('ALTER TABLE district DROP FOREIGN KEY FK_31C154874ADAD40B');
        $this->addSql('ALTER TABLE staff_pco DROP FOREIGN KEY FK_B6616E264ADAD40B');
        $this->addSql('DROP TABLE api_connect');
        $this->addSql('DROP TABLE bphs_health_facility');
        $this->addSql('DROP TABLE bphs_hf_indicator');
        $this->addSql('DROP TABLE bphs_indicator');
        $this->addSql('DROP TABLE bphs_indicator_reach');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE catchup_data');
        $this->addSql('DROP TABLE coverage_data');
        $this->addSql('DROP TABLE covid19_cases');
        $this->addSql('DROP TABLE district');
        $this->addSql('DROP TABLE heatmap_benchmark');
        $this->addSql('DROP TABLE imported_files');
        $this->addSql('DROP TABLE polio_cases');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE refusal_comm');
        $this->addSql('DROP TABLE staff_icn');
        $this->addSql('DROP TABLE staff_pco');
        $this->addSql('DROP TABLE tables_manager');
        $this->addSql('DROP TABLE temp_catchup_data');
        $this->addSql('DROP TABLE temp_coverage_data');
        $this->addSql('DROP TABLE temp_icm_data');
        $this->addSql('DROP TABLE upload_manager');
    }
}
