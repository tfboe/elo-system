<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180123222206 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE rankingSystemListEntry');
    $this->addSql('DROP TABLE rankingSystemChanges');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE rankingSystemListEntry (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_list_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', player_id INT DEFAULT NULL, points DOUBLE PRECISION NOT NULL, number_ranked_entities INT NOT NULL, sub_class_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_E75C8E9155EDEC5F (ranking_system_list_id), INDEX IDX_E75C8E9199E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE rankingSystemChanges (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', player_id INT DEFAULT NULL, hierarchy_entity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', points_change DOUBLE PRECISION NOT NULL, points_afterwards DOUBLE PRECISION NOT NULL, sub_class_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_96731022CD8F5098 (ranking_system_id), INDEX IDX_9673102299E6F5DF (player_id), INDEX IDX_96731022BF9F2E56 (hierarchy_entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE rankingSystemListEntry ADD CONSTRAINT FK_E75C8E9155EDEC5F FOREIGN KEY (ranking_system_list_id) REFERENCES rankingSystemLists (id)');
    $this->addSql('ALTER TABLE rankingSystemListEntry ADD CONSTRAINT FK_E75C8E9199E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE rankingSystemChanges ADD CONSTRAINT FK_96731022CD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id)');
    $this->addSql('ALTER TABLE rankingSystemChanges ADD CONSTRAINT FK_9673102299E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE rankingSystemChanges ADD CONSTRAINT FK_96731022BF9F2E56 FOREIGN KEY (hierarchy_entity_id) REFERENCES tournament_hierarchy_entities (id)');
  }
//</editor-fold desc="Public Methods">
}
