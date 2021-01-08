<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181015194918 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE elo_rankingSystemChanges DROP FOREIGN KEY FK_60DF3052BF9F2E56');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE elo_rankingSystemChanges ADD CONSTRAINT FK_60DF3052BF9F2E56 FOREIGN KEY (hierarchy_entity_id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
