<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180617172504 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE elo_players DROP FOREIGN KEY FK_35215CAF285E57BA');
    $this->addSql('DROP INDEX IDX_35215CAF285E57BA ON elo_players');
    $this->addSql('ALTER TABLE elo_players DROP merged_into_id');
    $this->addSql('ALTER TABLE elo_users DROP admin');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE elo_players ADD merged_into_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE elo_players ADD CONSTRAINT FK_35215CAF285E57BA FOREIGN KEY (merged_into_id) REFERENCES elo_players (id)');
    $this->addSql('CREATE INDEX IDX_35215CAF285E57BA ON elo_players (merged_into_id)');
    $this->addSql('ALTER TABLE elo_users ADD admin TINYINT(1) NOT NULL');
  }
//</editor-fold desc="Public Methods">
}
