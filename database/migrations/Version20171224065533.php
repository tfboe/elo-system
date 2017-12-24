<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171224065533 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__game_playersA DROP FOREIGN KEY FK_4F4CD8D9E48FD905');
    $this->addSql('ALTER TABLE relation__game_playersB DROP FOREIGN KEY FK_D6458963E48FD905');
    $this->addSql('DROP TABLE games');
    $this->addSql('DROP TABLE relation__game_playersA');
    $this->addSql('DROP TABLE relation__game_playersB');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE games (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', match_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, game_number INT NOT NULL, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, result_a INT NOT NULL, result_b INT NOT NULL, result INT NOT NULL, played TINYINT(1) NOT NULL, INDEX IDX_FF232B312ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__game_playersA (game_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', player_id INT NOT NULL, INDEX IDX_4F4CD8D9E48FD905 (game_id), INDEX IDX_4F4CD8D999E6F5DF (player_id), PRIMARY KEY(game_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__game_playersB (game_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', player_id INT NOT NULL, INDEX IDX_D6458963E48FD905 (game_id), INDEX IDX_D645896399E6F5DF (player_id), PRIMARY KEY(game_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B312ABEACD6 FOREIGN KEY (match_id) REFERENCES matches (id)');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D9E48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_playersA ADD CONSTRAINT FK_4F4CD8D999E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D6458963E48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_playersB ADD CONSTRAINT FK_D645896399E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
