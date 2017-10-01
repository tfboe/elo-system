<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171001195701 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE player_tournament');
    $this->addSql('ALTER TABLE players CHANGE id id INT NOT NULL');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE player_tournament (tournament_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', player_id INT NOT NULL, INDEX IDX_E2FA3CE433D1A3E7 (tournament_id), INDEX IDX_E2FA3CE499E6F5DF (player_id), PRIMARY KEY(tournament_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE players CHANGE id id INT AUTO_INCREMENT NOT NULL');
    $this->addSql('ALTER TABLE player_tournament ADD CONSTRAINT FK_E2FA3CE433D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE player_tournament ADD CONSTRAINT FK_E2FA3CE499E6F5DF FOREIGN KEY (player_id) REFERENCES players (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
