<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171203143103 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__ranking_teams DROP FOREIGN KEY FK_9344840320F64684');
    $this->addSql('DROP TABLE rankings');
    $this->addSql('DROP TABLE relation__ranking_teams');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE rankings (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', rank INT NOT NULL, unique_rank INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_9D5DA5E6FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__ranking_teams (ranking_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', team_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_9344840320F64684 (ranking_id), INDEX IDX_93448403296CD8AE (team_id), PRIMARY KEY(ranking_id, team_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE rankings ADD CONSTRAINT FK_9D5DA5E6FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)');
    $this->addSql('ALTER TABLE relation__ranking_teams ADD CONSTRAINT FK_9344840320F64684 FOREIGN KEY (ranking_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__ranking_teams ADD CONSTRAINT FK_93448403296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
