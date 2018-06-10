<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180610105147 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE relation__team_players (team_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', player_id INT NOT NULL, INDEX IDX_F6FBF5DC296CD8AE (team_id), INDEX IDX_F6FBF5DC99E6F5DF (player_id), PRIMARY KEY(team_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC99E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('DROP TABLE teamMemberships');
    $this->addSql('ALTER TABLE rankingSystemChanges DROP FOREIGN KEY FK_9673102299E6F5DF');
    $this->addSql('ALTER TABLE rankingSystemListEntry DROP FOREIGN KEY FK_E75C8E9199E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D999E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D645896399E6F5DF');
    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC99E6F5DF');
    $this->addSql('ALTER TABLE players MODIFY id INT NOT NULL');
    $this->addSql('ALTER TABLE players DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE players CHANGE id player_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY');
    $this->addSql('ALTER TABLE rankingSystemChanges ADD CONSTRAINT FK_9673102299E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE rankingSystemListEntry ADD CONSTRAINT FK_E75C8E9199E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D999E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D645896399E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems DROP FOREIGN KEY FK_750EA8FCE15D14EE');
    $this->addSql('DROP INDEX IDX_750EA8FCE15D14EE ON relation__hierarchy_entities_ranking_systems');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems CHANGE ranking_system_interface_id ranking_system_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems ADD CONSTRAINT FK_750EA8FCCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_750EA8FCCD8F5098 ON relation__hierarchy_entities_ranking_systems (ranking_system_id)');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems ADD PRIMARY KEY (tournament_hierarchy_entity_id, ranking_system_id)');
    $this->addSql('ALTER TABLE relation__match_rankingA DROP FOREIGN KEY FK_C8276767D27D031F');
    $this->addSql('DROP INDEX IDX_C8276767D27D031F ON relation__match_rankingA');
    $this->addSql('ALTER TABLE relation__match_rankingA DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__match_rankingA CHANGE ranking_interface_id ranking_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__match_rankingA ADD CONSTRAINT FK_C827676720F64684 FOREIGN KEY (ranking_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_C827676720F64684 ON relation__match_rankingA (ranking_id)');
    $this->addSql('ALTER TABLE relation__match_rankingA ADD PRIMARY KEY (match_id, ranking_id)');
    $this->addSql('ALTER TABLE relation__match_rankingB DROP FOREIGN KEY FK_512E36DDD27D031F');
    $this->addSql('DROP INDEX IDX_512E36DDD27D031F ON relation__match_rankingB');
    $this->addSql('ALTER TABLE relation__match_rankingB DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__match_rankingB CHANGE ranking_interface_id ranking_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__match_rankingB ADD CONSTRAINT FK_512E36DD20F64684 FOREIGN KEY (ranking_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_512E36DD20F64684 ON relation__match_rankingB (ranking_id)');
    $this->addSql('ALTER TABLE relation__match_rankingB ADD PRIMARY KEY (match_id, ranking_id)');
    $this->addSql('ALTER TABLE relation__ranking_teams DROP FOREIGN KEY FK_93448403A38DE440');
    $this->addSql('DROP INDEX IDX_93448403A38DE440 ON relation__ranking_teams');
    $this->addSql('ALTER TABLE relation__ranking_teams DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__ranking_teams CHANGE team_interface_id team_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__ranking_teams ADD CONSTRAINT FK_93448403296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_93448403296CD8AE ON relation__ranking_teams (team_id)');
    $this->addSql('ALTER TABLE relation__ranking_teams ADD PRIMARY KEY (ranking_id, team_id)');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC99E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC99E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D999E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D645896399E6F5DF');
    $this->addSql('ALTER TABLE rankingSystemChanges DROP FOREIGN KEY FK_9673102299E6F5DF');
    $this->addSql('ALTER TABLE rankingSystemListEntry DROP FOREIGN KEY FK_E75C8E9199E6F5DF');
    $this->addSql('ALTER TABLE players MODIFY player_id INT NOT NULL');
    $this->addSql('ALTER TABLE players DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE players CHANGE player_id id INT AUTO_INCREMENT NOT NULL PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC99E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D999E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D645896399E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('ALTER TABLE relation__ranking_teams DROP FOREIGN KEY FK_93448403296CD8AE');
    $this->addSql('DROP INDEX IDX_93448403296CD8AE ON relation__ranking_teams');
    $this->addSql('ALTER TABLE relation__ranking_teams DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__ranking_teams CHANGE team_id team_interface_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__ranking_teams ADD CONSTRAINT FK_93448403A38DE440 FOREIGN KEY (team_interface_id) REFERENCES teams (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_93448403A38DE440 ON relation__ranking_teams (team_interface_id)');
    $this->addSql('ALTER TABLE relation__ranking_teams ADD PRIMARY KEY (ranking_id, team_interface_id)');
    $this->addSql('ALTER TABLE relation__match_rankingA DROP FOREIGN KEY FK_C827676720F64684');
    $this->addSql('DROP INDEX IDX_C827676720F64684 ON relation__match_rankingA');
    $this->addSql('ALTER TABLE relation__match_rankingA DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__match_rankingA CHANGE ranking_id ranking_interface_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__match_rankingA ADD CONSTRAINT FK_C8276767D27D031F FOREIGN KEY (ranking_interface_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_C8276767D27D031F ON relation__match_rankingA (ranking_interface_id)');
    $this->addSql('ALTER TABLE relation__match_rankingA ADD PRIMARY KEY (match_id, ranking_interface_id)');
    $this->addSql('ALTER TABLE relation__match_rankingB DROP FOREIGN KEY FK_512E36DD20F64684');
    $this->addSql('DROP INDEX IDX_512E36DD20F64684 ON relation__match_rankingB');
    $this->addSql('ALTER TABLE relation__match_rankingB DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__match_rankingB CHANGE ranking_id ranking_interface_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__match_rankingB ADD CONSTRAINT FK_512E36DDD27D031F FOREIGN KEY (ranking_interface_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_512E36DDD27D031F ON relation__match_rankingB (ranking_interface_id)');
    $this->addSql('ALTER TABLE relation__match_rankingB ADD PRIMARY KEY (match_id, ranking_interface_id)');
    $this->addSql('ALTER TABLE rankingSystemListEntry ADD CONSTRAINT FK_E75C8E9199E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('ALTER TABLE rankingSystemChanges ADD CONSTRAINT FK_9673102299E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems DROP FOREIGN KEY FK_750EA8FCCD8F5098');
    $this->addSql('DROP INDEX IDX_750EA8FCCD8F5098 ON relation__hierarchy_entities_ranking_systems');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems DROP PRIMARY KEY');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems CHANGE ranking_system_id ranking_system_interface_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems ADD CONSTRAINT FK_750EA8FCE15D14EE FOREIGN KEY (ranking_system_interface_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('CREATE INDEX IDX_750EA8FCE15D14EE ON relation__hierarchy_entities_ranking_systems (ranking_system_interface_id)');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems ADD PRIMARY KEY (tournament_hierarchy_entity_id, ranking_system_interface_id)');
    $this->addSql('CREATE TABLE teamMemberships (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', team_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', player_id INT NOT NULL, INDEX IDX_5D544A1C296CD8AE (team_id), INDEX IDX_5D544A1C99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE teamMemberships ADD CONSTRAINT FK_5D544A1C296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
    $this->addSql('ALTER TABLE teamMemberships ADD CONSTRAINT FK_5D544A1C99E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
    $this->addSql('DROP TABLE relation__team_players');
  }
//</editor-fold desc="Public Methods">
}
