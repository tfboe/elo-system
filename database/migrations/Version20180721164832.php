<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180721164832 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP INDEX itsf_license_number ON elo_players');
    $this->addSql('ALTER TABLE elo_players DROP itsf_license_number');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE elo_players ADD itsf_license_number INT DEFAULT NULL');
    $this->addSql('CREATE INDEX itsf_license_number ON elo_players (itsf_license_number)');
  }
//</editor-fold desc="Public Methods">
}
