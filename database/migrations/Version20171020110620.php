<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171020110620 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC296CD8AE');
    $this->addSql('DROP TABLE teams');
    $this->addSql('DROP TABLE relation__team_players');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE teams (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', competition_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', rank INT NOT NULL, start_number INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_96C222587B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__team_players (team_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', player_id INT NOT NULL, INDEX IDX_F6FBF5DC296CD8AE (team_id), INDEX IDX_F6FBF5DC99E6F5DF (player_id), PRIMARY KEY(team_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE teams ADD CONSTRAINT FK_96C222587B39D312 FOREIGN KEY (competition_id) REFERENCES competitions (id)');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC99E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
