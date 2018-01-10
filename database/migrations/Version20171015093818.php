<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171015093818 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP INDEX unique_name_idx ON competitions');
    $this->addSql('DROP TABLE competitions');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE competitions (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', tournament_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, INDEX IDX_A7DD463D33D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE competitions ADD CONSTRAINT FK_A7DD463D33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id)');
    $this->addSql('CREATE INDEX unique_name_idx ON competitions (tournament_id, name)');
  }
//</editor-fold desc="Public Methods">
}
