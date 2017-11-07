<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171107174636 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE phase (id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', competition_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', phase_number INT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, INDEX IDX_B1BDD6CB7B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE phase ADD CONSTRAINT FK_B1BDD6CB7B39D312 FOREIGN KEY (competition_id) REFERENCES competitions (id)');
    $this->addSql('DROP TABLE phases');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE phases (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', competition_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', phase_number INT NOT NULL, name VARCHAR(255) NOT NULL, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, INDEX IDX_170969E57B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE phases ADD CONSTRAINT FK_170969E57B39D312 FOREIGN KEY (competition_id) REFERENCES competitions (id)');
    $this->addSql('DROP TABLE phase');
  }
//</editor-fold desc="Public Methods">
}
