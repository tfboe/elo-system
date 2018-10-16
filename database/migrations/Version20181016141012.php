<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181016141012 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP INDEX base_index ON elo_rankingSystemChanges');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE UNIQUE INDEX base_index ON elo_rankingSystemChanges (hierarchy_entity_id, ranking_system_id, player_id)');
  }
//</editor-fold desc="Public Methods">
}
