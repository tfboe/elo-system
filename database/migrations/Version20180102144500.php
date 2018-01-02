<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180102144500 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE competitions DROP start_time, DROP end_time');
    $this->addSql('ALTER TABLE phases DROP start_time, DROP end_time');
    $this->addSql('ALTER TABLE tournaments DROP start_time, DROP end_time');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE tournaments ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL');
    $this->addSql('ALTER TABLE competitions ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL');
    $this->addSql('ALTER TABLE phases ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL');
  }
//</editor-fold desc="Public Methods">
}
