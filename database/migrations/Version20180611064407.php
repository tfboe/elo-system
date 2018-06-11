<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180611064407 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('RENAME TABLE elo_competitions TO competitions');
    $this->addSql('RENAME TABLE elo_games TO games');
    $this->addSql('RENAME TABLE elo_matches TO matches');
    $this->addSql('RENAME TABLE elo_phases TO phases');
    $this->addSql('RENAME TABLE elo_players TO players');
    $this->addSql('RENAME TABLE elo_qualificationSystems TO qualificationSystems');
    $this->addSql('RENAME TABLE elo_rankingSystemChanges TO rankingSystemChanges');
    $this->addSql('RENAME TABLE elo_rankingSystemListEntry TO rankingSystemListEntry');
    $this->addSql('RENAME TABLE elo_rankingSystemLists TO rankingSystemLists');
    $this->addSql('RENAME TABLE elo_rankingSystems TO rankingSystems');
    $this->addSql('RENAME TABLE elo_rankings TO rankings');
    $this->addSql('RENAME TABLE elo_teamMemberships TO teamMemberships');
    $this->addSql('RENAME TABLE elo_teams TO teams');
    $this->addSql('RENAME TABLE elo_tournaments TO tournaments');
    $this->addSql('RENAME TABLE elo_users TO users');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('RENAME TABLE competitions TO elo_competitions');
    $this->addSql('RENAME TABLE games TO elo_games');
    $this->addSql('RENAME TABLE matches TO elo_matches');
    $this->addSql('RENAME TABLE phases TO elo_phases');
    $this->addSql('RENAME TABLE players TO elo_players');
    $this->addSql('RENAME TABLE qualificationSystems TO elo_qualificationSystems');
    $this->addSql('RENAME TABLE rankingSystemChanges TO elo_rankingSystemChanges');
    $this->addSql('RENAME TABLE rankingSystemListEntry TO elo_rankingSystemListEntry');
    $this->addSql('RENAME TABLE rankingSystemLists TO elo_rankingSystemLists');
    $this->addSql('RENAME TABLE rankingSystems TO elo_rankingSystems');
    $this->addSql('RENAME TABLE rankings TO elo_rankings');
    $this->addSql('RENAME TABLE teamMemberships TO elo_teamMemberships');
    $this->addSql('RENAME TABLE teams TO elo_teams');
    $this->addSql('RENAME TABLE tournaments TO elo_tournaments');
    $this->addSql('RENAME TABLE users TO elo_users');
  }
//</editor-fold desc="Public Methods">
}
