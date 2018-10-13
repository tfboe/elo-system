<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181013013745 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE tournamentHierarchyEntityRankingTimes');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE tournamentHierarchyEntityRankingTimes (id INT AUTO_INCREMENT NOT NULL, hierarchy_entity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', ranking_time DATETIME NOT NULL, INDEX IDX_306A7FCFBF9F2E56 (hierarchy_entity_id), INDEX IDX_306A7FCFCD8F5098 (ranking_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE tournamentHierarchyEntityRankingTimes ADD CONSTRAINT FK_306A7FCFBF9F2E56 FOREIGN KEY (hierarchy_entity_id) REFERENCES tournament_hierarchy_entities (id)');
    $this->addSql('ALTER TABLE tournamentHierarchyEntityRankingTimes ADD CONSTRAINT FK_306A7FCFCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES elo_rankingSystems (id)');
  }
//</editor-fold desc="Public Methods">
}
