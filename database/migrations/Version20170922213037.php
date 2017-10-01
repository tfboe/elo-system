<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170922213037 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE tournaments');
    $this->addSql('DROP TABLE players');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE tournaments (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', user_identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, tournament_list_id VARCHAR(255) NOT NULL, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, INDEX IDX_E4BCFAC361220EA6 (creator_id), INDEX user_id_idx (user_identifier, creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE players (id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birthday DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE tournaments ADD CONSTRAINT FK_E4BCFAC361220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
  }
//</editor-fold desc="Public Methods">
}
