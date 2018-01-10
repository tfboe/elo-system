<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180109231653 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC296CD8AE');
    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC99E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D6458963E48FD905');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D645896399E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D9E48FD905');
    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D999E6F5DF');
    $this->addSql('ALTER TABLE players MODIFY player_id INT NOT NULL');
    $this->addSql('ALTER TABLE players CHANGE player_id id INT AUTO_INCREMENT NOT NULL');
    $this->addSql('ALTER TABLE rankingSystems CHANGE generation_interval automatic_instance_generation INT NOT NULL');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D9E48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D999E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D6458963E48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D645896399E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC99E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE users CHANGE confirmed_a_g_b_version last_confirmed_a_g_b_version INT NOT NULL');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC296CD8AE');
    $this->addSql('ALTER TABLE relation__team_players DROP FOREIGN KEY FK_F6FBF5DC99E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D999E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D9E48FD905');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D645896399E6F5DF');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D6458963E48FD905');
    $this->addSql('ALTER TABLE players MODIFY id INT NOT NULL');
    $this->addSql('ALTER TABLE players CHANGE id player_id INT AUTO_INCREMENT NOT NULL');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
    $this->addSql('ALTER TABLE relation__team_players ADD CONSTRAINT FK_F6FBF5DC99E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE rankingSystems CHANGE automatic_instance_generation generation_interval INT NOT NULL');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D999E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D9E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D645896399E6F5DF FOREIGN KEY (player_id) REFERENCES players (player_id)');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D6458963E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
    $this->addSql('ALTER TABLE users CHANGE last_confirmed_a_g_b_version confirmed_a_g_b_version INT NOT NULL');
  }
//</editor-fold desc="Public Methods">
}
