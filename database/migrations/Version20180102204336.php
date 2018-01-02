<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180102204336 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE competitions DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE games DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE matches DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE phases DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE tournaments DROP start_timezone, DROP end_timezone');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE tournaments ADD start_timezone VARCHAR(255) NOT NULL, ADD end_timezone VARCHAR(255) NOT NULL');
    $this->addSql('ALTER TABLE competitions ADD start_timezone VARCHAR(255) NOT NULL, ADD end_timezone VARCHAR(255) NOT NULL');
    $this->addSql('ALTER TABLE phases ADD start_timezone VARCHAR(255) NOT NULL, ADD end_timezone VARCHAR(255) NOT NULL');
    $this->addSql('ALTER TABLE games ADD start_timezone VARCHAR(255) NOT NULL, ADD end_timezone VARCHAR(255) NOT NULL');
    $this->addSql('ALTER TABLE matches ADD start_timezone VARCHAR(255) NOT NULL, ADD end_timezone VARCHAR(255) NOT NULL');
  }
//</editor-fold desc="Public Methods">
}
