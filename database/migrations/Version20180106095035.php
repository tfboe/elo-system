<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180106095035 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE rankingSystemLists');
    $this->addSql('ALTER TABLE rankingSystems DROP open_sync_from');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE rankingSystemLists (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', current TINYINT(1) NOT NULL, last_entry_time DATETIME NOT NULL, INDEX IDX_38AC5A8DCD8F5098 (ranking_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE rankingSystemLists ADD CONSTRAINT FK_38AC5A8DCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id)');
    $this->addSql('ALTER TABLE rankingSystems ADD open_sync_from DATETIME DEFAULT NULL');
  }
//</editor-fold desc="Public Methods">
}
