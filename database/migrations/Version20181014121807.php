<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181014121807 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE elo_asyncRequests');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE elo_asyncRequests (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', result LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', input LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', service_name VARCHAR(255) NOT NULL, progress DOUBLE PRECISION NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, start_timezone VARCHAR(255) NOT NULL, end_timezone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
  }
//</editor-fold desc="Public Methods">
}
