<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181013005042 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE elo_lastRecalculation (id INT AUTO_INCREMENT NOT NULL, version INT NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, start_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, end_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('DROP TABLE elo_recalculation');
    $this->addSql('ALTER TABLE elo_rankingSystemChanges CHANGE sub_class_data sub_class_data LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
    $this->addSql('ALTER TABLE elo_rankingSystemListEntry CHANGE sub_class_data sub_class_data LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
    $this->addSql('ALTER TABLE elo_rankingSystems CHANGE sub_class_data sub_class_data LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE elo_recalculation (id INT AUTO_INCREMENT NOT NULL, ranking_system_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', version INT NOT NULL, recalculate_from DATETIME DEFAULT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, start_timezone VARCHAR(255) NOT NULL, end_timezone VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4097E72ECD8F5098 (ranking_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE elo_recalculation ADD CONSTRAINT FK_4097E72ECD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES elo_rankingSystems (id)');
    $this->addSql('DROP TABLE elo_lastRecalculation');
    $this->addSql('ALTER TABLE elo_rankingSystems CHANGE sub_class_data sub_class_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE elo_rankingSystemListEntry CHANGE sub_class_data sub_class_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    $this->addSql('ALTER TABLE elo_rankingSystemChanges CHANGE sub_class_data sub_class_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
  }
//</editor-fold desc="Public Methods">
}
