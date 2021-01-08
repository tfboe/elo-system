<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180628141712 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE elo_lastRecalculation');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE elo_lastRecalculation (id INT AUTO_INCREMENT NOT NULL, version INT NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, start_timezone VARCHAR(255) NOT NULL DEFAULT "", end_timezone VARCHAR(255) NOT NULL DEFAULT "", PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('INSERT INTO elo_lastRecalculation (version) VALUES (0)');
  }
//</editor-fold desc="Public Methods">
}
